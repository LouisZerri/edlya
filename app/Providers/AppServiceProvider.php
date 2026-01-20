<?php

namespace App\Providers;

use App\Models\Cle;
use App\Models\Compteur;
use App\Models\Element;
use App\Models\EtatDesLieux;
use App\Models\Logement;
use App\Models\Photo;
use App\Models\Piece;
use App\Observers\CleObserver;
use App\Observers\CompteurObserver;
use App\Observers\ElementObserver;
use App\Observers\EtatDesLieuxObserver;
use App\Observers\LogementObserver;
use App\Observers\PhotoObserver;
use App\Observers\PieceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        EtatDesLieux::observe(EtatDesLieuxObserver::class);
        Logement::observe(LogementObserver::class);
        Piece::observe(PieceObserver::class);
        Element::observe(ElementObserver::class);
        Photo::observe(PhotoObserver::class);
        Compteur::observe(CompteurObserver::class);
        Cle::observe(CleObserver::class);
    }
}
