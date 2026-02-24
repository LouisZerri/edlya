<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Politique de confidentialité - Edlya</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

        {{-- En-tête --}}
        <div class="text-center mb-10">
            <a href="/" class="inline-flex flex-col items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-12 h-12">
                    <path d="M50 10 L88 42 L88 88 L12 88 L12 42 Z" fill="none" stroke="#4f46e5" stroke-width="5"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6 45 L50 10 L94 45" fill="none" stroke="#4f46e5" stroke-width="5"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <ellipse cx="50" cy="58" rx="20" ry="14" fill="none" stroke="#4f46e5" stroke-width="4" />
                    <circle cx="50" cy="58" r="7" fill="#4f46e5" />
                </svg>
                <span class="text-xl font-bold text-primary-600 mt-1">Edlya</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Politique de confidentialité</h1>
            <p class="text-sm text-slate-500 mt-2">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
        </div>

        {{-- Contenu --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-8 text-slate-700 leading-relaxed">

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">1. Responsable du traitement</h2>
                <p>
                    Le responsable du traitement des données personnelles collectées via l'application
                    <strong>Edlya</strong> est la société <strong>GEST'IMMO</strong>,
                    SARL au capital de 1 000 €, immatriculée sous le numéro SIRET 990 877 417 00016
                    (RCS Brive B 990 877 417),
                    dont le siège social est situé au 35 rue Aliénor d'Aquitaine, 19360 Malemort.
                </p>
                <p class="mt-2">
                    N° TVA intracommunautaire : FR42 990 877 417
                </p>
                <p class="mt-2">
                    Contact : <a href="mailto:contact@gestimmo-presta.fr" class="text-primary-600 hover:underline">contact@gestimmo-presta.fr</a>
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">2. Données collectées</h2>
                <p>Dans le cadre de l'utilisation d'Edlya, nous collectons les données suivantes :</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>Données d'identification</strong> : nom, prénom, adresse email</li>
                    <li><strong>Données de connexion</strong> : adresse IP, logs de connexion</li>
                    <li><strong>Données relatives aux logements</strong> : adresse, description, photos</li>
                    <li><strong>Données relatives aux états des lieux</strong> : observations, photos, signatures</li>
                    <li><strong>Données des locataires</strong> : nom, prénom, numéro de téléphone (pour la signature électronique)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">3. Finalités du traitement</h2>
                <p>Les données collectées sont utilisées pour :</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>La création et la gestion de votre compte utilisateur</li>
                    <li>La réalisation et la gestion des états des lieux</li>
                    <li>La gestion des logements et de leur suivi</li>
                    <li>La génération de documents PDF (états des lieux)</li>
                    <li>La signature électronique des états des lieux</li>
                    <li>L'envoi de notifications liées au service (codes de vérification)</li>
                    <li>L'amélioration du service via l'analyse d'usage</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">4. Base légale</h2>
                <p>Le traitement des données repose sur :</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>L'exécution du contrat</strong> : le traitement est nécessaire à la fourniture du service</li>
                    <li><strong>L'intérêt légitime</strong> : amélioration et sécurisation du service</li>
                    <li><strong>Le consentement</strong> : pour les traitements optionnels (analyse IA des photos)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">5. Destinataires des données</h2>
                <p>
                    Les données sont destinées uniquement au personnel habilité de GEST'IMMO.
                    Elles ne sont ni vendues, ni cédées à des tiers à des fins commerciales.
                </p>
                <p class="mt-2">
                    Certaines données peuvent être transmises à des sous-traitants techniques
                    (hébergement, envoi de SMS) dans le strict cadre de la fourniture du service,
                    conformément au RGPD.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">6. Durée de conservation</h2>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Données de compte</strong> : conservées pendant la durée de la relation contractuelle, puis 3 ans après la dernière activité</li>
                    <li><strong>États des lieux et documents</strong> : conservés pendant la durée légale applicable (durée du bail + prescription)</li>
                    <li><strong>Logs de connexion</strong> : 12 mois</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">7. Sécurité des données</h2>
                <p>
                    Nous mettons en place des mesures techniques et organisationnelles appropriées
                    pour protéger vos données personnelles contre tout accès non autorisé,
                    modification, divulgation ou destruction. Ces mesures incluent notamment :
                </p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Le chiffrement des communications (HTTPS/TLS)</li>
                    <li>Le hachage des mots de passe</li>
                    <li>La limitation des accès aux données au personnel autorisé</li>
                    <li>Des sauvegardes régulières</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">8. Vos droits</h2>
                <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>Droit d'accès</strong> : obtenir une copie de vos données personnelles</li>
                    <li><strong>Droit de rectification</strong> : corriger vos données inexactes ou incomplètes</li>
                    <li><strong>Droit à l'effacement</strong> : demander la suppression de vos données</li>
                    <li><strong>Droit à la limitation</strong> : restreindre le traitement de vos données</li>
                    <li><strong>Droit à la portabilité</strong> : recevoir vos données dans un format structuré</li>
                    <li><strong>Droit d'opposition</strong> : vous opposer au traitement de vos données</li>
                </ul>
                <p class="mt-2">
                    Pour exercer ces droits, contactez-nous à :
                    <a href="mailto:contact@gestimmo-presta.fr" class="text-primary-600 hover:underline">contact@gestimmo-presta.fr</a>.
                    Nous nous engageons à répondre dans un délai d'un mois.
                </p>
                <p class="mt-2">
                    Vous pouvez également introduire une réclamation auprès de la
                    <strong>CNIL</strong> (Commission Nationale de l'Informatique et des Libertés).
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">9. Cookies</h2>
                <p>
                    Edlya utilise uniquement des cookies strictement nécessaires au fonctionnement
                    du service (cookie de session, jeton CSRF). Aucun cookie de traçage publicitaire
                    n'est utilisé.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">10. Modifications</h2>
                <p>
                    La présente politique de confidentialité peut être modifiée à tout moment.
                    En cas de modification substantielle, les utilisateurs seront informés
                    via l'application. La date de dernière mise à jour est indiquée en haut de cette page.
                </p>
            </section>

        </div>

        {{-- Retour --}}
        <div class="text-center mt-8">
            <a href="/" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
                &larr; Retour à l'accueil
            </a>
        </div>

    </div>

</body>

</html>
