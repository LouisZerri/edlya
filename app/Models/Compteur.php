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
        'photos',
        'commentaire',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($compteur) {
            if ($compteur->photos) {
                foreach ($compteur->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
        });
    }

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
        if (empty($this->photos)) {
            return null;
        }
        return Storage::url($this->photos[0]);
    }

    public function getPhotosUrlsAttribute(): array
    {
        if (empty($this->photos)) {
            return [];
        }
        return array_map(fn($photo) => Storage::url($photo), $this->photos);
    }
}