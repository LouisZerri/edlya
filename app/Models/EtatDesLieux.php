<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EtatDesLieux extends Model
{
    use HasFactory;

    protected $table = 'etats_des_lieux';

    protected $fillable = [
        'logement_id',
        'user_id',
        'type',
        'date_realisation',
        'locataire_nom',
        'locataire_email',
        'locataire_telephone',
        'observations_generales',
        'statut',
        'date_signature',
        'signature_bailleur',
        'signature_locataire',
        'date_signature_bailleur',
        'date_signature_locataire',
    ];

    protected function casts(): array
    {
        return [
            'date_realisation' => 'date',
            'date_signature' => 'datetime',
            'date_signature_bailleur' => 'datetime',
            'date_signature_locataire' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($etatDesLieux) {
            foreach ($etatDesLieux->pieces as $piece) {
                foreach ($piece->elements as $element) {
                    foreach ($element->photos as $photo) {
                        Storage::disk('public')->delete($photo->chemin);
                    }
                }
            }
        });
    }

    public function logement(): BelongsTo
    {
        return $this->belongsTo(Logement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(Piece::class)->orderBy('ordre');
    }

    public function partages(): HasMany
    {
        return $this->hasMany(Partage::class);
    }

    public function getTypeLibelleAttribute(): string
    {
        return $this->type === 'entree' ? 'Entrée' : 'Sortie';
    }

    public function getStatutLibelleAttribute(): string
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'signe' => 'Signé',
            default => $this->statut,
        };
    }

    public function getStatutCouleurAttribute(): string
    {
        return match($this->statut) {
            'brouillon' => 'bg-slate-100 text-slate-600',
            'en_cours' => 'bg-amber-100 text-amber-700',
            'termine' => 'bg-blue-100 text-blue-700',
            'signe' => 'bg-green-100 text-green-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}