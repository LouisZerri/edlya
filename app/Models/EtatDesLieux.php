<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EtatDesLieux extends Model
{
    use HasFactory;

    protected $table = 'etats_des_lieux';

    protected $fillable = [
        'logement_id',
        'user_id',
        'type',
        'date_realisation',
        'locataire_nom',
        'locataire_email',
        'locataire_telephone',
        'observations_generales',
        'statut',
        'date_signature',
        'signature_bailleur',
        'signature_locataire',
        'date_signature_bailleur',
        'date_signature_locataire',
        'code_validation',
        'code_validation_expire_at',
        'code_validation_verifie_at',
        'signature_ip',
        'signature_user_agent',
        'signature_token',
        'signature_token_expire_at',
    ];

    protected function casts(): array
    {
        return [
            'date_realisation' => 'date',
            'date_signature' => 'datetime',
            'date_signature_bailleur' => 'datetime',
            'date_signature_locataire' => 'datetime',
            'code_validation_expire_at' => 'datetime',
            'code_validation_verifie_at' => 'datetime',
            'signature_token_expire_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($etatDesLieux) {
            // Photos des éléments
            foreach ($etatDesLieux->pieces as $piece) {
                foreach ($piece->elements as $element) {
                    foreach ($element->photos as $photo) {
                        Storage::disk('public')->delete($photo->chemin);
                    }
                }
            }
            
            // Photos des compteurs
            foreach ($etatDesLieux->compteurs as $compteur) {
                if ($compteur->photo) {
                    Storage::disk('public')->delete($compteur->photo);
                }
            }
            
            // Photos des clés
            foreach ($etatDesLieux->cles as $cle) {
                if ($cle->photo) {
                    Storage::disk('public')->delete($cle->photo);
                }
            }
        });
    }

    public function logement(): BelongsTo
    {
        return $this->belongsTo(Logement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(Piece::class)->orderBy('ordre');
    }

    public function partages(): HasMany
    {
        return $this->hasMany(Partage::class);
    }

    public function compteurs(): HasMany
    {
        return $this->hasMany(Compteur::class);
    }

    public function cles(): HasMany
    {
        return $this->hasMany(Cle::class);
    }

    public function getTypeLibelleAttribute(): string
    {
        return $this->type === 'entree' ? 'Entrée' : 'Sortie';
    }

    public function getStatutLibelleAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'signe' => 'Signé',
            default => $this->statut,
        };
    }

    public function getStatutCouleurAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'bg-slate-100 text-slate-600',
            'en_cours' => 'bg-amber-100 text-amber-700',
            'termine' => 'bg-blue-100 text-blue-700',
            'signe' => 'bg-green-100 text-green-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * Générer un code de validation à 6 chiffres
     */
    public function genererCodeValidation(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'code_validation' => $code,
            'code_validation_expire_at' => now()->addMinutes(15),
            'code_validation_verifie_at' => null,
        ]);

        return $code;
    }

    /**
     * Vérifier si le code est valide
     */
    public function verifierCode(string $code): bool
    {
        if ($this->code_validation !== $code) {
            return false;
        }

        if ($this->code_validation_expire_at && $this->code_validation_expire_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Marquer le code comme vérifié
     */
    public function marquerCodeVerifie(): void
    {
        $this->update([
            'code_validation_verifie_at' => now(),
        ]);
    }

    /**
     * Vérifier si le bailleur a signé
     */
    public function baileurASigne(): bool
    {
        return !empty($this->signature_bailleur) && $this->date_signature_bailleur;
    }

    /**
     * Vérifier si le code a été validé
     */
    public function codeEstValide(): bool
    {
        return $this->code_validation_verifie_at !== null;
    }

    /**
     * Vérifier si le locataire a signé
     */
    public function locataireASigne(): bool
    {
        return !empty($this->signature_locataire) && $this->date_signature_locataire;
    }

    /**
     * Générer un token de signature pour le locataire
     */
    public function genererSignatureToken(): string
    {
        $token = Str::random(64);

        $this->update([
            'signature_token' => $token,
            'signature_token_expire_at' => now()->addHours(48),
        ]);

        return $token;
    }

    /**
     * Vérifier si le token de signature est valide
     */
    public function tokenEstValide(?string $token): bool
    {
        if (!$token || $this->signature_token !== $token) {
            return false;
        }

        if ($this->signature_token_expire_at && $this->signature_token_expire_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir l'URL de signature pour le locataire
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (!$this->signature_token) {
            return null;
        }

        return route('signature.locataire', ['token' => $this->signature_token]);
    }

    /**
     * Obtenir l'étape actuelle de signature
     */
    public function getEtapeSignatureAttribute(): int
    {
        // Étape 4 : Terminé (vérifier en premier !)
        if ($this->locataireASigne()) {
            return 4;
        }

        // Étape 1 : Signature bailleur
        if (!$this->baileurASigne()) {
            return 1;
        }

        // Étape 2 : Envoi du lien
        if (!$this->signature_token) {
            return 2;
        }

        // Étape 3 : Attente signature locataire
        return 3;
    }
}
