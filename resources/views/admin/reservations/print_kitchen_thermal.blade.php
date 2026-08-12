<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Dapur</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #000;
            background-color: #fff;
            margin: 0; 
            padding: 0;
        }

        @media print {
            @page {
                size: 50mm auto; /* Lebar 50mm, tinggi otomatis menyesuaikan isi */
                margin: 0;
            }
            body { width: 44mm; margin: 0 auto; }
            .no-print { display: none; }
            
            /* CSS AGAR 1 STRUK = 1 HALAMAN (PUTUS KERTAS) */
            .slip-dapur {
                page-break-after: always; /* Putus kertas setelah setiap slip */
                page-break-inside: avoid; /* Jangan potong isi slip */
            }
            /* Hilangkan garis putus di slip terakhir agar tidak ada kertas kosong di akhir */
            .slip-dapur:last-child {
                page-break-after: auto;
            }
        }

        @media screen {
            body { width: 44mm; margin: 0 auto; background: #f0f0f0; padding: 5px; }
            /* Simulasi pemisah halaman di preview layar */
            .slip-dapur {
                border-bottom: 2px dashed red;
                margin-bottom: 10px;
                padding-bottom: 10px;
            }
        }

        .container {
            width: 100%;
        }

        .slip-dapur {
            width: 100%;
            padding: 2mm 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        hr { border: none; border-top: 1px dashed #000; margin: 2mm 0; }
    </style>
</head>
<body>
<div class="container">
    @foreach($groupedOrders as $kitchenData)
    <div class="slip-dapur">
        <div class="text-center">
            <h3 style="margin:0; font-size: 11px;">XEPPO</h3>
            <small style="font-size: 9px; font-weight:bold;">{{ $kitchenData['kitchen_name'] }}</small>
            <hr>
        </div>

        <table width="100%">
            <tr><td>Meja</td><td class="text-right">{{ $reservation->area->name ?? '-' }}</td></tr>
            <tr><td>Nama</td><td class="text-right">{{ $reservation->customer_name }}</td></tr>
            <tr><td>Jumlah Tamu</td><td class="text-right">{{ $reservation->number_of_guests }} Orang</td></tr>
            <tr><td>Waktu Datang</td><td class="text-right">{{ $reservation->reservation_time }}</td></tr>
        </table>
        <hr>

        @foreach($kitchenData['items'] as $item)
        <div style="margin-bottom: 1mm;">
            <div style="display: flex; justify-content: space-between;">
                <span>{{ $item['qty'] }}x {{ $item['name'] }}</span>
            </div>
        </div>
        @endforeach

        <hr>
        <div class="text-center">
            <small>TOTAL ITEM: {{ array_sum(array_column($kitchenData['items'], 'qty')) }}</small>
        </div>
    </div>
    @endforeach
</div>

<script>
    @if($autoPrint)
        window.onload = function() { window.print(); }
    @endif
</script>
</body>
</html>