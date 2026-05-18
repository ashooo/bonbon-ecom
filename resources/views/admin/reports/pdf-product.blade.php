<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .title { font-size:18px; font-weight:700; }
        .meta { text-align:right; font-size:11px; }
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

    <table class="report small">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th class="right">Qty Sold</th>
                <th class="right">Orders Count</th>
                <th class="right">Revenue</th>
                <th class="right">Avg. Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matrix as $row)
                @if(is_array($row) && count($row) === 6 && $row[0] !== 'Product Name')
                    <tr>
                        <td>{{ $row[0] }}</td>
                        <td>{{ $row[1] }}</td>
                        <td class="right">{{ $row[2] }}</td>
                        <td class="right">{{ $row[3] }}</td>
                        <td class="right">{{ $row[4] }}</td>
                        <td class="right">{{ $row[5] }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <footer>Page <span class="pagenum"></span></footer>
</body>
</html>