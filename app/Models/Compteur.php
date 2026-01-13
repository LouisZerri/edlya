<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Compteur extends Model
{
    use HasFactory;

    protected $fillable = [
        'etat_des_lieux_id',
        'type',
        'numero',
        'index',
        'photo',
        'commentaire',
    ];

    public function etatDesLieux(): BelongsTo
    {
        return $this->belongsTo(EtatDesLieux::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'electricite' => 'Électricité',
            'eau_froide' => 'Eau froide',
            'eau_chaude' => 'Eau chaude',
            'gaz' => 'Gaz',
            default => $this->type,
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return Storage::url($this->photo);
    }
}