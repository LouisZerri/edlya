<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartageController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirection accueil vers login ou dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Routes authentification
require __DIR__ . '/auth.php';

// Routes logements
require __DIR__ . '/logements.php';

// Routes états des lieux
require __DIR__ . '/etats-des-lieux.php';

// Routes protégées
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Pages légales
Route::view('/politique-de-confidentialite', 'politique-confidentialite')->name('politique-confidentialite');

// Routes publiques pour les partages
Route::get('p/{token}', [PartageController::class, 'show'])->name('partage.show');
Route::get('p/{token}/pdf', [PartageController::class, 'pdf'])->name('partage.pdf');

// Routes publiques pour signature locataire
Route::get('signature/{token}', [SignatureController::class, 'showLocataire'])->name('signature.locataire');
Route::post('signature/{token}/envoyer-code', [SignatureController::class, 'envoyerCodeLocataire'])->name('signature.locataire.envoyer-code');
Route::post('signature/{token}/verifier-code', [SignatureController::class, 'verifierCodeLocataire'])->name('signature.locataire.verifier-code');
Route::post('signature/{token}/signer', [SignatureController::class, 'signerLocatairePublic'])->name('signature.locataire.signer');
Route::get('signature/{token}/confirmation', [SignatureController::class, 'confirmation'])->name('signature.confirmation');