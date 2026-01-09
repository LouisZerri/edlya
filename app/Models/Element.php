<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'piece_id',
        'type',
        'nom',
        'etat',
        'observations',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($element) {
            foreach ($element->photos as $photo) {
                Storage::disk('public')->delete($photo->chemin);
            }
        });
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
            'neuf' => 'bg-green-100 text-green-700',
            'tres_bon' => 'bg-emerald-100 text-emerald-700',
            'bon' => 'bg-blue-100 text-blue-700',
            'usage' => 'bg-amber-100 text-amber-700',
            'mauvais' => 'bg-orange-100 text-orange-700',
            'hors_service' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}