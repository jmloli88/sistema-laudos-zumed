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
        // Obtener nombre de la clínica
        $clinica = $this->firebaseService->getClinicaById($id_clinica);
        $nombre_clinica = $clinica['nombre'] ?? '';

        // Generar URL del PDF en Firebase Storage
        $pdfUrl = $this->firebaseService->getPDFUrl($nombre_clinica, $id_laudo);
        return view('ver_pdf', compact('pdfUrl'));
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
        // Obtener nombre de la clínica
        $clinica = $this->firebaseService->getClinicaById($id_clinica);
        $nombre_clinica = $clinica['nombre'] ?? '';

        // Generar URL del PDF en Firebase Storage
        $pdfUrl = $this->firebaseService->getPDFUrl($nombre_clinica, $id_laudo);
        
        // Obtener el contenido del PDF
        $pdfContent = file_get_contents($pdfUrl);
        
        // Generar respuesta para descarga directa
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laudo-'.$id_laudo.'.pdf"');
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
