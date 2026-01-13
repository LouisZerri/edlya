<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Comparatif - {{ $edlSortie->logement->nom }}</title>
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
            border-bottom: 2px solid #f59e0b;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #f59e0b;
            margin-bottom: 5px;
        }

        .header p {
            color: #64748b;
            font-size: 11px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .info-box.entree {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .info-box.sortie {
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }

        .info-box h3 {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .info-box.entree h3 {
            color: #1d4ed8;
        }

        .info-box.sortie h3 {
            color: #c2410c;
        }

        .info-box p {
            font-size: 10px;
            margin-bottom: 3px;
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

        .compteurs-table,
        .cles-table,
        .elements-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .compteurs-table th,
        .cles-table th,
        .elements-table th {
            padding: 6px;
            text-align: left;
            font-size: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .compteurs-table td,
        .cles-table td,
        .elements-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: top;
        }

        .bg-blue {
            background: #eff6ff;
        }

        .bg-orange {
            background: #fff7ed;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-red {
            color: #dc2626;
        }

        .text-green {
            color: #16a34a;
        }

        .warning-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .warning-box p {
            color: #dc2626;
            font-size: 10px;
        }

        .degradation-row {
            background: #fef2f2;
        }

        .etat-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
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
            font-size: 6px;
            padding: 1px 4px;
            border-radius: 6px;
            margin-right: 2px;
            margin-bottom: 2px;
        }

        .degradation-badge-blue {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 6px;
            padding: 1px 4px;
            border-radius: 6px;
            margin-right: 2px;
            margin-bottom: 2px;
        }

        .degradation-badge-orange {
            display: inline-block;
            background: #fed7aa;
            color: #c2410c;
            font-size: 6px;
            padding: 1px 4px;
            border-radius: 6px;
            margin-right: 2px;
            margin-bottom: 2px;
        }

        .degradation-badge-new {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            font-size: 6px;
            padding: 1px 4px;
            border-radius: 6px;
            margin-right: 2px;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .piece {
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .piece-header {
            background: #f1f5f9;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .piece-header.degradation {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .piece-header h3 {
            font-size: 12px;
            color: #1e293b;
        }

        .summary-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .summary-box.danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .summary-box.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .summary-box h3 {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .summary-box.danger h3 {
            color: #dc2626;
        }

        .summary-box.success h3 {
            color: #16a34a;
        }

        .legend {
            font-size: 8px;
            color: #dc2626;
            margin-top: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>COMPARATIF ENTRÉE / SORTIE</h1>
        <p>{{ $edlSortie->logement->adresse_complete }}</p>
    </div>

    <div class="info-grid">
        <div class="info-box entree">
            <h3>📥 ÉTAT DES LIEUX D'ENTRÉE</h3>
            <p><strong>Date :</strong> {{ $edlEntree->date_realisation->format('d/m/Y') }}</p>
            <p><strong>Locataire :</strong> {{ $edlEntree->locataire_nom }}</p>
        </div>
        <div class="info-box sortie">
            <h3>📤 ÉTAT DES LIEUX DE SORTIE</h3>
            <p><strong>Date :</strong> {{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
            <p><strong>Locataire :</strong> {{ $edlSortie->locataire_nom }}</p>
        </div>
    </div>

    {{-- Compteurs --}}
    @if ($edlEntree->compteurs->isNotEmpty() || $edlSortie->compteurs->isNotEmpty())
        <div class="section">
            <div class="section-title">⚡ Comparatif des compteurs</div>
            <table class="compteurs-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Type</th>
                        <th style="width: 20%;">N° Compteur</th>
                        <th class="text-center bg-blue" style="width: 20%;">Index entrée</th>
                        <th class="text-center bg-orange" style="width: 20%;">Index sortie</th>
                        <th class="text-center" style="width: 15%;">Consommation</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typesCompteurs = [
                            'electricite' => '⚡ Électricité',
                            'eau_froide' => '💧 Eau froide',
                            'eau_chaude' => '🔥 Eau chaude',
                            'gaz' => '🔵 Gaz',
                        ];
                        $unites = ['electricite' => 'kWh', 'eau_froide' => 'm³', 'eau_chaude' => 'm³', 'gaz' => 'm³'];
                    @endphp
                    @foreach ($typesCompteurs as $type => $label)
                        @php
                            $compteurEntree = $edlEntree->compteurs->where('type', $type)->first();
                            $compteurSortie = $edlSortie->compteurs->where('type', $type)->first();
                            $indexEntree = $compteurEntree?->index
                                ? (float) preg_replace('/[^0-9.]/', '', $compteurEntree->index)
                                : null;
                            $indexSortie = $compteurSortie?->index
                                ? (float) preg_replace('/[^0-9.]/', '', $compteurSortie->index)
                                : null;
                            $consommation =
                                $indexEntree !== null && $indexSortie !== null ? $indexSortie - $indexEntree : null;
                        @endphp
                        @if ($compteurEntree || $compteurSortie)
                            <tr>
                                <td class="font-bold">{{ $label }}</td>
                                <td style="font-size: 8px;">
                                    {{ $compteurEntree?->numero ?? ($compteurSortie?->numero ?? '-') }}</td>
                                <td class="text-center bg-blue font-bold">{{ $compteurEntree?->index ?? '-' }}</td>
                                <td class="text-center bg-orange font-bold">{{ $compteurSortie?->index ?? '-' }}</td>
                                <td class="text-center font-bold">
                                    @if ($consommation !== null)
                                        {{ number_format($consommation, 0, ',', ' ') }} {{ $unites[$type] }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Clés --}}
    @if ($edlEntree->cles->isNotEmpty() || $edlSortie->cles->isNotEmpty())
        <div class="section">
            <div class="section-title">🔑 Comparatif des clés</div>
            @php
                $tousTypesClés = $edlEntree->cles
                    ->pluck('type')
                    ->merge($edlSortie->cles->pluck('type'))
                    ->unique();
                $totalEntree = $edlEntree->cles->sum('nombre');
                $totalSortie = $edlSortie->cles->sum('nombre');
                $differenceTotal = $totalSortie - $totalEntree;
            @endphp
            <table class="cles-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Type de clé</th>
                        <th class="text-center bg-blue" style="width: 12%;">Photo</th>
                        <th class="text-center bg-blue" style="width: 13%;">Entrée</th>
                        <th class="text-center bg-orange" style="width: 12%;">Photo</th>
                        <th class="text-center bg-orange" style="width: 13%;">Sortie</th>
                        <th class="text-center" style="width: 15%;">Diff.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tousTypesClés as $type)
                        @php
                            $cleEntree = $edlEntree->cles->where('type', $type)->first();
                            $cleSortie = $edlSortie->cles->where('type', $type)->first();
                            $nbEntree = $cleEntree?->nombre ?? 0;
                            $nbSortie = $cleSortie?->nombre ?? 0;
                            $difference = $nbSortie - $nbEntree;
                        @endphp
                        <tr class="{{ $difference < 0 ? 'degradation-row' : '' }}">
                            <td>🔑 {{ $type }}</td>
                            <td class="text-center bg-blue">
                                @if ($cleEntree?->photo)
                                    <img src="{{ public_path('storage/' . $cleEntree->photo) }}" alt="Clé"
                                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px;">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center bg-blue font-bold">{{ $nbEntree > 0 ? $nbEntree : '-' }}</td>
                            <td class="text-center bg-orange">
                                @if ($cleSortie?->photo)
                                    <img src="{{ public_path('storage/' . $cleSortie->photo) }}" alt="Clé"
                                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px;">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center bg-orange font-bold">{{ $nbSortie > 0 ? $nbSortie : '-' }}</td>
                            <td class="text-center font-bold {{ $difference < 0 ? 'text-red' : ($difference > 0 ? 'text-green' : '') }}">
                                @if ($difference < 0)
                                    ⚠️ {{ $difference }}
                                @elseif($difference > 0)
                                    +{{ $difference }}
                                @else
                                    =
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9;">
                        <td class="font-bold">Total</td>
                        <td class="bg-blue"></td>
                        <td class="text-center font-bold" style="background: #dbeafe;">{{ $totalEntree }}</td>
                        <td class="bg-orange"></td>
                        <td class="text-center font-bold" style="background: #fed7aa;">{{ $totalSortie }}</td>
                        <td class="text-center font-bold {{ $differenceTotal < 0 ? 'text-red' : 'text-green' }}">
                            @if ($differenceTotal < 0)
                                {{ $differenceTotal }}
                            @elseif($differenceTotal > 0)
                                +{{ $differenceTotal }}
                            @else
                                ✓
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if ($differenceTotal < 0)
                <div class="warning-box">
                    <p><strong>⚠️ Attention :</strong> {{ abs($differenceTotal) }} clé(s) non restituée(s).</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Résumé dégradations --}}
    @if ($stats['degrade'] > 0)
        <div class="summary-box danger">
            <h3>⚠️ {{ $stats['degrade'] }} dégradation(s) constatée(s)</h3>
            <table class="elements-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Pièce</th>
                        <th style="width: 12%;">Élément</th>
                        <th class="text-center" style="width: 8%;">Entrée</th>
                        <th class="bg-blue" style="width: 22%;">Dégradations entrée</th>
                        <th class="text-center" style="width: 8%;">Sortie</th>
                        <th class="bg-orange" style="width: 22%;">Dégradations sortie</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comparatif as $pieceData)
                        @foreach ($pieceData['elements'] as $element)
                            @if ($element['status'] === 'degrade')
                                <tr>
                                    <td>{{ $pieceData['nom'] }}</td>
                                    <td class="font-bold">{{ $element['sortie']->nom }}</td>
                                    <td class="text-center">
                                        <span class="etat-badge etat-{{ $element['entree']->etat }}">{{ $element['entree']->etat_libelle }}</span>
                                    </td>
                                    <td class="bg-blue">
                                        @if($element['entree']->hasDegradations())
                                            @foreach($element['entree']->degradations as $degradation)
                                                <span class="degradation-badge-blue">{{ $degradation }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="etat-badge etat-{{ $element['sortie']->etat }}">{{ $element['sortie']->etat_libelle }}</span>
                                    </td>
                                    <td class="bg-orange">
                                        @if($element['sortie']->hasDegradations())
                                            @foreach($element['sortie']->degradations as $degradation)
                                                @php
                                                    $isNew = !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                                @endphp
                                                <span class="{{ $isNew ? 'degradation-badge-new' : 'degradation-badge-orange' }}">{{ $degradation }}{{ $isNew ? ' ●' : '' }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <p class="legend">● = Nouvelle dégradation (imputable au locataire)</p>
        </div>
    @else
        <div class="summary-box success">
            <h3>✓ Aucune dégradation constatée</h3>
            <p style="font-size: 10px; color: #16a34a;">Le logement est rendu dans un état conforme.</p>
        </div>
    @endif

    {{-- Détail par pièce --}}
    @foreach ($comparatif as $pieceData)
        <div class="piece">
            <div class="piece-header {{ $pieceData['has_degradation'] ? 'degradation' : '' }}">
                <h3>{{ $pieceData['nom'] }} @if ($pieceData['has_degradation']) ⚠️ @endif</h3>
            </div>
            <table class="elements-table" style="margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 14%;">Élément</th>
                        <th class="text-center bg-blue" style="width: 10%;">Entrée</th>
                        <th class="bg-blue" style="width: 24%;">Dégradations entrée</th>
                        <th class="text-center bg-orange" style="width: 10%;">Sortie</th>
                        <th class="bg-orange" style="width: 24%;">Dégradations sortie</th>
                        <th class="text-center" style="width: 10%;">Évolution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pieceData['elements'] as $element)
                        <tr class="{{ $element['status'] === 'degrade' ? 'degradation-row' : '' }}">
                            <td class="font-bold">{{ $element['sortie']->nom }}</td>
                            <td class="text-center bg-blue">
                                @if ($element['entree'])
                                    <span class="etat-badge etat-{{ $element['entree']->etat }}">{{ $element['entree']->etat_libelle }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="bg-blue">
                                @if ($element['entree'] && $element['entree']->hasDegradations())
                                    @foreach($element['entree']->degradations as $degradation)
                                        <span class="degradation-badge-blue">{{ $degradation }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center bg-orange">
                                <span class="etat-badge etat-{{ $element['sortie']->etat }}">{{ $element['sortie']->etat_libelle }}</span>
                            </td>
                            <td class="bg-orange">
                                @if($element['sortie']->hasDegradations())
                                    @foreach($element['sortie']->degradations as $degradation)
                                        @php
                                            $isNew = !$element['entree'] || !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                        @endphp
                                        <span class="{{ $isNew ? 'degradation-badge-new' : 'degradation-badge-orange' }}">{{ $degradation }}{{ $isNew ? ' ●' : '' }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($element['status'] === 'degrade')
                                    <span class="text-red font-bold">↓ Dégradé</span>
                                @elseif($element['status'] === 'ameliore')
                                    <span class="text-green font-bold">↑ Amélioré</span>
                                @elseif($element['status'] === 'nouveau')
                                    <span style="color: #2563eb;">Nouveau</span>
                                @else
                                    =
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <p class="legend">● = Nouvelle dégradation (imputable au locataire)</p>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} — Edlya</p>
    </div>
</body>

</html>