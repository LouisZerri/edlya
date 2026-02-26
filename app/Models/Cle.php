<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Cle extends Model
{
    use HasFactory;

    protected $table = 'cle';

    protected $fillable = [
        'etat_des_lieux_id',
        'type',
        'nombre',
        'commentaire',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'nombre' => 'integer',
        ];
    }

    public function etatDesLieux(): BelongsTo
    {
        return $this->belongsTo(EtatDesLieux::class);
    }

    /**
     * Mapping snake_case → libellé français
     */
    public static function getTypeLabels(): array
    {
        return [
            'porte_entree' => 'Porte d\'entrée',
            'parties_communes' => 'Parties communes',
            'boite_lettres' => 'Boîte aux lettres',
            'cave' => 'Cave',
            'garage' => 'Garage',
            'parking' => 'Parking',
            'local_velo' => 'Local vélo',
            'portail' => 'Portail',
            'interphone' => 'Interphone',
            'badge' => 'Badge',
            'telecommande' => 'Télécommande',
            'vigik' => 'Vigik',
            'digicode' => 'Digicode',
            'autre' => 'Autre',
        ];
    }

    /**
     * Libellé français du type
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getLabelForType($this->type);
    }

    /**
     * Convertit un type (snake_case ou legacy) en libellé français
     */
    public static function getLabelForType(string $type): string
    {
        return self::getTypeLabels()[$type] ?? $type;
    }

    /**
     * URL de la photo (compatible chemins Laravel et Symfony)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, '/uploads/')) {
            return $this->photo;
        }

        return Storage::url($this->photo);
    }
}