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
            line-height: 1.3;
            color: #1e293b;
        }

        .page-wrapper {
            padding: 40px 45px;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
        }

        .header h1 {
            font-size: 20px;
            color: #4f46e5;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 9px;
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 8px;
        }

        .badge-entree {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .badge-sortie {
            background-color: #ffedd5;
            color: #c2410c;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-grid {
            width: 100%;
        }

        .info-grid td {
            padding: 2px 8px 2px 0;
            vertical-align: top;
        }

        .info-label {
            color: #64748b;
            font-size: 9px;
            width: 100px;
        }

        .info-value {
            color: #1e293b;
            font-size: 10px;
        }

        .two-columns {
            width: 100%;
        }

        .two-columns td {
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .piece-header {
            background-color: #4f46e5;
            color: white;
            padding: 12px 18px;
            font-weight: bold;
            font-size: 15px;
            border-radius: 6px 6px 0 0;
        }

        .piece-content {
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 6px 6px;
            padding: 20px;
        }

        .element-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .element-table th {
            text-align: left;
            padding: 8px 6px;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .element-table td {
            padding: 10px 6px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            font-size: 10px;
        }

        .etat {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .etat-neuf {
            background-color: #dcfce7;
            color: #166534;
        }

        .etat-tres_bon {
            background-color: #d1fae5;
            color: #065f46;
        }

        .etat-bon {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .etat-usage {
            background-color: #fef3c7;
            color: #b45309;
        }

        .etat-mauvais {
            background-color: #ffedd5;
            color: #c2410c;
        }

        .etat-hors_service {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .photos-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .photos-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .photos-grid {
            width: 100%;
        }

        .photos-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 8px;
        }

        .photo-item img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
        }

        .photo-label {
            font-size: 11px;
            color: #374151;
            margin-top: 6px;
            font-weight: bold;
        }

        .signatures-grid {
            width: 100%;
        }

        .signatures-grid td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .signature-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            min-height: 80px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 9px;
        }

        .signature-name {
            margin-bottom: 5px;
            font-size: 10px;
        }

        .signature-date {
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .observations {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            font-style: italic;
            font-size: 10px;
        }

        .mini-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
        }

        .mini-header-right {
            float: right;
            color: #4f46e5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    {{-- Page 1 : Informations générales --}}
    <div class="page-wrapper page-break">
        <div class="header">
            <h1>Edlya</h1>
            <p>Propulsé par GEST'IMMO</p>
            <div class="badge badge-{{ $etatDesLieux->type }}">
                État des lieux {{ $etatDesLieux->type === 'entree' ? 'd\'entrée' : 'de sortie' }}
            </div>
        </div>

        {{-- Deux colonnes : Logement et Parties --}}
        <table class="two-columns">
            <tr>
                <td>
                    <div class="section">
                        <div class="section-title">Logement</div>
                        <table class="info-grid">
                            <tr>
                                <td class="info-label">Adresse</td>
                                <td class="info-value">{{ $etatDesLieux->logement->adresse_complete }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Type</td>
                                <td class="info-value">
                                    {{ ucfirst(str_replace('_', ' ', $etatDesLieux->logement->type)) }}</td>
                            </tr>
                            @if ($etatDesLieux->logement->surface)
                                <tr>
                                    <td class="info-label">Surface</td>
                                    <td class="info-value">{{ $etatDesLieux->logement->surface }} m²</td>
                                </tr>
                            @endif
                            @if ($etatDesLieux->logement->nb_pieces)
                                <tr>
                                    <td class="info-label">Pièces</td>
                                    <td class="info-value">{{ $etatDesLieux->logement->nb_pieces }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </td>
                <td>
                    <div class="section">
                        <div class="section-title">Parties</div>
                        <table class="info-grid">
                            <tr>
                                <td class="info-label">Bailleur</td>
                                <td class="info-value">{{ $etatDesLieux->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Locataire</td>
                                <td class="info-value">{{ $etatDesLieux->locataire_nom }}</td>
                            </tr>
                            @if ($etatDesLieux->locataire_email)
                                <tr>
                                    <td class="info-label">Email</td>
                                    <td class="info-value">{{ $etatDesLieux->locataire_email }}</td>
                                </tr>
                            @endif
                            @if ($etatDesLieux->locataire_telephone)
                                <tr>
                                    <td class="info-label">Téléphone</td>
                                    <td class="info-value">{{ $etatDesLieux->locataire_telephone }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="info-label">Date</td>
                                <td class="info-value">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Observations générales --}}
        @if ($etatDesLieux->observations_generales)
            <div class="section">
                <div class="section-title">Observations générales</div>
                <div class="observations">{{ $etatDesLieux->observations_generales }}</div>
            </div>
        @endif

        {{-- Sommaire des pièces --}}
        <div class="section">
            <div class="section-title">Sommaire des pièces</div>
            <table class="info-grid">
                @foreach ($etatDesLieux->pieces as $index => $piece)
                    @php
                        $photosCount = $piece->elements->sum(fn($e) => $e->photos->count());
                    @endphp
                    <tr>
                        <td class="info-value">{{ $index + 1 }}. {{ $piece->nom }}</td>
                        <td style="text-align: right; color: #64748b; font-size: 9px;">{{ $piece->elements->count() }}
                            élément(s) · {{ $photosCount }} photo(s)</td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Signatures --}}
        <div class="section">
            <div class="section-title">Signatures</div>
            <table class="signatures-grid">
                <tr>
                    <td>
                        <div class="signature-box">
                            <div class="signature-title">Le bailleur / Agent</div>
                            <div class="signature-name">{{ $etatDesLieux->user->name }}</div>
                            @if ($etatDesLieux->signature_bailleur && !str_contains($etatDesLieux->signature_bailleur, 'placeholder'))
                                <div style="margin: 8px 0;">
                                    <img src="{{ $etatDesLieux->signature_bailleur }}"
                                        style="max-height: 50px; max-width: 150px;">
                                </div>
                                <div class="signature-date">Signé le
                                    {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</div>
                            @elseif($etatDesLieux->signature_bailleur)
                                <div style="height: 40px; display: flex; align-items: center;">
                                    <span style="color: #22c55e; font-size: 9px;">✓ Signé électroniquement</span>
                                </div>
                                <div class="signature-date">Signé le
                                    {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</div>
                            @else
                                <div style="height: 50px;"></div>
                                <div class="signature-date">Date : _______________</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="signature-box">
                            <div class="signature-title">Le locataire</div>
                            <div class="signature-name">{{ $etatDesLieux->locataire_nom }}</div>
                            @if ($etatDesLieux->signature_locataire && !str_contains($etatDesLieux->signature_locataire, 'placeholder'))
                                <div style="margin: 8px 0;">
                                    <img src="{{ $etatDesLieux->signature_locataire }}"
                                        style="max-height: 50px; max-width: 150px;">
                                </div>
                                <div class="signature-date">Signé le
                                    {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</div>
                            @elseif($etatDesLieux->signature_locataire)
                                <div style="height: 40px; display: flex; align-items: center;">
                                    <span style="color: #22c55e; font-size: 9px;">✓ Signé électroniquement</span>
                                </div>
                                <div class="signature-date">Signé le
                                    {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</div>
                            @else
                                <div style="height: 50px;"></div>
                                <div class="signature-date">Date : _______________</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Document généré le {{ now()->format('d/m/Y à H:i') }} via Edlya - Propulsé par GEST'IMMO
        </div>
    </div>

    {{-- Pages des pièces : une page par pièce --}}
    @foreach ($etatDesLieux->pieces as $pieceIndex => $piece)
        @php
            $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->values();
            $isLast = $loop->last;
        @endphp

        <div class="page-wrapper {{ !$isLast ? 'page-break' : '' }}">
            {{-- Mini header --}}
            <div class="mini-header">
                <span class="mini-header-right">Page {{ $pieceIndex + 2 }}</span>
                {{ $etatDesLieux->logement->nom }} - {{ $etatDesLieux->type === 'entree' ? 'Entrée' : 'Sortie' }} du
                {{ $etatDesLieux->date_realisation->format('d/m/Y') }}
            </div>

            {{-- En-tête de la pièce --}}
            <div class="piece-header">
                {{ $piece->nom }}
                <span style="font-weight: normal; font-size: 11px; margin-left: 10px;">
                    ({{ $piece->elements->count() }} élément(s) · {{ $allPhotos->count() }} photo(s))
                </span>
            </div>

            <div class="piece-content">
                @if ($piece->elements->isNotEmpty())
                    {{-- Tableau des éléments --}}
                    <table class="element-table">
                        <thead>
                            <tr>
                                <th style="width: 14%;">Type</th>
                                <th style="width: 22%;">Élément</th>
                                <th style="width: 12%;">État</th>
                                <th style="width: 52%;">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($piece->elements as $element)
                                <tr>
                                    <td>{{ ucfirst($element->type) }}</td>
                                    <td><strong>{{ $element->nom }}</strong></td>
                                    <td><span
                                            class="etat etat-{{ $element->etat }}">{{ $element->etat_libelle }}</span>
                                    </td>
                                    <td>{{ $element->observations ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Photos en grille 3 colonnes --}}
                    @if ($allPhotos->isNotEmpty())
                        <div class="photos-section">
                            <div class="photos-title">Photos ({{ $allPhotos->count() }})</div>
                            <table class="photos-grid">
                                @foreach ($allPhotos->chunk(3) as $photoRow)
                                    <tr>
                                        @foreach ($photoRow as $index => $photo)
                                            <td>
                                                <div class="photo-item">
                                                    <img src="{{ public_path('storage/' . $photo->chemin) }}"
                                                        alt="Photo {{ $loop->parent->index * 3 + $index + 1 }}">
                                                    <p class="photo-label">Photo
                                                        {{ $loop->parent->index * 3 + $index + 1 }}</p>
                                                </div>
                                            </td>
                                        @endforeach
                                        {{-- Cellules vides pour compléter la ligne --}}
                                        @for ($i = $photoRow->count(); $i < 3; $i++)
                                            <td></td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                @else
                    <p style="color: #94a3b8; font-style: italic; padding: 20px 0;">Aucun élément renseigné pour cette
                        pièce.</p>
                @endif

                @if ($piece->observations)
                    <div class="observations" style="margin-top: 15px;">
                        <strong>Observations :</strong> {{ $piece->observations }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>
