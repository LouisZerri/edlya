<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piece extends Model
{
    use HasFactory;

    protected $table = 'piece';

    protected $fillable = [
        'etat_des_lieux_id',
        'nom',
        'ordre',
        'observations',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
        ];
    }

    public function etatDesLieux(): BelongsTo
    {
        return $this->belongsTo(EtatDesLieux::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(Element::class);
    }
}