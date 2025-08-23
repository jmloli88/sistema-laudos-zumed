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
        $this->projectId = config('firebase.project_id');
        $this->apiKey = config('firebase.api_key');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
        
        $this->firebase = (new Factory)
            ->withServiceAccount(base_path('storage/app/firebase/credenciales.json'));

        // Inicializar los servicios que necesites
        $this->firebaseAuth = $this->firebase->createAuth(); // Servicio de autenticación
        $this->firebaseStorage = $this->firebase->createStorage(); // Servicio

        // Configurar el bucket desde la configuración
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
            $bucket = $storage->getBucket($this->storageBucket);

            // Genera una URL firmada para el archivo con configuración mejorada
            $object = $bucket->object($path);
            
            // Configurar opciones para la URL firmada
            $options = [
                'version' => 'v4',
                'action' => 'read',
                'expires' => new \DateTime('+ ' . $expirationTimeInSeconds . ' seconds')
            ];
            
            $url = $object->signedUrl($options);
            
            \Log::info("URL generada para {$path}: " . $url);
            
            return $url;
        } catch (\Exception $e) {
            \Log::error('Error al generar URL de Firebase Storage: ' . $e->getMessage());
            return null;
        }
    }


    public function getPDFUrl($nombre_clinica, $id_laudo)
    {
        try {
            // No agregar .pdf si ya está incluido en el id_laudo
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (!str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }
            
            // Verificar si el archivo existe antes de generar la URL
            if (!$this->fileExists($path)) {
                \Log::warning("Archivo PDF no encontrado: {$path}");
                return null;
            }

            return $this->getFirebaseStorageToken($path);

        } catch (\Exception $e) {
            \Log::error('Error al generar URL de PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si un archivo existe en Firebase Storage
     */
    public function fileExists($path)
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);
            $object = $bucket->object($path);
            
            return $object->exists();
        } catch (\Exception $e) {
            \Log::error('Error al verificar existencia del archivo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista las carpetas disponibles en LAUDOS para debugging
     */
    public function listLaudosFolders()
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);
            
            // Listar objetos en la carpeta LAUDOS
            $objects = $bucket->objects(['prefix' => 'LAUDOS/']);
            
            $folders = [];
            foreach ($objects as $object) {
                $name = $object->name();
                \Log::info("Objeto encontrado: {$name}");
                
                // Extraer nombre de carpeta
                if (preg_match('/^LAUDOS\/([^\/]+)\//', $name, $matches)) {
                    $folderName = $matches[1];
                    if (!in_array($folderName, $folders)) {
                        $folders[] = $folderName;
                    }
                }
            }
            
            \Log::info("Carpetas encontradas en LAUDOS: " . implode(', ', $folders));
            return $folders;
            
        } catch (\Exception $e) {
            \Log::error('Error al listar carpetas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Descarga directamente el contenido del archivo desde Firebase Storage
     */
    public function downloadFileContent($path)
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);
            
            
            // Lista de rutas posibles para probar
            $possiblePaths = [
                $path,
                str_replace('CLINICA PRUEBA', 'CLINICA%20PRUEBA', $path),
                str_replace(' ', '%20', $path),
                str_replace(' ', '_', $path),
                str_replace('CLINICA PRUEBA', 'CLINICA_PRUEBA', $path)
            ];
            
            foreach ($possiblePaths as $testPath) {
                \Log::info("Probando ruta: {$testPath}");
                $object = $bucket->object($testPath);
                
                if ($object->exists()) {
                    \Log::info("¡Archivo encontrado en: {$testPath}!");
                    $content = $object->downloadAsString();
                    \Log::info("Archivo descargado exitosamente desde: {$testPath}");
                    return $content;
                }
            }
            
            \Log::warning("Archivo no encontrado en ninguna de las rutas probadas");
            return null;
            
        } catch (\Exception $e) {
            \Log::error('Error al descargar archivo desde Firebase Storage: ' . $e->getMessage());
            return null;
        }
    }
}
