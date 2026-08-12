<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Thermal</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 9px; /* Diperkecil sedikit agar muat dengan margin */
            color: #000;
            background-color: #fff;
            /* Penting: Margin 0 di body, nanti konten yang atur lebar */
            margin: 0; 
            padding: 0;
        }

        /* PENGATURAN SAAT PRINT */
        @media print {
            @page {
                size: 50mm auto; /* Ukuran kertas 50mm */
                margin: 0; /* Hapus margin bawaan browser */
            }
            
            body {
                /* Lebar konten dibuat 48mm, lalu otomatis di-center */
                width: 44mm; 
                margin: 0 auto; 
                padding: 0;
            }
            
            .no-print { display: none; }
        }

        /* PENGATURAN SAAT PREVIEW DI LAYAR */
        @media screen {
            body {
                width: 44mm;
                margin: 0 auto;
                background-color: #f0f0f0;
                border: 1px dashed #ccc; /* Garis batas kertas di preview */
            }
        }

        .container {
            padding: 1mm 0; /* Jarak atas bawah */
            width: 100%;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <!-- Header diperkecil -->
            <h3 style="margin:0; font-size: 11px;">XEPPO</h3>
            <small style="font-size: 8px;">Restoran & Cafe</small><br>
            <small>Jl. Contoh Alamat No. 123</small>
            <hr>
        </div>
        {{-- style="text-align: right;" --}}
        <table>
            <tr><td>Nama</td><td class="text-right">{{ $reservation->customer_name }}</td></tr>
            <tr><td>Area</td><td class="text-right">{{ $reservation->area->name ?? '-' }}</td></tr>
            <tr><td>TGL</td><td class="text-right">{{ date('d/m/Y', strtotime($reservation->reservation_date)) }}</td></tr>
            <tr><td>Jumlah Tamu</td><td class="text-right">{{ $reservation->number_of_guests }} Orang</td></tr>
            <tr><td>Jam</td><td class="text-right">{{ $reservation->reservation_time }}</td></tr>
        </table>

        <hr>

        <!-- ITEMS -->
        @foreach($reservation->menus as $item)
        <div style="margin-bottom: 1mm;">
            <div style="display: flex; justify-content: space-between;">
                <span>{{ $item['qty'] }}x {{ $item['name'] }}</span>
            </div>
            <div style="text-align: right;">
                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach

        <hr>

        <!-- TOTAL -->
        @if($type == 'dp')
        <table>
            <tr><td>Total</td><td class="text-right">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</td></tr>
            <tr style="font-weight:bold; font-size:11px;"><td>WAJIB DP (20%)</td><td class="text-right">Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Bayar</td><td class="text-right">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</td></tr>
        </table>

        <hr>
        <div class="text-center">
            <small>Mohon Lunasi saat Datang ke XEPPO</small><br>
            <small>TERIMA KASIH</small>
        </div>

        @else
        <div class="text-center">
            <p class="fw-bold" style="font-size:11px; margin:2px 0;">TOTAL BAYAR</p>
            <p style="font-size:11px; font-weight:bold; margin:0;">Rp {{ number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.') }}</p>
        </div>

        <hr>
        <div class="text-center">
            <small>TERIMA KASIH<br>Sudah Datang ke XEPPO</small>
        </div>
        @endif

    </div>

    <script>
        // Cuma auto print kalo $autoPrint true (bukan mode preview)
        @if($autoPrint)
            window.onload = function() {
                window.print();
            }
        @endif
    </script>
</body>
</html>