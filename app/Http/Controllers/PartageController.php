<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartageRequest;
use App\Mail\PartageEtatDesLieux;
use App\Models\EtatDesLieux;
use App\Models\Partage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class PartageController extends Controller
{
    public function store(PartageRequest $request, EtatDesLieux $etatDesLieux): JsonResponse
    {
        $this->authorize('view', $etatDesLieux);

        $validated = $request->validated();

        $partage = Partage::create([
            'etat_des_lieux_id' => $etatDesLieux->id,
            'token' => Partage::generateToken(),
            'email' => $validated['type'] === 'email' ? $validated['email'] : null,
            'type' => $validated['type'],
            'expire_at' => now()->addDays((int) $validated['duree']),
        ]);

        if ($validated['type'] === 'email') {
            Mail::to($validated['email'])->send(new PartageEtatDesLieux($etatDesLieux, $partage));
        }

        return response()->json([
            'success' => true,
            'message' => $validated['type'] === 'email'
                ? 'Email envoyé avec succès à ' . $validated['email']
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
        $this->authorize('view', $etatDesLieux);

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
        $this->authorize('view', $partage->etatDesLieux);

        $partage->delete();

        return response()->json(['success' => true]);
    }
}
