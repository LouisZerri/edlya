<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des lieux</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #4f46e5; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Edlya</h1>
        <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0; font-size: 14px;">Propulsé par GEST'IMMO</p>
    </div>

    <div style="background: #f8fafc; padding: 30px; border: 1px solid #e2e8f0; border-top: none;">
        <h2 style="color: #1e293b; margin-top: 0;">
            État des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}
        </h2>

        <p>Bonjour {{ $etatDesLieux->locataire_nom }},</p>

        <p>
            Vous trouverez ci-dessous le lien pour consulter l'état des lieux
            {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}
            du logement <strong>{{ $etatDesLieux->logement->nom }}</strong>.
        </p>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Logement</p>
            <p style="margin: 0 0 15px; font-weight: bold;">{{ $etatDesLieux->logement->adresse_complete }}</p>

            <p style="margin: 0 0 5px; color: #64748b; font-size: 14px;">Date de réalisation</p>
            <p style="margin: 0; font-weight: bold;">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $partage->url }}"
                style="display: inline-block; background: #4f46e5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;">
                Consulter l'état des lieux
            </a>
        </div>

        <p style="color: #64748b; font-size: 14px;">
            Ce lien est valable jusqu'au <strong>{{ $partage->expire_at->format('d/m/Y à H:i') }}</strong>.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">

        <p style="color: #64748b; font-size: 12px; margin-bottom: 0;">
            Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.
        </p>
    </div>

    <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 12px;">
        <p style="margin: 0;">© {{ date('Y') }} GEST'IMMO — Tous droits réservés</p>
    </div>
</body>

</html>
