<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
        .container { padding: 2mm; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        hr { border: none; border-top: 1px dashed #000; margin: 2mm 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 0.5mm 0; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="text-center">
            <h3 style="margin:0; font-size: 12px;">XEPPO</h3>
            <small style="font-size: 9px;">Restoran & Cafe</small>
            <hr>
        </div>

        <!-- Info -->
        <table>
            <tr><td>Nama</td><td class="text-right">{{ $reservation->customer_name }}</td></tr>
            <tr><td>Area</td><td class="text-right">{{ $reservation->area->name ?? '-' }}</td></tr>
            <tr><td>Tgl</td><td class="text-right">{{ date('d/m/Y', strtotime($reservation->reservation_date)) }}</td></tr>
            <tr><td>Jam</td><td class="text-right">{{ $reservation->reservation_time }}</td></tr>
            <tr><td>Jml Tamu</td><td class="text-right">{{ $reservation->number_of_guests }} Orang</tr>
        </table>
        <hr>

        <!-- Items -->
        @foreach($reservation->menus as $item)
        <div style="display: flex; justify-content: space-between; margin-bottom: 1mm;">
            <span>{{ $item['qty'] }}x {{ $item['name'] }}</span>
            <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
        </div>
        @endforeach

        <hr>

        <!-- Total -->
        @if($type == 'dp')
        <table>
            <tr><td>Total</td><td class="text-right">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</td></tr>
            <tr style="font-weight:bold;"><td>DP (20%)</td><td class="text-right">Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa</td><td class="text-right">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</td></tr>
        </table>
        @else
        <div class="text-center">
            <p style="font-size:11px; font-weight:bold; margin:0;">TOTAL BAYAR</p>
            <p style="font-size:11px; font-weight:bold; margin:0;">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</p>
        </div>
        @endif

        <hr>
        <div class="text-center">
            <small>Terima Kasih</small>
        </div>
    </div>
</body>
</html>