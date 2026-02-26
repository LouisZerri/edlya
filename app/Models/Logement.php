<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Logement extends Model
{
    use HasFactory;

    protected $table = 'logement';

    protected $fillable = [
        'user_id',
        'nom',
        'adresse',
        'code_postal',
        'ville',
        'type',
        'surface',
        'nb_pieces',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function etatsDesLieux(): HasMany
    {
        return $this->hasMany(EtatDesLieux::class);
    }

    public function getAdresseCompleteAttribute(): string
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}";
    }
}