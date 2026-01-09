<?php

namespace App\Http\Controllers;

use App\Models\EtatDesLieux;
use App\Models\Logement;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Stats générales
        $stats = [
            'logements' => Logement::where('user_id', $user->id)->count(),
            'etats_des_lieux' => EtatDesLieux::where('user_id', $user->id)->count(),
            'en_attente' => EtatDesLieux::where('user_id', $user->id)
                ->whereIn('statut', ['brouillon', 'en_cours'])
                ->count(),
            'signes' => EtatDesLieux::where('user_id', $user->id)
                ->where('statut', 'signe')
                ->count(),
        ];

        // Répartition par type
        $repartition = [
            'entree' => EtatDesLieux::where('user_id', $user->id)->where('type', 'entree')->count(),
            'sortie' => EtatDesLieux::where('user_id', $user->id)->where('type', 'sortie')->count(),
        ];

        // Derniers états des lieux
        $derniersEdl = EtatDesLieux::where('user_id', $user->id)
            ->with('logement')
            ->latest()
            ->take(5)
            ->get();

        // Logements sans état des lieux
        $logementsVides = Logement::where('user_id', $user->id)
            ->whereDoesntHave('etatsDesLieux')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'repartition', 'derniersEdl', 'logementsVides'));
    }
}