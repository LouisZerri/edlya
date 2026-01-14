<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Logement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'adresse',
        'code_postal',
        'ville',
        'type',
        'surface',
        'nb_pieces',
        'description',
        'photo_principale',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($logement) {
            foreach ($logement->etatsDesLieux as $edl) {
                // Photos des éléments
                foreach ($edl->pieces as $piece) {
                    foreach ($piece->elements as $element) {
                        foreach ($element->photos as $photo) {
                            Storage::disk('public')->delete($photo->chemin);
                        }
                    }
                }
                
                // Photos des compteurs
                foreach ($edl->compteurs as $compteur) {
                    if ($compteur->photo) {
                        Storage::disk('public')->delete($compteur->photo);
                    }
                }
                
                // Photos des clés
                foreach ($edl->cles as $cle) {
                    if ($cle->photo) {
                        Storage::disk('public')->delete($cle->photo);
                    }
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function etatsDesLieux(): HasMany
    {
        return $this->hasMany(EtatDesLieux::class);
    }

    public function getAdresseCompleteAttribute(): string
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}";
    }
}