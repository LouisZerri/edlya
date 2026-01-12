<?php

use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\ComparatifController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\EtatDesLieuxController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PartageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // États des lieux (CRUD)
    Route::get('etats-des-lieux', [EtatDesLieuxController::class, 'index'])->name('etats-des-lieux.index');
    Route::get('etats-des-lieux/create', [EtatDesLieuxController::class, 'create'])->name('etats-des-lieux.create');
    Route::post('etats-des-lieux', [EtatDesLieuxController::class, 'store'])->name('etats-des-lieux.store');
    Route::get('etats-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'show'])->name('etats-des-lieux.show');
    Route::get('etats-des-lieux/{etatDesLieux}/edit', [EtatDesLieuxController::class, 'edit'])->name('etats-des-lieux.edit');
    Route::put('etats-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'update'])->name('etats-des-lieux.update');
    Route::delete('etats-des-lieux/{etatDesLieux}', [EtatDesLieuxController::class, 'destroy'])->name('etats-des-lieux.destroy');
    Route::get('etats-des-lieux/{etatDesLieux}/pdf', [EtatDesLieuxController::class, 'pdf'])->name('etats-des-lieux.pdf');

    // Pièces
    Route::post('etats-des-lieux/{etatDesLieux}/pieces', [PieceController::class, 'store'])->name('pieces.store');
    Route::put('pieces/{piece}', [PieceController::class, 'update'])->name('pieces.update');
    Route::delete('pieces/{piece}', [PieceController::class, 'destroy'])->name('pieces.destroy');

    // Éléments
    Route::post('pieces/{piece}/elements', [ElementController::class, 'store'])->name('elements.store');
    Route::put('elements/{element}', [ElementController::class, 'update'])->name('elements.update');
    Route::delete('elements/{element}', [ElementController::class, 'destroy'])->name('elements.destroy');

    // Photos
    Route::post('elements/{element}/photos', [PhotoController::class, 'store'])->name('photos.store');
    Route::post('pieces/{piece}/photos', [PhotoController::class, 'storeForPiece'])->name('pieces.photos.store');
    Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    // Signatures
    Route::get('etats-des-lieux/{etatDesLieux}/signature', [SignatureController::class, 'show'])->name('etats-des-lieux.signature');
    Route::post('etats-des-lieux/{etatDesLieux}/signature', [SignatureController::class, 'store']);

    // Analyse IA
    Route::post('analyse/upload', [AnalyseController::class, 'uploadPhoto'])->name('analyse.upload');
    Route::post('analyse/photo', [AnalyseController::class, 'analyserPhoto'])->name('analyse.photo');
    Route::post('analyse/appliquer', [AnalyseController::class, 'appliquerElements'])->name('analyse.appliquer');
    Route::post('analyse/degradation', [AnalyseController::class, 'analyserDegradation'])->name('analyse.degradation');
    Route::post('analyse/degradation-path', [AnalyseController::class, 'analyserDegradationFromPath'])->name('analyse.degradation.path');

    // Comparatif
    Route::get('etats-des-lieux/{etatDesLieux}/comparatif', [ComparatifController::class, 'index'])->name('etats-des-lieux.comparatif');
    Route::get('etats-des-lieux/{etatDesLieux}/comparatif/pdf', [ComparatifController::class, 'pdf'])->name('etats-des-lieux.comparatif.pdf');

    // Estimation
    Route::get('etats-des-lieux/{etatDesLieux}/estimation', [EstimationController::class, 'index'])->name('etats-des-lieux.estimation');
    Route::post('etats-des-lieux/{etatDesLieux}/estimation/pdf', [EstimationController::class, 'pdf'])->name('etats-des-lieux.estimation.pdf');

    // Partages
    Route::post('etats-des-lieux/{etatDesLieux}/partage', [PartageController::class, 'store'])->name('partage.store');
    Route::get('etats-des-lieux/{etatDesLieux}/partages', [PartageController::class, 'history'])->name('partage.history');
    Route::delete('partages/{partage}', [PartageController::class, 'destroy'])->name('partage.destroy');

    // Import PDF
    Route::get('/import', [ImportController::class, 'create'])->name('etats-des-lieux.import');
    Route::post('/import/analyze', [ImportController::class, 'analyze'])->name('etats-des-lieux.import.analyze');
    Route::post('/import/store', [ImportController::class, 'store'])->name('etats-des-lieux.import.store');

    // Pré-remplissage typologie
    Route::post('etats-des-lieux/{etatDesLieux}/generer-pieces', [EtatDesLieuxController::class, 'genererPieces'])->name('etats-des-lieux.generer-pieces');
    Route::get('typologies', [EtatDesLieuxController::class, 'getTypologies'])->name('typologies.index');
});