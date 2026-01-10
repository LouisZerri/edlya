<?php

namespace App\Services;

use App\Models\EtatDesLieux;

class ComparatifService
{
    private array $etatsOrdre = [
        'neuf' => 6,
        'tres_bon' => 5,
        'bon' => 4,
        'usage' => 3,
        'mauvais' => 2,
        'hors_service' => 1,
    ];

    public function getEdlEntree(EtatDesLieux $edlSortie): ?EtatDesLieux
    {
        return EtatDesLieux::where('logement_id', $edlSortie->logement_id)
            ->where('type', 'entree')
            ->where('statut', 'signe')
            ->where('date_realisation', '<=', $edlSortie->date_realisation)
            ->where('id', '!=', $edlSortie->id)
            ->orderBy('date_realisation', 'desc')
            ->first();
    }

    public function buildComparatif(EtatDesLieux $entree, EtatDesLieux $sortie): array
    {
        $comparatif = [];

        $piecesEntree = $this->indexPiecesEntree($entree);

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

                $elementData = $this->compareElement($elementSortie, $elementEntree);

                if ($elementData['status'] === 'degrade') {
                    $pieceData['has_degradation'] = true;
                }

                $pieceData['elements'][] = $elementData;
            }

            $comparatif[] = $pieceData;
        }

        return $comparatif;
    }

    public function calculateStats(array $comparatif): array
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

    public function getDegradations(array $comparatif): array
    {
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

        return $degradations;
    }

    private function indexPiecesEntree(EtatDesLieux $entree): array
    {
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

        return $piecesEntree;
    }

    private function compareElement($elementSortie, $elementEntree): array
    {
        $elementData = [
            'sortie' => $elementSortie,
            'entree' => $elementEntree,
            'status' => 'nouveau',
            'evolution' => 0,
        ];

        if ($elementEntree) {
            $scoreEntree = $this->etatsOrdre[$elementEntree->etat] ?? 0;
            $scoreSortie = $this->etatsOrdre[$elementSortie->etat] ?? 0;
            $elementData['evolution'] = $scoreSortie - $scoreEntree;

            if ($scoreSortie < $scoreEntree) {
                $elementData['status'] = 'degrade';
            } elseif ($scoreSortie > $scoreEntree) {
                $elementData['status'] = 'ameliore';
            } else {
                $elementData['status'] = 'identique';
            }
        }

        return $elementData;
    }
}