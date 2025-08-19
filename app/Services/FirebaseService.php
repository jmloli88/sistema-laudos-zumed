<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;

class FirebaseService
{
    private $projectId;
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        //$this->storageBucket = config('firebase.storage_bucket');
        $this->projectId = config('firebase.project_id');
        $this->apiKey = config('firebase.api_key');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
        $this->firebase = (new Factory)
            ->withServiceAccount(base_path('storage/app/firebase/credenciales.json'));

        // Inicializar los servicios que necesites
        $this->firebaseAuth = $this->firebase->createAuth(); // Servicio de autenticación
        $this->firebaseStorage = $this->firebase->createStorage(); // Servicio

        $this->storageBucket = config('firebase.storage_bucket');
    }

    private function getHttpClient()
    {
        return Http::withOptions([
            'verify' => false // Deshabilita la verificación SSL
        ]);
    }

    public function validateUser($username, $password)
    {
        $query = [
            "structuredQuery" => [
                "from" => [["collectionId" => "USUARIOS"]],
                "where" => [
                    "compositeFilter" => [
                        "op" => "AND",
                        "filters" => [
                            [
                                "fieldFilter" => [
                                    "field" => ["fieldPath" => "USUARIO"],
                                    "op" => "EQUAL",
                                    "value" => ["stringValue" => $username]
                                ]
                            ],
                            [
                                "fieldFilter" => [
                                    "field" => ["fieldPath" => "PASSWORD"],
                                    "op" => "EQUAL",
                                    "value" => ["stringValue" => $password]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->getHttpClient()
            ->post("{$this->baseUrl}:runQuery?key={$this->apiKey}", $query);

        if ($response->successful() && !empty($response[0]['document'])) {
            return $this->parseFirestoreDocument($response[0]['document']);
        }

        return null;
    }

    public function getLaudos($idClinica)
    {
        $query = [
            "structuredQuery" => [
                "from" => [["collectionId" => "LAUDOS"]],
                "where" => [
                    "fieldFilter" => [
                        "field" => ["fieldPath" => "ID_CLINICA"],
                        "op" => "EQUAL",
                        "value" => ["stringValue" => $idClinica]
                    ]
                ],
                "select" => [
                    "fields" => [
                        ["fieldPath" => "DOCUMENTO"],
                        ["fieldPath" => "FECHA_ESTUDIO"],
                        ["fieldPath" => "NOMBRES"],
                        ["fieldPath" => "TIPO_ESTUDIO"]
                    ]
                ]
            ]
        ];

        $response = $this->getHttpClient()
            ->post("{$this->baseUrl}:runQuery?key={$this->apiKey}", $query);

        if (!$response->successful()) {
            return [];
        }

        $tiposEstudios = $this->getTiposEstudios();
        $laudos = [];

        foreach ($response->json() as $item) {
            if (isset($item['document'])) {
                $data = $this->parseFirestoreDocument($item['document']);
                // Obtener el nombre del documento
                $documentName = $item['document']['name'] ?? '';
                $documentId = basename($documentName); // Extraer solo el ID del documento
                $tipoEstudioNombre = $tiposEstudios[$data['TIPO_ESTUDIO']] ?? 'Desconocido';

                $laudos[] = [
                    'documento' => $data['DOCUMENTO'],
                    'fecha_estudio' => $data['FECHA_ESTUDIO'],
                    'nombres' => $data['NOMBRES'],
                    'tipo_estudio' => $tipoEstudioNombre,
                    'id_documento' => $documentId
                ];
            }
        }

        return $laudos;
    }

    private function getTiposEstudios()
    {
        $query = [
            "structuredQuery" => [
                "from" => [["collectionId" => "TIPOS_ESTUDIOS"]],
                "select" => [
                    "fields" => [
                        ["fieldPath" => "ID"],
                        ["fieldPath" => "NOMBRE_ESTUDIO"]
                    ]
                ]
            ]
        ];

        $response = $this->getHttpClient()
            ->post("{$this->baseUrl}:runQuery?key={$this->apiKey}", $query);

        $tipos = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    $tipos[$data['ID']] = $data['NOMBRE_ESTUDIO'];
                }
            }
        }

        return $tipos;
    }

    private function parseFirestoreDocument($document)
    {
        $result = [];
        foreach ($document['fields'] as $field => $value) {
            $valueType = array_key_first($value);
            $result[$field] = $value[$valueType];
        }
        return $result;
    }

    public function getClinicaById($id_clinica)
{
    try {
        $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/CLINICAS/{$id_clinica}";

        $response = $this->getHttpClient()
            ->get($baseUrl);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'nombre' => $data['fields']['NOMBRE']['stringValue'] ?? null,
                // Otros campos que necesites
            ];
        }

        return null;
    } catch (\Exception $e) {
        Log::error('Error al obtener clínica: ' . $e->getMessage());
        return null;
    }
}

public function getFirebaseToken()
    {
        try {
            // Obtén un token personalizado con Firebase Admin SDK
            $auth = $this->firebase->createAuth();
            $customToken = $auth->createCustomToken('QyLK8yuVpEeCCsZ2LO3HelAicYa2'); // Cambia el identificador si es necesario
            return $customToken->toString();
        } catch (\Exception $e) {
            Log::error('Error al generar token de Firebase: ' . $e->getMessage());
            return null;
        }
    }


    public function getFirebaseStorageToken($path, $expirationTimeInSeconds = 3600)
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket();

            // Genera una URL firmada para el archivo
            $object = $bucket->object($path);
            $url = $object->signedUrl(new \DateTime('+ ' . $expirationTimeInSeconds . ' seconds'));
            return $url;
        } catch (\Exception $e) {
            Log::error('Error al generar URL de Firebase Storage: ' . $e->getMessage());
            return null;
        }
    }


    public function getPDFUrl($nombre_clinica, $id_laudo)
    {
        try {

            return $this->getFirebaseStorageToken("LAUDOS/{$nombre_clinica}/{$id_laudo}.pdf");

        } catch (\Exception $e) {
            Log::error('Error al generar URL de PDF: ' . $e->getMessage());
            return null;
        }
    }
}
