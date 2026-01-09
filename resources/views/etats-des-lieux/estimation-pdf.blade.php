<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis réparations - {{ $edlSortie->logement->nom }}</title>
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
            margin-bottom: 25px;
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
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-box h3 {
            font-size: 11px;
            color: #4f46e5;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-box p {
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th {
            background: #4f46e5;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        th:last-child {
            text-align: right;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        td:last-child {
            text-align: right;
            white-space: nowrap;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .totals {
            width: 250px;
            margin-left: auto;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            padding: 8px;
            border: none;
        }
        .totals tr:last-child {
            background: #4f46e5;
            color: white;
            font-weight: bold;
        }
        .totals tr:last-child td {
            padding: 12px 8px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 5px;
        }
        .notes h4 {
            font-size: 10px;
            color: #92400e;
            margin-bottom: 5px;
        }
        .notes p {
            font-size: 9px;
            color: #78350f;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            text-align: center;
        }
        .signature-box p {
            margin-bottom: 50px;
            font-size: 10px;
            color: #475569;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>DEVIS DE RÉPARATIONS</h1>
        <p>État des lieux de sortie — {{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
    </div>

    {{-- Infos --}}
    <div class="info-section">
        <div class="info-box">
            <h3>Logement</h3>
            <p><strong>{{ $edlSortie->logement->nom }}</strong></p>
            <p>{{ $edlSortie->logement->adresse }}</p>
            <p>{{ $edlSortie->logement->code_postal }} {{ $edlSortie->logement->ville }}</p>
        </div>
        <div class="info-box">
            <h3>Locataire</h3>
            <p><strong>{{ $edlSortie->locataire_nom }}</strong></p>
            @if($edlSortie->locataire_email)
                <p>{{ $edlSortie->locataire_email }}</p>
            @endif
            @if($edlSortie->locataire_telephone)
                <p>{{ $edlSortie->locataire_telephone }}</p>
            @endif
        </div>
    </div>

    {{-- Tableau --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Pièce</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%;">Qté</th>
                <th style="width: 10%;">Unité</th>
                <th style="width: 10%;">P.U. HT</th>
                <th style="width: 10%;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lignes as $ligne)
                <tr>
                    <td>{{ $ligne['piece'] }}</td>
                    <td>{{ $ligne['description'] }}</td>
                    <td>{{ number_format($ligne['quantite'], 2, ',', ' ') }}</td>
                    <td>{{ $ligne['unite'] === 'm2' ? 'm²' : $ligne['unite'] }}</td>
                    <td>{{ number_format($ligne['prix_unitaire'], 2, ',', ' ') }} €</td>
                    <td>{{ number_format($ligne['montant'], 2, ',', ' ') }} €</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8;">Aucune ligne</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totals">
        <table>
            <tr>
                <td>Total HT</td>
                <td>{{ number_format($totalHT, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td>TVA (20%)</td>
                <td>{{ number_format($tva, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td>Total TTC</td>
                <td>{{ number_format($totalTTC, 2, ',', ' ') }} €</td>
            </tr>
        </table>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <h4>Conditions</h4>
        <p>Ce devis est établi sur la base des dégradations constatées lors de l'état des lieux de sortie. Les montants sont indicatifs et peuvent être ajustés après évaluation détaillée par un professionnel. Validité du devis : 30 jours.</p>
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-box">
            <p>Le bailleur / L'agent</p>
            <div class="signature-line">Date et signature</div>
        </div>
        <div class="signature-box">
            <p>Le locataire</p>
            <div class="signature-line">Date et signature</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} — Edlya, propulsé par GEST'IMMO</p>
    </div>
</body>
</html>