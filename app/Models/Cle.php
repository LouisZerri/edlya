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
     * URL de la photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return Storage::url($this->photo);
    }

    /**
     * Types de clés courants
     */
    public static function getTypesCommuns(): array
    {
        return [
            'Porte d\'entrée',
            'Porte de service',
            'Boîte aux lettres',
            'Cave',
            'Garage',
            'Portail',
            'Portillon',
            'Local vélo',
            'Local poubelles',
            'Parties communes',
            'Parking',
            'Interphone/Digicode',
            'Autre',
        ];
    }
}