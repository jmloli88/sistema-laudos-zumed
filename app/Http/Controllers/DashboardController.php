<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    private $firebaseService;
    private const PER_PAGE = 20; // Número de items por página

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        if (session('id_clinica') == "") {
            return redirect()->route('login.show')->with('message', 'Sessão fechada por inatividade');
        }

        $searchTerm = $request->input('search', '');
        $id_clinica = session('id_clinica');
        $currentPage = $request->get('page', 1);

        // Obtener nombre de la clínica
        $clinica = $this->firebaseService->getClinicaById($id_clinica);
        $nombre_clinica = $clinica['nombre'] ?? '';
        session([
            'nombre_clinica' => $nombre_clinica
        ]);

        // Obtener todos los laudos
        $allLaudos = $this->firebaseService->getLaudos($id_clinica);

        // Convertir a Collection para usar métodos de Laravel
        $laudosCollection = collect($allLaudos);

        // Filtrar laudos si hay término de búsqueda
        if (!empty($searchTerm)) {
            $laudosCollection = $laudosCollection->filter(function ($laudo) use ($searchTerm) {
                return
                    stripos($laudo['documento'], $searchTerm) !== false ||
                    stripos($laudo['nombres'], $searchTerm) !== false;
            });
        }

        // Ordenar los laudos por fecha de estudio (asumiendo que existe el campo)
        $laudosCollection = $laudosCollection->sortBy('fecha_estudio');

        // Crear paginador manual
        $total = $laudosCollection->count();
        $start = ($currentPage - 1) * self::PER_PAGE;

        // Obtener solo los items de la página actual
        $laudosForCurrentPage = $laudosCollection->slice($start, self::PER_PAGE)->values();

        // Crear instancia de LengthAwarePaginator
        $laudos = new LengthAwarePaginator(
            $laudosForCurrentPage,
            $total,
            self::PER_PAGE,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('dashboard', compact('laudos', 'id_clinica'));
    }

    public function verPDF($id_clinica, $id_laudo)
    {
        // En lugar de generar URL externa, usar endpoint interno
        $pdfUrl = route('serve.pdf', ['id_clinica' => $id_clinica, 'id_laudo' => $id_laudo]);
        return view('ver_pdf', compact('pdfUrl'));
    }

    /**
     * Sirve el PDF directamente desde el servidor con headers correctos
     */
    public function servePDF($id_clinica, $id_laudo)
    {
        try {
            // Obtener nombre de la clínica
            $clinica = $this->firebaseService->getClinicaById($id_clinica);
            $nombre_clinica = $clinica['nombre'] ?? '';

            // No agregar .pdf si ya está incluido en el id_laudo
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (!str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }
            
            // Descargar el contenido directamente desde Firebase Storage
            $pdfContent = $this->firebaseService->downloadFileContent($path);
            
            if (!$pdfContent) {
                abort(404, 'PDF no encontrado');
            }
            
            // Servir el PDF con headers correctos para visualización
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="laudo-'.$id_laudo.'.pdf"')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('X-Frame-Options', 'SAMEORIGIN');
                
        } catch (\Exception $e) {
            \Log::error('Error al servir PDF: ' . $e->getMessage());
            abort(500, 'Error interno del servidor');
        }
    }

    /**
     * Descarga directamente un PDF sin visualización previa
     *
     * @param string $id_clinica ID de la clínica
     * @param string $id_laudo ID del laudo a descargar
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($id_clinica, $id_laudo)
    {
        try {
            // Obtener nombre de la clínica
            $clinica = $this->firebaseService->getClinicaById($id_clinica);
            $nombre_clinica = $clinica['nombre'] ?? '';

            \Log::info("Descarga PDF - ID Clínica: {$id_clinica}, Nombre Clínica: '{$nombre_clinica}', ID Laudo: {$id_laudo}");

            // No agregar .pdf si ya está incluido en el id_laudo
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (!str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }
            
            \Log::info("Ruta completa del archivo: {$path}");
            
            // Descargar el contenido directamente desde Firebase Storage
            $pdfContent = $this->firebaseService->downloadFileContent($path);
            
            if (!$pdfContent) {
                \Log::error("No se pudo obtener el contenido del archivo: {$path}");
                return response()->json(['error' => 'El archivo PDF no existe o no se pudo descargar'], 404);
            }
            
            // Generar nombre de archivo para descarga
            $filename = str_ends_with($id_laudo, '.pdf') ? $id_laudo : $id_laudo . '.pdf';
            
            \Log::info("Descarga exitosa del archivo: {$path}");
            
            // Generar respuesta para descarga directa
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('Error al descargar PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to create paginator for arrays or collections
     *
     * @param Collection|array $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    private function paginate($items, $perPage = self::PER_PAGE, $page = null, $options = [])
    {
        $page = $page ?: (request()->get('page', 1));

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
}
