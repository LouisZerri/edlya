<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartageController;
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

// Routes publiques pour les partages
Route::get('p/{token}', [PartageController::class, 'show'])->name('partage.show');
Route::get('p/{token}/pdf', [PartageController::class, 'pdf'])->name('partage.pdf');