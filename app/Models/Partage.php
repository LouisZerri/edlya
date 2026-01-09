<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Partage extends Model
{
    protected $fillable = [
        'etat_des_lieux_id',
        'token',
        'email',
        'type',
        'expire_at',
        'consulte_at',
    ];

    protected $casts = [
        'expire_at' => 'datetime',
        'consulte_at' => 'datetime',
    ];

    public function etatDesLieux(): BelongsTo
    {
        return $this->belongsTo(EtatDesLieux::class);
    }

    public function isExpired(): bool
    {
        return $this->expire_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function markAsConsulted(): void
    {
        if (!$this->consulte_at) {
            $this->update(['consulte_at' => now()]);
        }
    }

    public function getUrlAttribute(): string
    {
        return route('partage.show', $this->token);
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (self::where('token', $token)->exists());

        return $token;
    }
}