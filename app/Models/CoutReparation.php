<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoutReparation extends Model
{
    protected $table = 'couts_reparations';

    protected $fillable = [
        'type_element',
        'nom',
        'description',
        'unite',
        'prix_unitaire',
        'actif',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParType($query, string $type)
    {
        return $query->where('type_element', $type);
    }

    public function getPrixFormatAttribute(): string
    {
        return number_format($this->prix_unitaire, 2, ',', ' ') . ' €';
    }

    public function getUniteLibelleAttribute(): string
    {
        return match($this->unite) {
            'm2' => 'm²',
            'ml' => 'ml',
            'unite' => 'unité',
            'forfait' => 'forfait',
            default => $this->unite,
        };
    }
}