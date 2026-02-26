<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Compteur extends Model
{
    use HasFactory;

    protected $table = 'compteur';

    protected $fillable = [
        'etat_des_lieux_id',
        'type',
        'numero',
        'index_value',
        'photos',
        'commentaire',
    ];

    protected $casts = [
        'photos' => 'array',
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
        if (empty($this->photos)) {
            return null;
        }
        return self::resolvePhotoUrl($this->photos[0]);
    }

    public function getPhotosUrlsAttribute(): array
    {
        if (empty($this->photos)) {
            return [];
        }
        return array_map(fn($photo) => self::resolvePhotoUrl($photo), $this->photos);
    }

    private static function resolvePhotoUrl(string $path): string
    {
        if (str_starts_with($path, '/uploads/')) {
            return $path;
        }
        return Storage::url($path);
    }

    /**
     * Extrait la valeur numérique totale de l'index du compteur.
     * Gère les index simples ("1245 m³") et composites ("HP : 7548 kWh, HC : 9808 kWh")
     * en sommant toutes les valeurs numériques trouvées.
     */
    public function getIndexNumeriqueAttribute(): ?float
    {
        if (empty($this->index_value)) {
            return null;
        }

        preg_match_all('/(\d+[\s.]?\d*)\s*(?:kWh|m³|m3)?/i', $this->index_value, $matches);

        if (empty($matches[1])) {
            return null;
        }

        $total = 0;
        foreach ($matches[1] as $value) {
            $total += (float) str_replace(' ', '', $value);
        }

        return $total;
    }
}