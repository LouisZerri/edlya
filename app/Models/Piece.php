<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Piece extends Model
{
    use HasFactory;

    protected $fillable = [
        'etat_des_lieux_id',
        'nom',
        'ordre',
        'observations',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($piece) {
            foreach ($piece->elements as $element) {
                foreach ($element->photos as $photo) {
                    Storage::disk('public')->delete($photo->chemin);
                }
            }
        });
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