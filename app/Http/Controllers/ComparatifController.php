<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Services\ComparatifService;
use Barryvdh\DomPDF\Facade\Pdf;

class ComparatifController extends Controller
{
    public function __construct(
        private ComparatifService $comparatifService
    ) {}

    public function index(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Le comparatif n\'est disponible que pour les états des lieux de sortie.');
        }

        $edlEntree = $this->comparatifService->getEdlEntree($etatDesLieux);

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        $edlEntree->load('pieces.elements.photos');
        $etatDesLieux->load('pieces.elements.photos');

        $comparatif = $this->comparatifService->buildComparatif($edlEntree, $etatDesLieux);
        $stats = $this->comparatifService->calculateStats($comparatif);

        return view('etats-des-lieux.comparatif', [
            'edlEntree' => $edlEntree,
            'edlSortie' => $etatDesLieux,
            'comparatif' => $comparatif,
            'stats' => $stats,
        ]);
    }

    public function pdf(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Le comparatif n\'est disponible que pour les états des lieux de sortie.');
        }

        $edlEntree = $this->comparatifService->getEdlEntree($etatDesLieux);

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        $edlEntree->load('pieces.elements.photos');
        $etatDesLieux->load('pieces.elements.photos');

        $comparatif = $this->comparatifService->buildComparatif($edlEntree, $etatDesLieux);
        $stats = $this->comparatifService->calculateStats($comparatif);

        $pdf = Pdf::loadView('etats-des-lieux.comparatif-pdf', [
            'edlEntree' => $edlEntree,
            'edlSortie' => $etatDesLieux,
            'comparatif' => $comparatif,
            'stats' => $stats,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'comparatif_' . $etatDesLieux->logement->nom . '_' . $etatDesLieux->date_realisation->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}