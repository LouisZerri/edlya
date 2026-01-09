<?php

namespace App\Http\Controllers;

use App\Mail\PartageEtatDesLieux;
use App\Models\EtatDesLieux;
use App\Models\Partage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PartageController extends Controller
{

    public function store(Request $request, EtatDesLieux $etatDesLieux): JsonResponse
    {
        if ($etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'type' => ['required', 'in:email,lien'],
            'email' => ['required_if:type,email', 'nullable', 'email'],
            'duree' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $partage = Partage::create([
            'etat_des_lieux_id' => $etatDesLieux->id,
            'token' => Partage::generateToken(),
            'email' => $request->type === 'email' ? $request->email : null,
            'type' => $request->type,
            'expire_at' => now()->addDays((int) $request->duree),
        ]);

        if ($request->type === 'email') {
            Mail::to($request->email)->send(new PartageEtatDesLieux($etatDesLieux, $partage));
        }

        return response()->json([
            'success' => true,
            'message' => $request->type === 'email'
                ? 'Email envoyé avec succès à ' . $request->email
                : 'Lien généré avec succès',
            'url' => $partage->url,
            'expire_at' => $partage->expire_at->format('d/m/Y à H:i'),
        ]);
    }

    public function show(string $token)
    {
        $partage = Partage::where('token', $token)->firstOrFail();

        if ($partage->isExpired()) {
            return view('partage.expired');
        }

        $partage->markAsConsulted();

        $etatDesLieux = $partage->etatDesLieux;
        $etatDesLieux->load(['logement', 'pieces.elements.photos']);

        return view('partage.show', [
            'etatDesLieux' => $etatDesLieux,
            'partage' => $partage,
        ]);
    }

    public function pdf(string $token)
    {
        $partage = Partage::where('token', $token)->firstOrFail();

        if ($partage->isExpired()) {
            return redirect()->route('partage.show', $token);
        }

        $etatDesLieux = $partage->etatDesLieux;
        $etatDesLieux->load(['logement', 'user', 'pieces.elements.photos']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('etats-des-lieux.pdf', [
            'etatDesLieux' => $etatDesLieux,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $type = $etatDesLieux->type === 'entree' ? 'entree' : 'sortie';
        $filename = 'edl_' . $type . '_' . $etatDesLieux->logement->nom . '_' . $etatDesLieux->date_realisation->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function history(EtatDesLieux $etatDesLieux): JsonResponse
    {
        if ($etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $partages = $etatDesLieux->partages()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($partage) {
                return [
                    'id' => $partage->id,
                    'type' => $partage->type,
                    'email' => $partage->email,
                    'url' => $partage->url,
                    'expire_at' => $partage->expire_at->format('d/m/Y à H:i'),
                    'consulte_at' => $partage->consulte_at?->format('d/m/Y à H:i'),
                    'is_expired' => $partage->isExpired(),
                    'created_at' => $partage->created_at->format('d/m/Y à H:i'),
                ];
            });

        return response()->json($partages);
    }

    public function destroy(Partage $partage): JsonResponse
    {
        if ($partage->etatDesLieux->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $partage->delete();

        return response()->json(['success' => true]);
    }
}
