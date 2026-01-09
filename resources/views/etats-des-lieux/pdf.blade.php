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
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
        }

        .wrapper {
            padding: 40px 35px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
        }

        .header h1 {
            font-size: 22px;
            color: #4f46e5;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
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
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-grid td {
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }

        .info-label {
            color: #64748b;
            font-size: 10px;
            width: 120px;
        }

        .info-value {
            color: #1e293b;
        }

        .piece {
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .piece-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #e2e8f0;
        }

        .piece-content {
            padding: 10px 15px;
        }

        .element-table {
            width: 100%;
            border-collapse: collapse;
        }

        .element-table th {
            text-align: left;
            padding: 8px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            color: #64748b;
        }

        .element-table td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .etat {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
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

        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signatures-grid {
            width: 100%;
        }

        .signatures-grid td {
            width: 50%;
            padding: 20px;
            vertical-align: top;
        }

        .signature-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            min-height: 120px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 10px;
        }

        .signature-name {
            margin-bottom: 10px;
        }

        .signature-date {
            font-size: 10px;
            color: #64748b;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .observations {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{-- En-tête --}}
        <div class="header">
            <h1>Edlya</h1>
            <p>Propulsé par GEST'IMMO</p>
            <div class="badge badge-{{ $etatDesLieux->type }}">
                État des lieux {{ $etatDesLieux->type === 'entree' ? 'd\'entrée' : 'de sortie' }}
            </div>
        </div>

        {{-- Informations du logement --}}
        <div class="section">
            <div class="section-title">Logement</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Adresse</td>
                    <td class="info-value">{{ $etatDesLieux->logement->adresse_complete }}</td>
                </tr>
                <tr>
                    <td class="info-label">Type de bien</td>
                    <td class="info-value">{{ ucfirst(str_replace('_', ' ', $etatDesLieux->logement->type)) }}</td>
                </tr>
                @if($etatDesLieux->logement->surface)
                    <tr>
                        <td class="info-label">Surface</td>
                        <td class="info-value">{{ $etatDesLieux->logement->surface }} m²</td>
                    </tr>
                @endif
                @if($etatDesLieux->logement->nb_pieces)
                    <tr>
                        <td class="info-label">Nombre de pièces</td>
                        <td class="info-value">{{ $etatDesLieux->logement->nb_pieces }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Informations des parties --}}
        <div class="section">
            <div class="section-title">Parties</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Bailleur / Agent</td>
                    <td class="info-value">{{ $etatDesLieux->user->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Locataire</td>
                    <td class="info-value">{{ $etatDesLieux->locataire_nom }}</td>
                </tr>
                @if($etatDesLieux->locataire_email)
                    <tr>
                        <td class="info-label">Email locataire</td>
                        <td class="info-value">{{ $etatDesLieux->locataire_email }}</td>
                    </tr>
                @endif
                @if($etatDesLieux->locataire_telephone)
                    <tr>
                        <td class="info-label">Tél. locataire</td>
                        <td class="info-value">{{ $etatDesLieux->locataire_telephone }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="info-label">Date de réalisation</td>
                    <td class="info-value">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        {{-- Observations générales --}}
        @if($etatDesLieux->observations_generales)
            <div class="section">
                <div class="section-title">Observations générales</div>
                <div class="observations">{{ $etatDesLieux->observations_generales }}</div>
            </div>
        @endif

        {{-- Détail des pièces --}}
        <div class="section">
            <div class="section-title">Détail des pièces</div>

            @forelse($etatDesLieux->pieces as $piece)
                <div class="piece">
                    <div class="piece-header">{{ $piece->nom }}</div>
                    <div class="piece-content">
                        @if($piece->elements->isNotEmpty())
                            <table class="element-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Type</th>
                                        <th style="width: 30%;">Élément</th>
                                        <th style="width: 15%;">État</th>
                                        <th style="width: 30%;">Observations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($piece->elements as $element)
                                        <tr>
                                            <td>{{ ucfirst($element->type) }}</td>
                                            <td>{{ $element->nom }}</td>
                                            <td><span class="etat etat-{{ $element->etat }}">{{ $element->etat_libelle }}</span></td>
                                            <td>{{ $element->observations ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Photos de la pièce --}}
                            @php
                                $photosCount = $piece->elements->sum(fn($e) => $e->photos->count());
                            @endphp

                            @if($photosCount > 0)
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                                    <p style="font-size: 10px; color: #64748b; margin-bottom: 10px; font-weight: bold;">Photos</p>
                                    @foreach($piece->elements as $element)
                                        @if($element->photos->isNotEmpty())
                                            <div style="margin-bottom: 10px;">
                                                <p style="font-size: 9px; color: #64748b; margin-bottom: 5px;">{{ $element->nom }}</p>
                                                <div>
                                                    @foreach($element->photos as $photo)
                                                        <img src="{{ public_path('storage/' . $photo->chemin) }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; display: inline-block; margin-right: 8px; margin-bottom: 8px; border: 1px solid #e2e8f0;">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p style="color: #94a3b8; font-style: italic;">Aucun élément renseigné.</p>
                        @endif

                        @if($piece->observations)
                            <div class="observations" style="margin-top: 10px;">
                                {{ $piece->observations }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p style="color: #94a3b8; font-style: italic;">Aucune pièce renseignée.</p>
            @endforelse
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="section-title">Signatures</div>
            <table class="signatures-grid">
                <tr>
                    <td>
                        <div class="signature-box">
                            <div class="signature-title">Le bailleur / Agent</div>
                            <div class="signature-name">{{ $etatDesLieux->user->name }}</div>
                            @if($etatDesLieux->signature_bailleur)
                                <img src="{{ $etatDesLieux->signature_bailleur }}" style="max-height: 60px; margin: 10px 0;">
                                <div class="signature-date">Signé le {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</div>
                            @else
                                <div style="height: 60px;"></div>
                                <div class="signature-date">Date : _______________</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="signature-box">
                            <div class="signature-title">Le locataire</div>
                            <div class="signature-name">{{ $etatDesLieux->locataire_nom }}</div>
                            @if($etatDesLieux->signature_locataire)
                                <img src="{{ $etatDesLieux->signature_locataire }}" style="max-height: 60px; margin: 10px 0;">
                                <div class="signature-date">Signé le {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</div>
                            @else
                                <div style="height: 60px;"></div>
                                <div class="signature-date">Date : _______________</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Pied de page --}}
        <div class="footer">
            Document généré le {{ now()->format('d/m/Y à H:i') }} via Edlya - Propulsé par GEST'IMMO
        </div>
    </div>
</body>
</html>