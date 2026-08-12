<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-size: 12px;
        }

        /* CSS KHUSUS CETAK DAPUR (A4) */
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: white;
            box-sizing: border-box;
        }

        .grid-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm; /* Jarak antar struk */
            justify-content: flex-start; /* Rata kiri */
        }

        /* Ukuran 1 struk dapur (setengah lebar A4 minus padding) */
        .struk-item {
            width: 95mm; /* Sekitar 1/2 A4 */
            padding: 5mm;
            border: 1px dashed #000;
            box-sizing: border-box;
            background-color: #fff;
        }

        /* CSS KHUSUS STRUK CUSTOMER (Thermal) */
        .thermal-receipt {
            width: 58mm;
            margin: 0 auto;
            background: white;
            padding: 5mm;
        }

        @media print {
            body { background-color: white; }
            .no-print { display: none; }
            
            /* Pengaturan Kertas A4 */
            @page {
                size: A4;
                margin: 0;
            }
            
            .a4-page {
                margin: 0;
                box-shadow: none;
                border: none;
                width: 100%;
                height: auto;
            }
        }

        .separator {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

@if($type == 'dapur')
    <!-- ===================== MODE DAPUR (A4 GRID) ===================== -->
    <div class="a4-page">
        <div class="grid-container">
            @foreach($groupedOrders as $kitchenData)
                <div class="struk-item">
                    <div class="text-center">
                        <h3 style="margin:0; font-size: 14px;">XEPPO</h3>
                        <small style="font-weight: bold; color: #d9534f;">{{ $kitchenData['kitchen_name'] }}</small>
                        <div class="separator"></div>
                    </div>

                    <table width="100%" style="font-size: 11px;">
                        <tr>
                            <td>Meja/Area</td>
                            <td class="text-right">{{ $reservation->area->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Atas Nama</td>
                            <td class="text-right">{{ $reservation->customer_name }}</td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td class="text-right">{{ date('H:i', strtotime($reservation->reservation_time)) }}</td>
                        </tr>
                    </table>

                    <div class="separator"></div>

                    <!-- Daftar Pesanan Khusus Dapur Ini -->
                    @foreach($kitchenData['items'] as $item)
                        <div style="margin-bottom: 2px;">
                            <span style="font-size: 13px; font-weight: bold;">{{ $item['qty'] }}x {{ $item['name'] }}</span>
                        </div>
                    @endforeach

                    <div class="separator" style="margin-top: 5px;"></div>
                    
                    <!-- Total Items Dapur Ini (Opsional, untuk cekingan koki) -->
                    <table width="100%">
                        <tr>
                            <td class="fw-bold">Total Item</td>
                            <td class="text-right fw-bold">{{ array_sum(array_column($kitchenData['items'], 'qty')) }}</td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    </div>

@else
    <!-- ===================== MODE CUSTOMER (THERMAL) ===================== -->
    <div class="thermal-receipt">
        <div class="text-center">
            <h3 style="margin:0; font-size: 16px;">XEPPO</h3>
            <small>Restoran & Cafe</small><br>
            <small>Jl. Contoh Alamat No. 123</small>
        </div>
        <div class="separator"></div>

        <table width="100%">
            <tr><td>Nama</td><td class="text-right">{{ $reservation->customer_name }}</td></tr>
            <tr><td>Tanggal</td><td class="text-right">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td></tr>
            <tr><td>Jam</td><td class="text-right">{{ $reservation->reservation_time }}</td></tr>
            <tr><td>Area</td><td class="text-right">{{ $reservation->area->name ?? '-' }}</td></tr>
        </table>

        <div class="separator"></div>

        <table width="100%">
            @foreach($reservation->menus as $item)
            <tr>
                <td colspan="2">{{ $item['name'] }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="separator"></div>

        @if($type == 'dp')
            <table width="100%">
                <tr><td>Total Harga</td><td class="text-right">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</td></tr>
                <tr class="fw-bold"><td>DP Bayar (20%)</td><td class="text-right">Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</td></tr>
                <tr><td>Sisa Pelunasan</td><td class="text-right">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</td></tr>
            </table>
        @elseif($type == 'pelunasan')
            <table width="100%">
                <tr><td>Total Tagihan</td><td class="text-right">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</td></tr>
                <tr><td>DP Dibayar</td><td class="text-right">(Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }})</td></tr>
                <tr class="fw-bold" style="font-size: 14px;"><td>PELUNASAN</td><td class="text-right">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</td></tr>
            </table>
        @endif

        <div class="separator"></div>
        <div class="text-center"><small>Terima kasih!</small></div>
    </div>
@endif

<div class="no-print text-center mt-4" style="background: #eee; padding: 10px;">
    <button onclick="window.print()" class="btn btn-primary">Print</button>
    <button onclick="window.close()" class="btn btn-secondary">Close</button>
</div>

</body>
</html>