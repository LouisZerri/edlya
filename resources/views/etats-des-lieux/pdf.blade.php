<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>État des lieux - {{ $etatDesLieux->logement->nom }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1e293b;
            padding: 40px 35px;
        }

        .header {
            text-align: center;
            padding: 15px 0 20px;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #4f46e5;
            margin-bottom: 5px;
        }

        .header p {
            color: #64748b;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-entree {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-sortie {
            background: #ffedd5;
            color: #c2410c;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-box h3 {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-box p {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .info-box strong {
            color: #1e293b;
        }

        /* Compteurs */
        .compteurs-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .compteur-box {
            display: table-cell;
            width: 25%;
            padding: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: top;
        }

        .compteur-box .icon {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .compteur-box .type {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .compteur-box .index {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 5px 0;
        }

        .compteur-box .numero {
            font-size: 8px;
            color: #94a3b8;
        }

        /* Clés */
        .cles-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .cles-table th {
            background: #f1f5f9;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #e2e8f0;
        }

        .cles-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .cles-table .nombre {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        /* Pièces */
        .piece {
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .piece-header {
            background: #f1f5f9;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .piece-header h3 {
            font-size: 12px;
            color: #1e293b;
        }

        .piece-content {
            padding: 10px 15px;
        }

        table.elements {
            width: 100%;
            border-collapse: collapse;
        }

        table.elements th {
            text-align: left;
            padding: 8px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
        }

        table.elements td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
            vertical-align: top;
        }

        .etat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .etat-neuf {
            background: #dcfce7;
            color: #166534;
        }

        .etat-tres_bon {
            background: #d1fae5;
            color: #065f46;
        }

        .etat-bon {
            background: #e0f2fe;
            color: #0369a1;
        }

        .etat-usage {
            background: #fef3c7;
            color: #92400e;
        }

        .etat-mauvais {
            background: #fee2e2;
            color: #dc2626;
        }

        .etat-hors_service {
            background: #f3f4f6;
            color: #374151;
        }

        .degradation-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            font-size: 7px;
            padding: 1px 5px;
            border-radius: 8px;
            margin-right: 2px;
            margin-bottom: 2px;
        }

        .photos-grid {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
        }

        .photos-grid img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .photo-label {
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }

        .observations-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .observations-box h3 {
            font-size: 10px;
            color: #92400e;
            margin-bottom: 5px;
        }

        .observations-box p {
            font-size: 10px;
            color: #78350f;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 20px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .signature-box h4 {
            font-size: 10px;
            margin-bottom: 50px;
        }

        .signature-box p {
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>ÉTAT DES LIEUX {{ $etatDesLieux->type === 'entree' ? "D'ENTRÉE" : 'DE SORTIE' }}</h1>
        <p>{{ $etatDesLieux->logement->adresse_complete }}</p>
        <div style="margin-top: 10px;">
            <span class="badge {{ $etatDesLieux->type === 'entree' ? 'badge-entree' : 'badge-sortie' }}">
                {{ strtoupper($etatDesLieux->type_libelle) }}
            </span>
        </div>
    </div>

    {{-- Informations générales --}}
    <div class="info-grid">
        <div class="info-box">
            <h3>Logement</h3>
            <p><strong>{{ $etatDesLieux->logement->nom }}</strong></p>
            <p>{{ $etatDesLieux->logement->adresse }}</p>
            <p>{{ $etatDesLieux->logement->code_postal }} {{ $etatDesLieux->logement->ville }}</p>
            @if ($etatDesLieux->logement->surface)
                <p>Surface : {{ $etatDesLieux->logement->surface }} m²</p>
            @endif
        </div>
        <div class="info-box">
            <h3>Locataire</h3>
            <p><strong>{{ $etatDesLieux->locataire_nom }}</strong></p>
            @if ($etatDesLieux->locataire_email)
                <p>{{ $etatDesLieux->locataire_email }}</p>
            @endif
            @if ($etatDesLieux->locataire_telephone)
                <p>{{ $etatDesLieux->locataire_telephone }}</p>
            @endif
        </div>
        <div class="info-box">
            <h3>Informations</h3>
            <p>Date : <strong>{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</strong></p>
            <p>Type : <strong>{{ $etatDesLieux->type_libelle }}</strong></p>
            <p>Propriétaire : <strong>{{ $etatDesLieux->user->name }}</strong></p>
        </div>
    </div>

    {{-- Compteurs --}}
    @if ($etatDesLieux->compteurs->isNotEmpty())
        <div class="section">
            <div class="section-title">⚡ Relevé des compteurs</div>
            <div class="compteurs-grid">
                @php
                    $iconsCompteurs = [
                        'electricite' => '⚡',
                        'eau_froide' => '💧',
                        'eau_chaude' => '🔥',
                        'gaz' => '🔵',
                    ];
                @endphp
                @foreach ($etatDesLieux->compteurs as $compteur)
                    <div class="compteur-box">
                        <div class="icon">{{ $iconsCompteurs[$compteur->type] ?? '📊' }}</div>
                        <div class="type">{{ $compteur->type_label }}</div>
                        <div class="index">{{ $compteur->index ?: 'N/R' }}</div>
                        @if ($compteur->numero)
                            <div class="numero">N° {{ $compteur->numero }}</div>
                        @endif
                        @if ($compteur->commentaire)
                            <div class="numero">{{ $compteur->commentaire }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Clés --}}
    @if ($etatDesLieux->cles->isNotEmpty())
        <div class="section">
            <div class="section-title">🔑 Remise des clés</div>
            <table class="cles-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Photo</th>
                        <th style="width: 30%;">Type de clé</th>
                        <th style="width: 15%;">Nombre</th>
                        <th style="width: 40%;">Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($etatDesLieux->cles as $cle)
                        <tr>
                            <td style="text-align: center;">
                                @if ($cle->photo)
                                    <img src="{{ public_path('storage/' . $cle->photo) }}" alt="Clé"
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td>{{ $cle->type }}</td>
                            <td class="nombre">{{ $cle->nombre }}</td>
                            <td>{{ $cle->commentaire ?: '-' }}</td>
                        </tr>
                    @endforeach
                    <tr style="background: #f8fafc;">
                        <td></td>
                        <td><strong>Total</strong></td>
                        <td class="nombre"><strong>{{ $etatDesLieux->cles->sum('nombre') }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    {{-- Observations générales --}}
    @if ($etatDesLieux->observations_generales)
        <div class="observations-box">
            <h3>📝 Observations générales</h3>
            <p>{{ $etatDesLieux->observations_generales }}</p>
        </div>
    @endif

    {{-- Pièces --}}
    @foreach ($etatDesLieux->pieces as $piece)
        @php
            $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->values();
        @endphp

        <div class="piece">
            <div class="piece-header">
                <h3>{{ $piece->nom }}</h3>
            </div>
            <div class="piece-content">
                @if ($piece->elements->isNotEmpty())
                    <table class="elements">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Élément</th>
                                <th style="width: 12%;">Type</th>
                                <th style="width: 12%;">État</th>
                                <th style="width: 28%;">Dégradations</th>
                                <th style="width: 28%;">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($piece->elements as $element)
                                <tr>
                                    <td><strong>{{ $element->nom }}</strong></td>
                                    <td>{{ $element->type_libelle }}</td>
                                    <td>
                                        <span class="etat-badge etat-{{ $element->etat }}">
                                            {{ $element->etat_libelle }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($element->hasDegradations())
                                            @foreach($element->degradations as $degradation)
                                                <span class="degradation-badge">{{ $degradation }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $element->observations ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($allPhotos->isNotEmpty())
                        <div class="photos-grid">
                            @foreach ($allPhotos as $index => $photo)
                                <div style="display: inline-block; text-align: center; margin-right: 5px;">
                                    <img src="{{ public_path('storage/' . $photo->chemin) }}" alt="Photo">
                                    <div class="photo-label">Photo {{ $index + 1 }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p style="color: #94a3b8; font-style: italic;">Aucun élément enregistré</p>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-box">
            <h4>Le Bailleur</h4>
            <p>{{ $etatDesLieux->user->name }}</p>
            <p style="margin-top: 10px;">Date et signature :</p>
        </div>
        <div class="signature-box">
            <h4>Le Locataire</h4>
            <p>{{ $etatDesLieux->locataire_nom }}</p>
            <p style="margin-top: 10px;">Date et signature :</p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} — Edlya</p>
    </div>
</body>

</html>