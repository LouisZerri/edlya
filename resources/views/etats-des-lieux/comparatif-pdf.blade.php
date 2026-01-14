<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Comparatif - {{ $edlSortie->logement->nom }}</title>
    <style>
        @page {
            margin: 20mm 10mm 25mm 10mm;
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

        .content-wrapper {
            padding: 5px 20px 60px 20px;
        }

        /* Header */
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 15px;
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

        .header-left h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #666;
        }

        /* Sections */
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
        }

        /* Info boxes */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
            border: 1px solid #ddd;
        }

        .info-box h3 {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-box p {
            font-size: 9px;
            margin-bottom: 2px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px 5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
        }

        td {
            border: 1px solid #ddd;
            padding: 5px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        /* États - couleurs essentielles */
        .etat-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
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
            background: #dbeafe;
            color: #1e40af;
        }

        .etat-usage {
            background: #fef3c7;
            color: #92400e;
        }

        .etat-mauvais {
            background: #fed7aa;
            color: #c2410c;
        }

        .etat-hors_service {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Dégradations */
        .degradation-badge {
            display: inline-block;
            font-size: 7px;
            padding: 1px 4px;
            border-radius: 3px;
            margin-right: 2px;
            margin-bottom: 2px;
            border: 1px solid #ddd;
        }

        .degradation-badge-new {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
            font-weight: bold;
        }

        .text-red {
            color: #dc2626;
        }

        .text-green {
            color: #16a34a;
        }

        /* Ligne dégradée */
        .degradation-row {
            background: #fef2f2;
        }

        /* Pièces */
        .piece {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .piece-header {
            background: #f5f5f5;
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }

        .piece-header.degradation {
            background: #fef2f2;
        }

        .piece-header h3 {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* Summary boxes */
        .summary-box {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .summary-box.danger {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .summary-box.success {
            border-color: #bbf7d0;
        }

        .summary-box h3 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .summary-box.danger h3 {
            color: #dc2626;
        }

        .summary-box.success h3 {
            color: #16a34a;
        }

        /* Warning */
        .warning-box {
            border: 1px solid #fecaca;
            padding: 8px;
            margin-top: 10px;
        }

        .warning-box p {
            color: #dc2626;
            font-size: 9px;
        }

        .legend {
            font-size: 8px;
            color: #dc2626;
            margin-top: 8px;
        }

        /* Footer */
        .footer {
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

        .col-entree {
            background: #f8fafc;
        }

        .col-sortie {
            background: #fffbeb;
        }
    </style>
</head>

<body>
    {{-- Footer fixe --}}
    <div class="footer">
        <span class="footer-brand">EDLYA</span> — Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <div class="content-wrapper">
        {{-- Header --}}
        <div class="header-top">
            <div class="header-left">
                <h1>Comparatif Entrée / Sortie</h1>
                <p class="header-subtitle">{{ $edlSortie->logement->adresse_complete }}</p>
            </div>
            <div class="header-right">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj4KICAgIDxwYXRoIGQ9Ik01MCAxMCBMODggNDIgTDg4IDg4IEwxMiA4OCBMMTIgNDIgWiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNGY0NmU1IiBzdHJva2Utd2lkdGg9IjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgogICAgPHBhdGggZD0iTTYgNDUgTDUwIDEwIEw5NCA0NSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNGY0NmU1IiBzdHJva2Utd2lkdGg9IjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgogICAgPGVsbGlwc2UgY3g9IjUwIiBjeT0iNTgiIHJ4PSIyMCIgcnk9IjE0IiBmaWxsPSJub25lIiBzdHJva2U9IiM0ZjQ2ZTUiIHN0cm9rZS13aWR0aD0iNCIvPgogICAgPGNpcmNsZSBjeD0iNTAiIGN5PSI1OCIgcj0iNyIgZmlsbD0iIzRmNDZlNSIvPgo8L3N2Zz4=" alt="Edlya" style="width: 50px; height: 50px;">
            </div>
        </div>

        {{-- Info boxes --}}
        <div class="info-grid">
            <div class="info-box">
                <h3>État des lieux d'entrée</h3>
                <p><strong>Date :</strong> {{ $edlEntree->date_realisation->format('d/m/Y') }}</p>
                <p><strong>Locataire :</strong> {{ $edlEntree->locataire_nom }}</p>
            </div>
            <div class="info-box">
                <h3>État des lieux de sortie</h3>
                <p><strong>Date :</strong> {{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
                <p><strong>Locataire :</strong> {{ $edlSortie->locataire_nom }}</p>
            </div>
        </div>

        {{-- Compteurs --}}
        @if ($edlEntree->compteurs->isNotEmpty() || $edlSortie->compteurs->isNotEmpty())
            <div class="section">
                <div class="section-title">Relevé des compteurs</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Type</th>
                            <th style="width: 25%;">N° Compteur</th>
                            <th class="text-center" style="width: 18%;">Index entrée</th>
                            <th class="text-center" style="width: 18%;">Index sortie</th>
                            <th class="text-center" style="width: 19%;">Consommation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $typesCompteurs = [
                                'electricite' => 'Électricité',
                                'eau_froide' => 'Eau froide',
                                'eau_chaude' => 'Eau chaude',
                                'gaz' => 'Gaz',
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
                                    <td style="font-size: 8px;">{{ $compteurEntree?->numero ?? ($compteurSortie?->numero ?? '-') }}</td>
                                    <td class="text-center col-entree font-bold">{{ $compteurEntree?->index ?? '-' }}</td>
                                    <td class="text-center col-sortie font-bold">{{ $compteurSortie?->index ?? '-' }}</td>
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
                <div class="section-title">Restitution des clés</div>
                @php
                    $tousTypesClés = $edlEntree->cles
                        ->pluck('type')
                        ->merge($edlSortie->cles->pluck('type'))
                        ->unique();
                    $totalEntree = $edlEntree->cles->sum('nombre');
                    $totalSortie = $edlSortie->cles->sum('nombre');
                    $differenceTotal = $totalSortie - $totalEntree;
                @endphp
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30%;">Type de clé</th>
                            <th class="text-center" style="width: 20%;">Entrée</th>
                            <th class="text-center" style="width: 20%;">Sortie</th>
                            <th class="text-center" style="width: 30%;">Différence</th>
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
                                <td>{{ $type }}</td>
                                <td class="text-center col-entree font-bold">{{ $nbEntree > 0 ? $nbEntree : '-' }}</td>
                                <td class="text-center col-sortie font-bold">{{ $nbSortie > 0 ? $nbSortie : '-' }}</td>
                                <td class="text-center font-bold {{ $difference < 0 ? 'text-red' : ($difference > 0 ? 'text-green' : '') }}">
                                    @if ($difference < 0)
                                        {{ $difference }} clé(s) manquante(s)
                                    @elseif($difference > 0)
                                        +{{ $difference }}
                                    @else
                                        OK
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f5f5f5;">
                            <td class="font-bold">TOTAL</td>
                            <td class="text-center font-bold col-entree">{{ $totalEntree }}</td>
                            <td class="text-center font-bold col-sortie">{{ $totalSortie }}</td>
                            <td class="text-center font-bold {{ $differenceTotal < 0 ? 'text-red' : 'text-green' }}">
                                @if ($differenceTotal < 0)
                                    {{ $differenceTotal }} clé(s)
                                @elseif($differenceTotal > 0)
                                    +{{ $differenceTotal }}
                                @else
                                    Complet
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>

                @if ($differenceTotal < 0)
                    <div class="warning-box">
                        <p><strong>Attention :</strong> {{ abs($differenceTotal) }} clé(s) non restituée(s).</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Résumé dégradations --}}
        @if ($stats['degrade'] > 0)
            <div class="summary-box danger">
                <h3>{{ $stats['degrade'] }} dégradation(s) constatée(s)</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Pièce</th>
                            <th style="width: 15%;">Élément</th>
                            <th class="text-center" style="width: 10%;">Entrée</th>
                            <th style="width: 25%;">Dégradations entrée</th>
                            <th class="text-center" style="width: 10%;">Sortie</th>
                            <th style="width: 25%;">Dégradations sortie</th>
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
                                        <td>
                                            @if($element['entree']->hasDegradations())
                                                @foreach($element['entree']->degradations as $degradation)
                                                    <span class="degradation-badge">{{ $degradation }}</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="etat-badge etat-{{ $element['sortie']->etat }}">{{ $element['sortie']->etat_libelle }}</span>
                                        </td>
                                        <td>
                                            @if($element['sortie']->hasDegradations())
                                                @foreach($element['sortie']->degradations as $degradation)
                                                    @php
                                                        $isNew = !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                                    @endphp
                                                    <span class="degradation-badge {{ $isNew ? 'degradation-badge-new' : '' }}">{{ $degradation }}{{ $isNew ? ' ●' : '' }}</span>
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
                <h3>Aucune dégradation constatée</h3>
                <p style="font-size: 10px; color: #16a34a;">Le logement est rendu dans un état conforme à l'état des lieux d'entrée.</p>
            </div>
        @endif

        {{-- Détail par pièce --}}
        @foreach ($comparatif as $pieceData)
            <div class="piece">
                <div class="piece-header {{ $pieceData['has_degradation'] ? 'degradation' : '' }}">
                    <h3>{{ $pieceData['nom'] }} @if ($pieceData['has_degradation']) — Dégradation(s) @endif</h3>
                </div>
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Élément</th>
                            <th class="text-center" style="width: 10%;">Entrée</th>
                            <th style="width: 22%;">Dégradations entrée</th>
                            <th class="text-center" style="width: 10%;">Sortie</th>
                            <th style="width: 22%;">Dégradations sortie</th>
                            <th class="text-center" style="width: 18%;">Évolution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pieceData['elements'] as $element)
                            <tr class="{{ $element['status'] === 'degrade' ? 'degradation-row' : '' }}">
                                <td class="font-bold">{{ $element['sortie']->nom }}</td>
                                <td class="text-center">
                                    @if ($element['entree'])
                                        <span class="etat-badge etat-{{ $element['entree']->etat }}">{{ $element['entree']->etat_libelle }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($element['entree'] && $element['entree']->hasDegradations())
                                        @foreach($element['entree']->degradations as $degradation)
                                            <span class="degradation-badge">{{ $degradation }}</span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="etat-badge etat-{{ $element['sortie']->etat }}">{{ $element['sortie']->etat_libelle }}</span>
                                </td>
                                <td>
                                    @if($element['sortie']->hasDegradations())
                                        @foreach($element['sortie']->degradations as $degradation)
                                            @php
                                                $isNew = !$element['entree'] || !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                            @endphp
                                            <span class="degradation-badge {{ $isNew ? 'degradation-badge-new' : '' }}">{{ $degradation }}{{ $isNew ? ' ●' : '' }}</span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($element['status'] === 'degrade')
                                        <span class="text-red font-bold">Dégradé</span>
                                    @elseif($element['status'] === 'ameliore')
                                        <span class="text-green font-bold">Amélioré</span>
                                    @elseif($element['status'] === 'nouveau')
                                        <span style="color: #666;">Nouveau</span>
                                    @else
                                        Identique
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <p class="legend">● = Nouvelle dégradation (imputable au locataire)</p>

    </div>
</body>

</html>