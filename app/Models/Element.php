<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'piece_id',
        'type',
        'nom',
        'etat',
        'observations',
        'degradations',
    ];

    protected function casts(): array
    {
        return [
            'degradations' => 'array',
        ];
    }

    public function piece(): BelongsTo
    {
        return $this->belongsTo(Piece::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function getEtatLibelleAttribute(): string
    {
        return match($this->etat) {
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon',
            'bon' => 'Bon',
            'usage' => 'Usagé',
            'mauvais' => 'Mauvais',
            'hors_service' => 'Hors service',
            default => $this->etat,
        };
    }

    public function getEtatCouleurAttribute(): string
    {
        return match($this->etat) {
            'neuf' => 'bg-purple-100 text-purple-700',
            'tres_bon' => 'bg-emerald-100 text-emerald-700',
            'bon' => 'bg-blue-100 text-blue-700',
            'usage' => 'bg-amber-100 text-amber-700',
            'mauvais' => 'bg-orange-100 text-orange-700',
            'hors_service' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public function getTypeLibelleAttribute(): string
    {
        return match($this->type) {
            'sol' => 'Sol',
            'mur' => 'Mur',
            'plafond' => 'Plafond',
            'menuiserie' => 'Menuiserie',
            'electricite' => 'Électricité',
            'plomberie' => 'Plomberie',
            'chauffage' => 'Chauffage',
            'equipement' => 'Équipement',
            'mobilier' => 'Mobilier',
            'electromenager' => 'Électroménager',
            'autre' => 'Autre',
            default => ucfirst($this->type),
        };
    }

    /**
     * Récupérer les dégradations suggérées pour ce type d'élément
     */
    public function getDegradationsSuggerees(): array
    {
        return config("degradations.{$this->type}", config('degradations.autre', []));
    }

    /**
     * Vérifier si l'élément a des dégradations
     */
    public function hasDegradations(): bool
    {
        return !empty($this->degradations) && count($this->degradations) > 0;
    }

    /**
     * Récupérer les dégradations formatées en texte
     */
    public function getDegradationsFormateesAttribute(): string
    {
        if (!$this->hasDegradations()) {
            return '';
        }

        return implode(', ', $this->degradations);
    }
}