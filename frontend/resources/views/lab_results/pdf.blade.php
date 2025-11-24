<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Résultat d’Analyse</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #bc1622;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 90px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 20px;
            color: #bc1622;
            margin: 0;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #f5f5f5;
            color: #333;
            font-weight: bold;
            padding: 5px 10px;
            border-left: 5px solid #bc1622;
            margin-bottom: 10px;
        }

        .info-table, .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .info-table td.label {
            font-weight: bold;
            color: #555;
            width: 30%;
        }

        .results-table th, .results-table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        .results-table th {
            background-color: #efefef;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .result-value {
            font-weight: bold;
        }

        .normal {
            color: #22a745;
        }

        .high {
            color: #ff9900;
        }

        .low {
            color: #007bff;
        }

        .critical {
            color: #d93025;
            font-weight: bold;
        }

        .footer {
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding-top: 10px;
            margin-top: 30px;
        }

        .lab-signature {
            text-align: right;
            margin-top: 40px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo du Laboratoire">
        <h1>Abdelatif Lab</h1>
        <p>Laboratoire d’Analyses Médicales — Résultats Officiels</p>
    </div>

    {{-- PATIENT INFORMATION --}}
    <div class="section">
        <div class="section-title">Informations du Patient</div>
        <table class="info-table">
            <tr>
                <td class="label">Nom complet :</td>
                <td>{{ $result['patient_first_name'] ?? '' }} {{ $result['patient_last_name'] ?? '' }}</td>
                <td class="label">N° dossier :</td>
                <td>{{ $result['file_number'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Date du rapport :</td>
                <td>{{ \Carbon\Carbon::parse($result['created_at'])->format('d/m/Y H:i') }}</td>
                <td class="label">Appareil utilisé :</td>
                <td>{{ $result['device_name'] ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ANALYSIS RESULT --}}
    <div class="section">
        <div class="section-title">Résultat d’Analyse</div>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Analyse</th>
                    <th>Résultat</th>
                    <th>Valeurs normales</th>
                    <th>Interprétation</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $result['analysis_code'] ?? '—' }}</td>
                    <td>{{ $result['analysis_name'] ?? '—' }}</td>
                    <td class="result-value">
                        {{ $result['result_value'] ?? '—' }}
                    </td>
                    <td>
                        @if(isset($result['normal_min']) && isset($result['normal_max']))
                            {{ $result['normal_min'] }} – {{ $result['normal_max'] }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="{{ strtolower($result['interpretation'] ?? '') }}">
                        {{ ucfirst($result['interpretation'] ?? 'n/a') }}
                    </td>
                    <td>
                        {{ ucfirst($result['status'] ?? '—') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="lab-signature">
        <p><strong>Validé par :</strong> Le Biologiste Responsable</p>
        <p style="margin-top: 40px;">Signature : __________________________</p>
    </div>

    <div class="footer">
        <p>Ce rapport est généré automatiquement par le système Abdelatif Lab — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
