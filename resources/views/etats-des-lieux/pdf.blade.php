<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>État des lieux - {{ $etatDesLieux->logement->nom }}</title>
    <style>
        @page {
            margin: 20mm 10mm 30mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1a1a1a;
        }

        /* Wrapper principal */
        .content-wrapper {
            padding: 5px 20px 100px 20px;
        }

        /* Paraphes en bas de chaque page */
        .paraphes-footer {
            position: fixed;
            bottom: 25mm;
            left: 10mm;
            right: 10mm;
            height: 60px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
            padding: 5px 10px;
        }

        .paraphes-container {
            display: table;
            width: 100%;
        }

        .paraphe-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: middle;
        }

        .paraphe-box .label {
            font-size: 7px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .paraphe-box img {
            max-height: 28px;
            max-width: 70px;
        }

        .paraphe-box .date {
            font-size: 6px;
            color: #94a3b8;
        }

        /* Footer fixe */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 15mm;
            font-size: 8px;
            border-top: 1px solid #4f46e5;
            background: #fff;
        }

        .footer-brand {
            color: #4f46e5;
            font-weight: bold;
        }

        .footer-sub {
            color: #4f46e5;
        }

        .footer-app {
            color: #666;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
        }

        .header-dates {
            font-size: 10px;
            margin-bottom: 5px;
        }

        .logo-container {
            text-align: right;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 8px;
            margin-top: 10px;
            text-align: left;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            padding-top: 25px;
        }

        .section-title-first {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            padding-top: 0;
        }

        .info-row {
            margin-bottom: 3px;
            font-size: 10px;
        }

        .info-label {
            display: inline-block;
            width: 110px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        table th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px 5px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 5px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        /* Couleurs des états */
        .etat-neuf {
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
        }

        .etat-tres_bon {
            background: #d1fae5;
            color: #065f46;
            font-weight: bold;
        }

        .etat-bon {
            background: #dbeafe;
            color: #1e40af;
            font-weight: bold;
        }

        .etat-usage {
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }

        .etat-mauvais {
            background: #fed7aa;
            color: #c2410c;
            font-weight: bold;
        }

        .etat-hors_service {
            background: #fee2e2;
            color: #dc2626;
            font-weight: bold;
        }

        .photo-ref {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7px;
            margin: 1px;
        }

        .piece-title {
            font-weight: bold;
            font-size: 12px;
            margin: 0 0 10px;
            text-transform: uppercase;
            color: #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 5px;
            padding-top: 25px;
        }

        .piece-title-first {
            font-weight: bold;
            font-size: 12px;
            margin: 0 0 10px;
            text-transform: uppercase;
            color: #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 5px;
            padding-top: 0;
        }

        .page-break {
            page-break-before: always;
        }

        .photos-section {
            margin-top: 15px;
        }

        .photo-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .photo-cell {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
            text-align: center;
        }

        .photo-cell:last-child {
            padding-right: 0;
            padding-left: 2%;
        }

        /* Photo seule - ne pas étaler */
        .photo-cell.single {
            width: 48%;
        }

        .photo-cell.single img {
            max-width: 250px;
        }

        .photo-caption {
            text-align: center;
            font-size: 9px;
            margin-bottom: 5px;
        }

        .photo-caption strong {
            display: block;
        }

        .photo-caption span {
            color: #666;
            font-size: 8px;
        }

        .photo-cell img {
            max-width: 100%;
            max-height: 180px;
            border: 1px solid #ddd;
        }

        /* Encart pas de photo */
        .no-photo-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            margin-top: 20px;
            color: #64748b;
        }

        .no-photo-box .icon {
            font-size: 28px;
            margin-bottom: 10px;
            display: block;
        }

        .no-photo-box .title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .no-photo-box .subtitle {
            font-size: 9px;
        }

        .legal-text {
            font-size: 9px;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 10px;
        }

        .signatures-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .signature-box h4 {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-box p {
            font-size: 9px;
            margin-bottom: 3px;
        }

        .signature-box img {
            max-height: 50px;
            max-width: 150px;
            margin: 10px 0;
        }

        .signature-date {
            font-size: 8px;
            color: #666;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .tracabilite {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            font-size: 8px;
        }
    </style>
</head>

<body>
    {{-- Paraphes en bas de chaque page (position fixed = répété sur chaque page) --}}
    @if($etatDesLieux->statut === 'signe' && $etatDesLieux->signature_bailleur && $etatDesLieux->signature_locataire)
        <div class="paraphes-footer">
            <div class="paraphes-container">
                <div class="paraphe-box">
                    <div class="label">Paraphe bailleur</div>
                    <img src="{{ $etatDesLieux->signature_bailleur }}" alt="Paraphe">
                    <div class="date">{{ $etatDesLieux->date_signature_bailleur->format('d/m/Y') }}</div>
                </div>
                <div class="paraphe-box">
                    <div class="label">Paraphe locataire</div>
                    <img src="{{ $etatDesLieux->signature_locataire }}" alt="Paraphe">
                    <div class="date">{{ $etatDesLieux->date_signature_locataire->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Footer fixe --}}
    <div class="page-footer">
        <span class="footer-brand">EDLYA</span><br>
        <span class="footer-sub">Propulsé par GEST'IMMO</span><br>
        <span class="footer-app">État des lieux réalisé à l'aide de l'application Edlya.</span>
    </div>

    {{-- WRAPPER PRINCIPAL --}}
    <div class="content-wrapper">

        {{-- PAGE 1 : En-tête --}}
        <div class="header-top">
            <div class="header-left">
                <h1>État des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}</h1>
                <p class="header-subtitle">
                    État des lieux contradictoire établi lors de la
                    {{ $etatDesLieux->type === 'entree' ? 'remise' : 'restitution' }} des clés
                    {{ $etatDesLieux->type === 'entree' ? 'en début' : 'en fin' }} de bail.
                </p>
                <div class="header-dates">
                    <strong>Date d'établissement :</strong> {{ $etatDesLieux->date_realisation->format('d/m/Y') }}
                </div>
                @if($etatDesLieux->type === 'sortie' && $edlEntree)
                    <div class="header-dates">
                        Date d'état des lieux d'entrée : {{ $edlEntree->date_realisation->format('d/m/Y') }}
                    </div>
                @endif
            </div>
            <div class="header-right">
                <div class="logo-container">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj4KICAgIDxwYXRoIGQ9Ik01MCAxMCBMODggNDIgTDg4IDg4IEwxMiA4OCBMMTIgNDIgWiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNGY0NmU1IiBzdHJva2Utd2lkdGg9IjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgogICAgPHBhdGggZD0iTTYgNDUgTDUwIDEwIEw5NCA0NSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNGY0NmU1IiBzdHJva2Utd2lkdGg9IjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgogICAgPGVsbGlwc2UgY3g9IjUwIiBjeT0iNTgiIHJ4PSIyMCIgcnk9IjE0IiBmaWxsPSJub25lIiBzdHJva2U9IiM0ZjQ2ZTUiIHN0cm9rZS13aWR0aD0iNCIvPgogICAgPGNpcmNsZSBjeD0iNTAiIGN5PSI1OCIgcj0iNyIgZmlsbD0iIzRmNDZlNSIvPgo8L3N2Zz4=" alt="Edlya" style="width: 55px; height: 55px;">
                </div>
                @if($etatDesLieux->statut === 'signe')
                    <div class="info-box">
                        <strong>Ce document est signé électroniquement.</strong><br>
                        Vérifiez la traçabilité en fin de document.
                    </div>
                @endif
            </div>
        </div>

        {{-- BIEN CONCERNÉ --}}
        <div class="section">
            <div class="section-title-first">Bien concerné</div>
            <div class="info-row">
                <span class="info-label">Type :</span>
                {{ ucfirst($etatDesLieux->logement->type ?? 'Appartement') }}
                @if($etatDesLieux->logement->surface) - {{ $etatDesLieux->logement->surface }} m² @endif
                @if($etatDesLieux->logement->nb_pieces) - {{ $etatDesLieux->logement->nb_pieces }} pièces @endif
            </div>
            <div class="info-row">
                <span class="info-label">Adresse :</span>
                {{ $etatDesLieux->logement->adresse }}, {{ $etatDesLieux->logement->code_postal }} {{ $etatDesLieux->logement->ville }}
            </div>
        </div>

        {{-- LE BAILLEUR --}}
        <div class="section">
            <div class="section-title-first">Le bailleur</div>
            <div class="info-row">
                <span class="info-label">M/Mme :</span>
                {{ $etatDesLieux->user->name }}
            </div>
        </div>

        {{-- LE(S) LOCATAIRE(S) --}}
        <div class="section">
            <div class="section-title-first">Le(s) locataire(s)</div>
            <div class="info-row">
                <span class="info-label">M/Mme :</span>
                {{ $etatDesLieux->locataire_nom }}
            </div>
            <div class="info-row">
                <span class="info-label">Domiciliation :</span>
                {{ $etatDesLieux->logement->adresse }}, {{ $etatDesLieux->logement->code_postal }} {{ $etatDesLieux->logement->ville }}
            </div>
            @if($etatDesLieux->locataire_email)
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    {{ $etatDesLieux->locataire_email }}
                </div>
            @endif
            @if($etatDesLieux->locataire_telephone)
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    {{ $etatDesLieux->locataire_telephone }}
                </div>
            @endif
            @if(!empty($etatDesLieux->autres_locataires))
                <div class="info-row">
                    <span class="info-label">Occupants :</span>
                    {{ implode(', ', $etatDesLieux->autres_locataires) }}
                </div>
            @endif
        </div>

        {{-- RELEVÉ DES COMPTEURS --}}
        @if($etatDesLieux->compteurs->isNotEmpty())
            <div class="page-break">
                <div class="section-title">Relevé des compteurs</div>

                @php
                    $typesCompteurs = [
                        'electricite' => 'ÉLECTRICITÉ',
                        'eau_froide' => 'EAU FROIDE',
                        'eau_chaude' => 'EAU CHAUDE',
                        'gaz' => 'GAZ',
                    ];
                    $globalPhotoIndex = 1;
                    $compteurPhotos = [];
                @endphp

                @foreach($typesCompteurs as $type => $label)
                    @php
                        $compteur = $etatDesLieux->compteurs->where('type', $type)->first();
                        $compteurEntree = $edlEntree?->compteurs->where('type', $type)->first();
                    @endphp

                    @if($compteur)
                        <p style="font-weight: bold; margin: 10px 0 5px;">{{ $label }}</p>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Numéro de compteur</th>
                                    <th style="width: 20%;">Relevé Entrée</th>
                                    <th style="width: 20%;">Relevé Sortie</th>
                                    <th style="width: 35%;">Observation(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="center">{{ $compteur->numero ?? '-' }}</td>
                                    <td class="center">{{ $compteurEntree?->index_value ?? '-' }}</td>
                                    <td class="center">{{ $compteur->index_value ?? 'non relevé' }}</td>
                                    <td>
                                        {{ $compteur->commentaire ?? '' }}
                                        @if($compteur->photos)
                                            @foreach($compteur->photos as $photo)
                                                <span class="photo-ref">Photo {{ $globalPhotoIndex }}</span>
                                                @php
                                                    $compteurPhotos[] = [
                                                        'index' => $globalPhotoIndex,
                                                        'label' => $label,
                                                        'path' => $photo,
                                                    ];
                                                    $globalPhotoIndex++;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                @endforeach

                {{-- Photos compteurs --}}
                @if(count($compteurPhotos) > 0)
                    <div class="photos-section">
                        @foreach(array_chunk($compteurPhotos, 2) as $photoRow)
                            <div class="photo-row">
                                @foreach($photoRow as $photo)
                                    <div class="photo-cell {{ count($photoRow) === 1 ? 'single' : '' }}">
                                        <div class="photo-caption">
                                            <strong>Photo {{ $photo['index'] }} - {{ $photo['label'] }}</strong>
                                            <span>Photo prise lors de l'état des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}</span>
                                        </div>
                                        <img src="{{ public_path('storage/' . $photo['path']) }}" alt="Photo">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-photo-box">
                        <span class="icon">📷</span>
                        <div class="title">Aucune photo de compteur</div>
                        <div class="subtitle">Aucune photo n'a été prise pour les compteurs</div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ÉTAT DES PIÈCES --}}
        @php
            if (!isset($globalPhotoIndex)) {
                $globalPhotoIndex = 1;
            }
        @endphp

        @foreach($etatDesLieux->pieces as $pieceIndex => $piece)
            @php
                $piecePhotos = [];
                $pieceEntree = $edlEntree?->pieces->where('nom', $piece->nom)->first();
            @endphp

            <div class="page-break">
                <p class="{{ $pieceIndex === 0 ? 'piece-title' : 'piece-title' }}">{{ $piece->nom }}</p>

                @if($piece->elements->isNotEmpty())
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 22%;">Élément / Équipement</th>
                                <th style="width: 17%;">État Entrée</th>
                                <th style="width: 17%;">État Sortie</th>
                                <th style="width: 44%;">Observation(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($piece->elements as $element)
                                @php
                                    $elementEntree = $pieceEntree?->elements->where('nom', $element->nom)->first();
                                @endphp
                                <tr>
                                    <td>{{ $element->nom }}</td>
                                    <td class="center etat-{{ $elementEntree?->etat ?? '' }}">
                                        {{ $elementEntree?->etat_libelle ?? '-' }}
                                    </td>
                                    <td class="center etat-{{ $element->etat }}">
                                        {{ $element->etat_libelle }}
                                    </td>
                                    <td>
                                        @if($element->hasDegradations())
                                            {{ implode(', ', $element->degradations) }}
                                        @endif
                                        @if($element->observations)
                                            @if($element->hasDegradations()) - @endif
                                            {{ $element->observations }}
                                        @endif
                                        @if($element->photos->isNotEmpty())
                                            @foreach($element->photos as $photo)
                                                <span class="photo-ref">Photo {{ $globalPhotoIndex }}</span>
                                                @php
                                                    $piecePhotos[] = [
                                                        'index' => $globalPhotoIndex,
                                                        'element' => $element->nom,
                                                        'path' => $photo->chemin,
                                                    ];
                                                    $globalPhotoIndex++;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #666; font-style: italic;">Aucun élément enregistré</p>
                @endif

                {{-- Photos de la pièce ou encart "pas de photo" --}}
                @if(count($piecePhotos) > 0)
                    <div class="photos-section">
                        @foreach(array_chunk($piecePhotos, 2) as $photoRow)
                            <div class="photo-row">
                                @foreach($photoRow as $photo)
                                    <div class="photo-cell {{ count($photoRow) === 1 ? 'single' : '' }}">
                                        <div class="photo-caption">
                                            <strong>Photo {{ $photo['index'] }} - {{ $photo['element'] }}</strong>
                                            <span>Photo prise lors de l'état des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}</span>
                                        </div>
                                        <img src="{{ public_path('storage/' . $photo['path']) }}" alt="Photo">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-photo-box">
                        <span class="icon">📷</span>
                        <div class="title">Aucune photo pour cette pièce</div>
                        <div class="subtitle">Aucune photo n'a été prise pour « {{ $piece->nom }} »</div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- RESTITUTION DES CLÉS --}}
        @if($etatDesLieux->cles->isNotEmpty())
            @php $clePhotos = []; @endphp

            <div class="page-break">
                <div class="section-title">Restitution des clés</div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 30%;">Type de clé</th>
                            <th style="width: 15%;">Nombre Entrée</th>
                            <th style="width: 15%;">Nombre Sortie</th>
                            <th style="width: 40%;">Observation(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($etatDesLieux->cles as $cle)
                            @php
                                $cleEntree = $edlEntree?->cles->where('type', $cle->type)->first();
                            @endphp
                            <tr>
                                <td>{{ $cle->type }}</td>
                                <td class="center">{{ $cleEntree?->nombre ?? '-' }}</td>
                                <td class="center">{{ $cle->nombre }}</td>
                                <td>
                                    {{ $cle->commentaire ?? '' }}
                                    @if($cle->photo)
                                        <span class="photo-ref">Photo {{ $globalPhotoIndex }}</span>
                                        @php
                                            $clePhotos[] = [
                                                'index' => $globalPhotoIndex,
                                                'type' => $cle->type,
                                                'path' => $cle->photo,
                                            ];
                                            $globalPhotoIndex++;
                                        @endphp
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Photos clés --}}
                @if(count($clePhotos) > 0)
                    <div class="photos-section">
                        @foreach(array_chunk($clePhotos, 2) as $photoRow)
                            <div class="photo-row">
                                @foreach($photoRow as $photo)
                                    <div class="photo-cell {{ count($photoRow) === 1 ? 'single' : '' }}">
                                        <div class="photo-caption">
                                            <strong>Photo {{ $photo['index'] }} - {{ $photo['type'] }}</strong>
                                            <span>Photo prise lors de l'état des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : 'de sortie' }}</span>
                                        </div>
                                        <img src="{{ public_path('storage/' . $photo['path']) }}" alt="Photo">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-photo-box">
                        <span class="icon">🔑</span>
                        <div class="title">Aucune photo de clés</div>
                        <div class="subtitle">Aucune photo n'a été prise pour les clés</div>
                    </div>
                @endif
            </div>
        @endif

        {{-- OBSERVATIONS ET CONDITIONS --}}
        <div class="page-break">
            <div class="section-title">Observations générales et réserves</div>

            <p style="font-weight: bold; margin: 10px 0 5px; text-transform: uppercase;">Observations et réserves</p>
            <p style="margin-bottom: 15px;">{{ $etatDesLieux->observations_generales ?? 'Aucune observation ni réserve.' }}</p>

            <p style="font-weight: bold; margin: 10px 0 5px; text-transform: uppercase;">Conditions générales</p>
            <p class="legal-text">
                Conformément à l'article 3 de la Loi n° 89 – 462 du 6 Juillet 1989, un état des lieux doit être établi contradictoirement entre les parties lors de la remise des clés au locataire, et lors de la restitution de celles-ci.
            </p>
            <p class="legal-text">
                Les cosignataires reconnaissent avoir reçu chacun un exemplaire du présent état des lieux et s'accordent pour y faire référence lors du départ du locataire.
            </p>
            <p class="legal-text">
                Le locataire {{ $etatDesLieux->type === 'sortie' ? 'sortant' : 'entrant' }} ou son représentant est informé que les dégradations ou défauts d'entretien constatés sur le présent état des lieux peuvent relever de son éventuelle responsabilité après comparaison avec l'état des lieux d'entrée. Il reconnaît que des indemnités correspondant à des dégradations ou défauts d'entretien seront imputées sur son dépôt de garantie.
            </p>
            <p class="legal-text">
                Le présent état des lieux établi contradictoirement entre les parties qui le reconnaissent exact, fait partie intégrante du contrat de location dont il ne peut être dissocié.
            </p>
            <p class="legal-text" style="margin-top: 15px;">
                <strong>Ainsi fait contradictoirement et signé à {{ $etatDesLieux->logement->ville }}, le {{ $etatDesLieux->date_realisation->format('d/m/Y') }}.</strong>
            </p>
        </div>

        {{-- SIGNATURES --}}
        <div class="signatures-grid">
            <div class="signature-box">
                <h4>Bailleur</h4>
                <p>{{ $etatDesLieux->user->name }}</p>
                @if($etatDesLieux->signature_bailleur)
                    <img src="{{ $etatDesLieux->signature_bailleur }}" alt="Signature">
                    <p class="signature-date">Signé le {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y') }}</p>
                @else
                    <div style="height: 50px; border-bottom: 1px solid #ddd; margin: 20px 0;"></div>
                @endif
            </div>
            <div class="signature-box">
                <h4>Locataire</h4>
                <p>{{ $etatDesLieux->locataire_nom }}</p>
                @if($etatDesLieux->signature_locataire)
                    <img src="{{ $etatDesLieux->signature_locataire }}" alt="Signature">
                    <p class="signature-date">Signé le {{ $etatDesLieux->date_signature_locataire->format('d/m/Y') }}</p>
                @else
                    <div style="height: 50px; border-bottom: 1px solid #ddd; margin: 20px 0;"></div>
                @endif
            </div>
        </div>

        {{-- TRAÇABILITÉ --}}
        @if($etatDesLieux->statut === 'signe' && $etatDesLieux->signature_ip)
            <div class="tracabilite">
                <strong>🔒 Traçabilité de la signature électronique</strong><br><br>
                Document signé conformément aux articles 1366 et 1367 du Code civil.<br><br>
                <strong>Bailleur :</strong> {{ $etatDesLieux->user->name }} - Signé le {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}<br>
                <strong>Locataire :</strong> {{ $etatDesLieux->locataire_nom }} - Signé le {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}<br>
                Email vérifié : {{ $etatDesLieux->locataire_email }}<br>
                @if($etatDesLieux->code_validation_verifie_at)
                    Code validé le : {{ $etatDesLieux->code_validation_verifie_at->format('d/m/Y à H:i') }}<br>
                @endif
                IP : {{ $etatDesLieux->signature_ip }}
            </div>
        @endif

    </div> {{-- Fin content-wrapper --}}

</body>
</html>