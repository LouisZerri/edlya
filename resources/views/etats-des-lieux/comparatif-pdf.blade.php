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
        .summary {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .summary h2 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #1e293b;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }
        .stat-box .number {
            font-size: 20px;
            font-weight: bold;
        }
        .stat-box .label {
            font-size: 9px;
            color: #64748b;
        }
        .stat-green .number { color: #16a34a; }
        .stat-red .number { color: #dc2626; }
        .stat-blue .number { color: #2563eb; }
        .info-row {
            display: table;
            width: 100%;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
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
            font-size: 10px;
            margin-bottom: 5px;
        }
        .info-box.entree h3 { color: #1d4ed8; }
        .info-box.sortie h3 { color: #c2410c; }
        .piece {
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .piece.has-degradation {
            border-color: #fca5a5;
            border-width: 2px;
        }
        .piece-header {
            background: #f1f5f9;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .piece-header h3 {
            font-size: 12px;
            display: inline;
        }
        .piece-header .badge {
            float: right;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        .badge-success {
            background: #dcfce7;
            color: #16a34a;
        }
        .element {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            page-break-inside: avoid;
        }
        .element:last-child {
            border-bottom: none;
        }
        .element.degrade {
            background: #fef2f2;
        }
        .element-header {
            margin-bottom: 8px;
        }
        .element-name {
            font-weight: bold;
            font-size: 11px;
        }
        .element-type {
            color: #64748b;
            font-size: 9px;
        }
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .dot-green { background: #16a34a; }
        .dot-amber { background: #f59e0b; }
        .dot-red { background: #dc2626; }
        .dot-blue { background: #2563eb; }
        .comparison {
            display: table;
            width: 100%;
            margin-top: 8px;
        }
        .comparison-col {
            display: table-cell;
            width: 50%;
            padding: 8px;
            vertical-align: top;
            background: white;
            border: 1px solid #e2e8f0;
        }
        .comparison-col h4 {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .etat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .etat-neuf { background: #dcfce7; color: #166534; }
        .etat-tres_bon { background: #d1fae5; color: #065f46; }
        .etat-bon { background: #e0f2fe; color: #0369a1; }
        .etat-usage { background: #fef3c7; color: #92400e; }
        .etat-mauvais { background: #fee2e2; color: #dc2626; }
        .etat-hors_service { background: #f3f4f6; color: #374151; }
        .observation {
            font-size: 9px;
            color: #475569;
            font-style: italic;
            background: #f8fafc;
            padding: 5px;
            border-radius: 3px;
            margin-top: 5px;
        }
        .evolution {
            font-size: 9px;
            color: #dc2626;
            font-weight: bold;
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
    {{-- Header --}}
    <div class="header">
        <h1>COMPARATIF ÉTAT DES LIEUX</h1>
        <p>{{ $edlSortie->logement->nom }} — {{ $edlSortie->logement->adresse_complete }}</p>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <h2>Résumé du comparatif</h2>
        
        <div class="stats">
            <div class="stat-box">
                <div class="number">{{ $stats['total'] }}</div>
                <div class="label">Éléments comparés</div>
            </div>
            <div class="stat-box stat-green">
                <div class="number">{{ $stats['identique'] + $stats['ameliore'] }}</div>
                <div class="label">Sans dégradation</div>
            </div>
            <div class="stat-box stat-red">
                <div class="number">{{ $stats['degrade'] }}</div>
                <div class="label">Dégradations</div>
            </div>
            <div class="stat-box stat-blue">
                <div class="number">{{ $stats['nouveau'] }}</div>
                <div class="label">Nouveaux éléments</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-box entree">
                <h3>État des lieux d'entrée</h3>
                <p>Date : {{ $edlEntree->date_realisation->format('d/m/Y') }}</p>
                <p>Locataire : {{ $edlEntree->locataire_nom }}</p>
            </div>
            <div class="info-box sortie">
                <h3>État des lieux de sortie</h3>
                <p>Date : {{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
                <p>Locataire : {{ $edlSortie->locataire_nom }}</p>
            </div>
        </div>
    </div>

    {{-- Comparatif --}}
    @foreach($comparatif as $piece)
        <div class="piece {{ $piece['has_degradation'] ? 'has-degradation' : '' }}">
            <div class="piece-header">
                <h3>{{ $piece['nom'] }}</h3>
                @if($piece['has_degradation'])
                    <span class="badge badge-danger">Dégradations</span>
                @else
                    <span class="badge badge-success">RAS</span>
                @endif
            </div>

            @foreach($piece['elements'] as $element)
                @php
                    $isDegrade = $element['status'] === 'degrade';
                    $dotClass = match($element['status']) {
                        'degrade' => $element['evolution'] <= -2 ? 'dot-red' : 'dot-amber',
                        'ameliore', 'identique' => 'dot-green',
                        'nouveau' => 'dot-blue',
                        default => 'dot-green'
                    };
                @endphp

                <div class="element {{ $isDegrade ? 'degrade' : '' }}">
                    <div class="element-header">
                        <span class="status-dot {{ $dotClass }}"></span>
                        <span class="element-name">{{ $element['sortie']->nom }}</span>
                        <span class="element-type">({{ $element['sortie']->type }})</span>
                        @if($isDegrade)
                            <span class="evolution">↓ {{ abs($element['evolution']) }} niveau(x)</span>
                        @endif
                    </div>

                    <div class="comparison">
                        <div class="comparison-col">
                            <h4>Entrée</h4>
                            @if($element['entree'])
                                <span class="etat-badge etat-{{ $element['entree']->etat }}">{{ $element['entree']->etat_libelle }}</span>
                                @if($element['entree']->observations)
                                    <div class="observation">{{ $element['entree']->observations }}</div>
                                @endif
                            @else
                                <p style="font-size: 9px; color: #94a3b8;">Non présent</p>
                            @endif
                        </div>
                        <div class="comparison-col">
                            <h4>Sortie</h4>
                            <span class="etat-badge etat-{{ $element['sortie']->etat }}">{{ $element['sortie']->etat_libelle }}</span>
                            @if($element['sortie']->observations)
                                <div class="observation" style="{{ $isDegrade ? 'background: #fee2e2;' : '' }}">
                                    {{ $element['sortie']->observations }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- Footer --}}
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} — Edlya, propulsé par GEST'IMMO</p>
    </div>
</body>
</html>