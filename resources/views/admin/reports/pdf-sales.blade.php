<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .title { font-size:18px; font-weight:700; }
        .meta { text-align:right; font-size:11px; }
        .summary { margin:10px 0; }
        .summary table { border-collapse: collapse; width: 100%; }
        .summary td { padding:6px; border:1px solid #ddd; }
        table.report { width:100%; border-collapse: collapse; margin-top:8px; }
        table.report th, table.report td { border:1px solid #ddd; padding:6px; }
        table.report th { background:#f5f5f5; font-weight:700; }
        .right { text-align:right; }
        .small { font-size:11px; }
        footer { position: fixed; bottom: 0; left:0; right:0; text-align:center; font-size:11px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="meta">
            <div class="title">{{ $title }}</div>
            <div class="small">Generated: {{ now()->toDateTimeString() }}</div>
        </div>
    </div>

    @php
        // matrix contains key rows; find the sales summary block between labels
        $summaryStart = null;
        $trendStart = null;
        foreach ($matrix as $idx => $row) {
            if (!empty($row) && strtolower((string)($row[0] ?? '')) === 'sales summary') {
                $summaryStart = $idx + 1;
            }
            if (!empty($row) && strtolower((string)($row[0] ?? '')) === 'revenue trend') {
                $trendStart = $idx + 1;
            }
        }
    @endphp

    @if($summaryStart !== null)
        <div class="summary">
            <table>
                <tr>
                    @for($i = $summaryStart; $i < $summaryStart + 6 && isset($matrix[$i]); $i++)
                        <td><strong>{{ $matrix[$i][0] ?? '' }}</strong><br>{{ $matrix[$i][1] ?? '' }}</td>
                    @endfor
                </tr>
            </table>
        </div>
    @endif

    @if($trendStart !== null)
        <table class="report small">
            <thead>
                <tr>
                    <th>Period</th>
                    <th class="right">Orders</th>
                    <th class="right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @for($i = $trendStart + 1; $i < count($matrix); $i++)
                    @php $row = $matrix[$i]; @endphp
                    @if(empty($row)) @continue @endif
                    @if(is_array($row) && count($row) >= 3)
                        <tr>
                            <td>{{ $row[0] }}</td>
                            <td class="right">{{ $row[1] }}</td>
                            <td class="right">{{ $row[2] }}</td>
                        </tr>
                    @endif
                @endfor
            </tbody>
        </table>
    @endif

    <footer>Page <span class="pagenum"></span></footer>
</body>
</html>