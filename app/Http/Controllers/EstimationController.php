<?php

namespace App\Http\Controllers;

use App\Models\CoutReparation;
use App\Models\EtatDesLieux;
use App\Services\ComparatifService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EstimationController extends Controller
{
    public function __construct(
        private ComparatifService $comparatifService
    ) {}

    public function index(EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'L\'estimation n\'est disponible que pour les états des lieux de sortie.');
        }

        $edlEntree = $this->comparatifService->getEdlEntree($etatDesLieux);

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        $edlEntree->load('pieces.elements');
        $etatDesLieux->load('pieces.elements.photos');

        $comparatif = $this->comparatifService->buildComparatif($edlEntree, $etatDesLieux);
        $degradations = $this->comparatifService->getDegradations($comparatif);

        $couts = CoutReparation::actif()
            ->orderBy('type_element')
            ->orderBy('nom')
            ->get()
            ->groupBy('type_element');

        return view('etats-des-lieux.estimation', [
            'edlSortie' => $etatDesLieux,
            'edlEntree' => $edlEntree,
            'degradations' => $degradations,
            'couts' => $couts,
        ]);
    }

    public function pdf(Request $request, EtatDesLieux $etatDesLieux)
    {
        $this->authorize('view', $etatDesLieux);

        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'L\'estimation n\'est disponible que pour les états des lieux de sortie.');
        }

        $lignes = $request->input('lignes', []);

        $totalHT = 0;
        $lignesDevis = [];

        foreach ($lignes as $ligne) {
            if (!empty($ligne['description']) && !empty($ligne['quantite']) && !empty($ligne['prix_unitaire'])) {
                $montant = floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']);
                $totalHT += $montant;

                $lignesDevis[] = [
                    'piece' => $ligne['piece'] ?? '',
                    'description' => $ligne['description'],
                    'quantite' => floatval($ligne['quantite']),
                    'unite' => $ligne['unite'] ?? 'unité',
                    'prix_unitaire' => floatval($ligne['prix_unitaire']),
                    'montant' => $montant,
                ];
            }
        }

        $tva = $totalHT * 0.20;
        $totalTTC = $totalHT + $tva;

        $pdf = Pdf::loadView('etats-des-lieux.estimation-pdf', [
            'edlSortie' => $etatDesLieux,
            'lignes' => $lignesDevis,
            'totalHT' => $totalHT,
            'tva' => $tva,
            'totalTTC' => $totalTTC,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'devis_reparations_' . $etatDesLieux->logement->nom . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}