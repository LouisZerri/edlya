<?php

namespace App\Http\Controllers;

use App\Http\Requests\EtatDesLieuxRequest;
use App\Models\CoutReparation;
use App\Models\EtatDesLieux;
use App\Models\Logement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EtatDesLieuxController extends Controller
{
    public function index(): View
    {
        $etatsDesLieux = EtatDesLieux::where('user_id', Auth::id())
            ->with('logement')
            ->latest()
            ->get();

        return view('etats-des-lieux.index', compact('etatsDesLieux'));
    }

    public function create(): View
    {
        $logements = Logement::where('user_id', Auth::id())->get();
        $logementSelectionne = request()->query('logement');

        return view('etats-des-lieux.create', compact('logements', 'logementSelectionne'));
    }

    public function store(EtatDesLieuxRequest $request): RedirectResponse
    {
        Logement::where('user_id', Auth::id())
            ->where('id', $request->logement_id)
            ->firstOrFail();

        $etatDesLieux = EtatDesLieux::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('etats-des-lieux.edit', $etatDesLieux)
            ->with('success', 'État des lieux créé. Ajoutez maintenant les pièces.');
    }

    public function show(EtatDesLieux $etatDesLieux): View
    {
        $this->authorizeAccess($etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos']);

        return view('etats-des-lieux.show', compact('etatDesLieux'));
    }

    public function edit(EtatDesLieux $etatDesLieux): View
    {
        $this->authorizeAccess($etatDesLieux);

        $etatDesLieux->load(['logement', 'pieces.elements.photos']);
        $logements = Logement::where('user_id', Auth::id())->get();

        return view('etats-des-lieux.edit', compact('etatDesLieux', 'logements'));
    }

    public function update(EtatDesLieuxRequest $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

        $etatDesLieux->update($request->validated());

        return redirect()
            ->route('etats-des-lieux.show', $etatDesLieux)
            ->with('success', 'État des lieux mis à jour.');
    }

    public function destroy(EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

        $etatDesLieux->delete();

        return redirect()
            ->route('etats-des-lieux.index')
            ->with('success', 'État des lieux supprimé.');
    }

    public function pdf(EtatDesLieux $etatDesLieux)
    {
        $this->authorizeAccess($etatDesLieux);

        $etatDesLieux->load(['logement', 'user', 'pieces.elements']);

        $pdf = Pdf::loadView('etats-des-lieux.pdf', compact('etatDesLieux'));

        $filename = 'edl-' . $etatDesLieux->type . '-' . $etatDesLieux->logement->nom . '-' . $etatDesLieux->date_realisation->format('Y-m-d') . '.pdf';
        $filename = str_replace(' ', '-', $filename);

        return $pdf->download($filename);
    }

    public function comparatif(EtatDesLieux $etatDesLieux)
    {
        // Vérifier que c'est bien un EDL de sortie
        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Le comparatif n\'est disponible que pour les états des lieux de sortie.');
        }

        // Trouver l'EDL d'entrée correspondant (même logement, type entrée, le plus récent signé)
        $edlEntree = EtatDesLieux::where('logement_id', $etatDesLieux->logement_id)
            ->where('type', 'entree')
            ->where('statut', 'signe')
            ->where('date_realisation', '<', $etatDesLieux->date_realisation)
            ->orderBy('date_realisation', 'desc')
            ->first();

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        // Charger les relations
        $edlEntree->load('pieces.elements.photos');
        $etatDesLieux->load('pieces.elements.photos');

        // Construire le comparatif par pièce
        $comparatif = $this->buildComparatif($edlEntree, $etatDesLieux);

        return view('etats-des-lieux.comparatif', [
            'edlEntree' => $edlEntree,
            'edlSortie' => $etatDesLieux,
            'comparatif' => $comparatif,
            'stats' => $this->calculateStats($comparatif),
        ]);
    }

    public function comparatifPdf(EtatDesLieux $etatDesLieux)
    {
        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Le comparatif n\'est disponible que pour les états des lieux de sortie.');
        }

        $edlEntree = EtatDesLieux::where('logement_id', $etatDesLieux->logement_id)
            ->where('type', 'entree')
            ->where('statut', 'signe')
            ->where('date_realisation', '<', $etatDesLieux->date_realisation)
            ->orderBy('date_realisation', 'desc')
            ->first();

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        $edlEntree->load('pieces.elements.photos');
        $etatDesLieux->load('pieces.elements.photos');

        $comparatif = $this->buildComparatif($edlEntree, $etatDesLieux);
        $stats = $this->calculateStats($comparatif);

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

    private function buildComparatif(EtatDesLieux $entree, EtatDesLieux $sortie): array
    {
        $comparatif = [];
        $etatsOrdre = ['neuf' => 6, 'tres_bon' => 5, 'bon' => 4, 'usage' => 3, 'mauvais' => 2, 'hors_service' => 1];

        // Indexer les pièces et éléments de l'entrée par nom
        $piecesEntree = [];
        foreach ($entree->pieces as $piece) {
            $elements = [];
            foreach ($piece->elements as $element) {
                $key = $element->type . '_' . $element->nom;
                $elements[$key] = $element;
            }
            $piecesEntree[$piece->nom] = [
                'piece' => $piece,
                'elements' => $elements,
            ];
        }

        // Comparer avec la sortie
        foreach ($sortie->pieces as $pieceSortie) {
            $pieceData = [
                'nom' => $pieceSortie->nom,
                'piece_sortie' => $pieceSortie,
                'piece_entree' => null,
                'elements' => [],
                'has_degradation' => false,
            ];

            $pieceEntreeData = $piecesEntree[$pieceSortie->nom] ?? null;
            if ($pieceEntreeData) {
                $pieceData['piece_entree'] = $pieceEntreeData['piece'];
            }

            foreach ($pieceSortie->elements as $elementSortie) {
                $key = $elementSortie->type . '_' . $elementSortie->nom;
                $elementEntree = $pieceEntreeData['elements'][$key] ?? null;

                $elementData = [
                    'sortie' => $elementSortie,
                    'entree' => $elementEntree,
                    'status' => 'nouveau', // Par défaut : nouvel élément
                    'evolution' => 0,
                ];

                if ($elementEntree) {
                    $scoreEntree = $etatsOrdre[$elementEntree->etat] ?? 0;
                    $scoreSortie = $etatsOrdre[$elementSortie->etat] ?? 0;
                    $elementData['evolution'] = $scoreSortie - $scoreEntree;

                    if ($scoreSortie < $scoreEntree) {
                        $elementData['status'] = 'degrade';
                        $pieceData['has_degradation'] = true;
                    } elseif ($scoreSortie > $scoreEntree) {
                        $elementData['status'] = 'ameliore';
                    } else {
                        $elementData['status'] = 'identique';
                    }
                }

                $pieceData['elements'][] = $elementData;
            }

            $comparatif[] = $pieceData;
        }

        return $comparatif;
    }

    private function calculateStats(array $comparatif): array
    {
        $stats = [
            'total' => 0,
            'identique' => 0,
            'degrade' => 0,
            'ameliore' => 0,
            'nouveau' => 0,
        ];

        foreach ($comparatif as $piece) {
            foreach ($piece['elements'] as $element) {
                $stats['total']++;
                $stats[$element['status']]++;
            }
        }

        return $stats;
    }

    public function estimation(EtatDesLieux $etatDesLieux)
    {
        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'L\'estimation n\'est disponible que pour les états des lieux de sortie.');
        }

        $edlEntree = EtatDesLieux::where('logement_id', $etatDesLieux->logement_id)
            ->where('type', 'entree')
            ->where('statut', 'signe')
            ->where('date_realisation', '<', $etatDesLieux->date_realisation)
            ->orderBy('date_realisation', 'desc')
            ->first();

        if (!$edlEntree) {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'Aucun état des lieux d\'entrée signé trouvé pour ce logement.');
        }

        $edlEntree->load('pieces.elements');
        $etatDesLieux->load('pieces.elements');

        $comparatif = $this->buildComparatif($edlEntree, $etatDesLieux);

        // Filtrer uniquement les dégradations
        $degradations = [];
        foreach ($comparatif as $piece) {
            foreach ($piece['elements'] as $element) {
                if ($element['status'] === 'degrade') {
                    $degradations[] = [
                        'piece' => $piece['nom'],
                        'element' => $element['sortie'],
                        'entree' => $element['entree'],
                        'evolution' => $element['evolution'],
                    ];
                }
            }
        }

        // Récupérer les coûts de réparation par type
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

    public function estimationPdf(Request $request, EtatDesLieux $etatDesLieux)
    {
        if ($etatDesLieux->type !== 'sortie') {
            return redirect()->route('etats-des-lieux.show', $etatDesLieux)
                ->with('error', 'L\'estimation n\'est disponible que pour les états des lieux de sortie.');
        }

        $lignes = $request->input('lignes', []);

        // Calculer les totaux
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

    private function authorizeAccess(EtatDesLieux $etatDesLieux): void
    {
        if ($etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}
