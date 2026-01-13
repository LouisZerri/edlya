<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de validation</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <tr>
            <td>
                <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    {{-- Logo --}}
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h1 style="color: #4f46e5; font-size: 28px; margin: 0;">Edlya</h1>
                    </div>

                    {{-- Titre --}}
                    <h2 style="color: #1e293b; font-size: 20px; margin-bottom: 20px; text-align: center;">
                        Code de validation pour votre signature
                    </h2>

                    {{-- Message --}}
                    <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
                        Bonjour <strong>{{ $etatDesLieux->locataire_nom }}</strong>,
                    </p>

                    <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
                        Vous êtes sur le point de signer l'état des lieux 
                        <strong>{{ $etatDesLieux->type === 'entree' ? "d'entrée" : "de sortie" }}</strong> 
                        pour le logement situé au :
                    </p>

                    <div style="background: #f1f5f9; border-radius: 8px; padding: 15px; margin-bottom: 30px;">
                        <p style="color: #1e293b; font-size: 14px; margin: 0;">
                            <strong>{{ $etatDesLieux->logement->adresse }}</strong><br>
                            {{ $etatDesLieux->logement->code_postal }} {{ $etatDesLieux->logement->ville }}
                        </p>
                    </div>

                    {{-- Code --}}
                    <div style="text-align: center; margin-bottom: 30px;">
                        <p style="color: #64748b; font-size: 14px; margin-bottom: 10px;">Votre code de validation :</p>
                        <div style="background: #4f46e5; color: white; font-size: 32px; font-weight: bold; letter-spacing: 8px; padding: 20px 40px; border-radius: 8px; display: inline-block;">
                            {{ $code }}
                        </div>
                    </div>

                    {{-- Expiration --}}
                    <p style="color: #f59e0b; font-size: 13px; text-align: center; margin-bottom: 30px;">
                        ⚠️ Ce code expire dans <strong>15 minutes</strong>.
                    </p>

                    {{-- Avertissement --}}
                    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <p style="color: #92400e; font-size: 13px; margin: 0;">
                            Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.
                        </p>
                    </div>

                    {{-- Footer --}}
                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
                    
                    <p style="color: #94a3b8; font-size: 12px; text-align: center; margin: 0;">
                        Cet email a été envoyé automatiquement par Edlya.<br>
                        © {{ date('Y') }} Edlya - Gestion des états des lieux
                    </p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>