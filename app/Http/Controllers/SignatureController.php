<?php

namespace App\Http\Controllers;

use App\Mail\CodeSignatureMail;
use App\Mail\SignatureLocataireMail;
use App\Models\EtatDesLieux;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SignatureController extends Controller
{
    /**
     * Page signature côté bailleur (authentifié)
     */
    public function show(EtatDesLieux $etatDesLieux): View
    {
        $this->authorizeAccess($etatDesLieux);

        return view('etats-des-lieux.signature', compact('etatDesLieux'));
    }

    /**
     * Étape 1 : Signature du bailleur
     */
    public function signerBailleur(Request $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

        $request->validate([
            'signature_bailleur' => ['required', 'string'],
        ], [
            'signature_bailleur.required' => 'La signature du bailleur est requise.',
        ]);

        $etatDesLieux->update([
            'signature_bailleur' => $request->signature_bailleur,
            'date_signature_bailleur' => now(),
        ]);

        return redirect()
            ->route('etats-des-lieux.signature', $etatDesLieux)
            ->with('success', 'Signature du bailleur enregistrée.');
    }

    /**
     * Étape 2 : Envoi du lien de signature au locataire
     */
    public function envoyerLien(Request $request, EtatDesLieux $etatDesLieux): RedirectResponse
    {
        $this->authorizeAccess($etatDesLieux);

        if (!$etatDesLieux->baileurASigne()) {
            return redirect()
                ->route('etats-des-lieux.signature', $etatDesLieux)
                ->with('error', 'Le bailleur doit d\'abord signer.');
        }

        if (empty($etatDesLieux->locataire_email)) {
            return redirect()
                ->route('etats-des-lieux.signature', $etatDesLieux)
                ->with('error', 'L\'email du locataire n\'est pas renseigné.');
        }

        // Générer le token
        $etatDesLieux->genererSignatureToken();

        // Envoyer l'email
        Mail::to($etatDesLieux->locataire_email)->send(new SignatureLocataireMail($etatDesLieux));

        return redirect()
            ->route('etats-des-lieux.signature', $etatDesLieux)
            ->with('success', 'Lien de signature envoyé à ' . $etatDesLieux->locataire_email);
    }

    /**
     * Page publique : Signature locataire
     */
    public function showLocataire(string $token): View|RedirectResponse
    {
        $etatDesLieux = EtatDesLieux::where('signature_token', $token)->first();

        if (!$etatDesLieux || !$etatDesLieux->tokenEstValide($token)) {
            abort(404, 'Lien invalide ou expiré.');
        }

        if ($etatDesLieux->locataireASigne()) {
            return view('signature.confirmation', compact('etatDesLieux'));
        }

        return view('signature.locataire', compact('etatDesLieux', 'token'));
    }

    /**
     * Envoi du code de validation au locataire (depuis page publique)
     */
    public function envoyerCodeLocataire(Request $request, string $token): RedirectResponse
    {
        $etatDesLieux = EtatDesLieux::where('signature_token', $token)->first();

        if (!$etatDesLieux || !$etatDesLieux->tokenEstValide($token)) {
            abort(404, 'Lien invalide ou expiré.');
        }

        $code = $etatDesLieux->genererCodeValidation();

        Mail::to($etatDesLieux->locataire_email)->send(new CodeSignatureMail($etatDesLieux, $code));

        return redirect()
            ->route('signature.locataire', ['token' => $token])
            ->with('success', 'Code envoyé à ' . $etatDesLieux->locataire_email);
    }

    /**
     * Vérification du code (depuis page publique)
     */
    public function verifierCodeLocataire(Request $request, string $token): RedirectResponse
    {
        $etatDesLieux = EtatDesLieux::where('signature_token', $token)->first();

        if (!$etatDesLieux || !$etatDesLieux->tokenEstValide($token)) {
            abort(404, 'Lien invalide ou expiré.');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (!$etatDesLieux->verifierCode($request->code)) {
            return redirect()
                ->route('signature.locataire', ['token' => $token])
                ->with('error', 'Code invalide ou expiré.');
        }

        $etatDesLieux->marquerCodeVerifie();

        return redirect()
            ->route('signature.locataire', ['token' => $token])
            ->with('success', 'Code validé ! Vous pouvez maintenant signer.');
    }

    /**
     * Signature du locataire (depuis page publique)
     */
    public function signerLocatairePublic(Request $request, string $token): RedirectResponse
    {
        $etatDesLieux = EtatDesLieux::where('signature_token', $token)->first();

        if (!$etatDesLieux || !$etatDesLieux->tokenEstValide($token)) {
            abort(404, 'Lien invalide ou expiré.');
        }

        if (!$etatDesLieux->codeEstValide()) {
            return redirect()
                ->route('signature.locataire', ['token' => $token])
                ->with('error', 'Veuillez d\'abord valider votre code.');
        }

        $request->validate([
            'signature_locataire' => ['required', 'string'],
        ]);

        $etatDesLieux->update([
            'signature_locataire' => $request->signature_locataire,
            'date_signature_locataire' => now(),
            'signature_ip' => $request->ip(),
            'signature_user_agent' => $request->userAgent(),
            'statut' => 'signe',
            'date_signature' => now(),
            'signature_token' => null, // Invalider le token
            'signature_token_expire_at' => null,
        ]);

        return redirect()
            ->route('signature.confirmation', ['token' => $token])
            ->with('success', 'État des lieux signé avec succès !');
    }

    /**
     * Page de confirmation après signature
     */
    public function confirmation(string $token): View
    {
        // Récupérer via l'ID stocké en session ou via une autre méthode
        $etatDesLieux = EtatDesLieux::where('statut', 'signe')
            ->whereNotNull('signature_locataire')
            ->whereNotNull('date_signature_locataire')
            ->latest('date_signature_locataire')
            ->firstOrFail();

        return view('signature.confirmation', compact('etatDesLieux'));
    }

    private function authorizeAccess(EtatDesLieux $etatDesLieux): void
    {
        if ($etatDesLieux->user_id != Auth::id()) {
            abort(403);
        }
    }
}