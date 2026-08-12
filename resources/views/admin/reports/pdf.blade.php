<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 2px 0; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        .total-section { margin-top: 20px; text-align: right; }
        .total-section table { width: 300px; margin-left: auto; border: none; }
        .total-section td { border: none; padding: 5px; }
        .total-label { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>XEPPO RESTAURANT</h2>
        <p>{{ $title }}</p>
        <p>Periode: {{ $dateLabel }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Customer</th>
                <th>Area</th>
                <th>Total Tagihan</th>
                <th>DP Dibayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ date('d/m/Y', strtotime($row->reservation_date)) }}</td>
                <td>{{ $row->customer_name }}</td>
                <td>{{ $row->area->name ?? '-' }}</td>
                <td>Rp {{ number_format($row->total_price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($row->dp_amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($row->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table>
            <tr>
                <td class="total-label">Total Penjualan:</td>
                <td>Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="total-label">Total DP Masuk:</td>
                <td>Rp {{ number_format($totalDp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="total-label">Sisa Piutang:</td>
                <td>Rp {{ number_format($totalSales - $totalDp, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: right; font-size: 10px;">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

</body>
</html>