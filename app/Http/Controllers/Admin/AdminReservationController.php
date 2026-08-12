<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Barryvdh\DomPDF\Facade\Pdf;



class AdminReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::latest();

        if ($request->search) {
            $query->where('customer_name', 'like', "%{$request->search}%")
                ->orWhere('phone_number', 'like', "%{$request->search}%");
        }

        $reservations = $query->paginate(10);
        return view('admin.reservations.index', compact('reservations'));
    }

    public function edit(Reservation $reservation)
    {
        $menus = Menu::where('is_active', true)->get(); // Ambil semua menu yang aktif
        return view('admin.reservations.edit', compact('reservation', 'menus'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'required',
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        // Proses ulang data menu jika ada perubahan
        $orderDetails = [];
        $totalPrice = 0;

        if ($request->menus) {
            foreach ($request->menus as $id => $item) {
                $qty = (int) $item['qty'];
                if ($qty > 0) {
                    $subtotal = $item['price'] * $qty;
                    $totalPrice += $subtotal;

                    $orderDetails[] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'qty' => $qty,
                        'subtotal' => $subtotal
                    ];
                }
            }
        }

        $reservation->update([
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'number_of_guests' => $request->number_of_guests,
            'status' => $request->status,
            'notes' => $request->notes,
            'menus' => $orderDetails,
            'total_price' => $totalPrice
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Data reservasi & pesanan berhasil diupdate');
    }


    public function downloadReceiptPdf($id, $type)
    {
        $reservation = Reservation::findOrFail($id);

        // Load view khusus PDF
        $pdf = Pdf::loadView('admin.reservations.pdf_receipt', compact('reservation', 'type'));

        // HITUNG TINGGI KERTAS SECARA DINAMIS (dalam milimeter)
        // Tinggi Dasar (Logo + Header + Info) ~ 60mm
        $heightMm = 60;
        // Tinggi per item menu ~ 8mm
        $heightMm += (count($reservation->menus) * 8);
        // Tinggi Footer ~ 25mm
        $heightMm += 25;

        // Konversi mm ke points (1 mm = 2.834645669 points)
        $heightPt = $heightMm * 2.834645669;

        // Set Ukuran Kertas KUSTOM: Lebar 50mm, Tinggi Dinamis
        // 50mm = 141.73 points
        $pdf->setPaper([0, 0, 141.73, $heightPt], 'portrait');

        $filename = 'struk-' . $reservation->id . '.pdf';
        return $pdf->stream($filename); // Preview di browser
        // return $pdf->download($filename); // Langsung download
    }
    public function printStruk($id, $type = 'dp')
    {
        $reservation = Reservation::with('area')->findOrFail($id);

        $groupedOrders = [];
        $kitchens = \App\Models\Kitchen::all()->keyBy('id');

        if ($reservation->menus) {
            foreach ($reservation->menus as $item) {
                // 1. Coba ambil kitchen_id dari data pesanan (JSON)
                $kid = $item['kitchen_id'] ?? null;

                // 2. Jika tidak ada (data lama), coba cari di tabel Menu (Master Data)
                if (!$kid && isset($item['id'])) {
                    $menuMaster = \App\Models\Menu::find($item['id']);
                    $kid = $menuMaster->kitchen_id ?? 0;
                }

                // Jika dapur belum ada di grup, buat wadah baru
                if (!isset($groupedOrders[$kid])) {
                    $groupedOrders[$kid] = [
                        'kitchen_name' => $kitchens[$kid]->name ?? 'Dapur Umum',
                        'items' => []
                    ];
                }

                // Masukkan item ke grup dapur yang sesuai
                $groupedOrders[$kid]['items'][] = $item;
            }
        }

        return view('admin.reservations.struk', compact('reservation', 'type', 'groupedOrders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = $request->status;
        $reservation->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
    }

    // public function printThermal($id, $type)
    // {
    //     $reservation = Reservation::findOrFail($id);
    //     return view('admin.reservations.print_thermal', compact('reservation', 'type'));
    // }

    public function printThermal($id, $type)
    {
        $reservation = Reservation::findOrFail($id);

        // buat cek preview atau cetak langsung
        // Kalo ada parameter ?preview=true, maka auto print dimatikan
        $autoPrint = !request()->query('preview');

        return view('admin.reservations.print_thermal', compact('reservation', 'type', 'autoPrint'));
    }

    public function printKitchenThermal($id)
    {
        $reservation = Reservation::with('area')->findOrFail($id);

        // Logika pengelompokan dapur (sama seperti sebelumnya)
        $groupedOrders = [];
        $kitchens = \App\Models\Kitchen::all()->keyBy('id');

        if ($reservation->menus) {
            foreach ($reservation->menus as $item) {
                $kid = $item['kitchen_id'] ?? null;
                if (!$kid && isset($item['id'])) {
                    $menuMaster = \App\Models\Menu::find($item['id']);
                    $kid = $menuMaster->kitchen_id ?? 0;
                }

                if (!isset($groupedOrders[$kid])) {
                    $groupedOrders[$kid] = [
                        'kitchen_name' => $kitchens[$kid]->name ?? 'Dapur Umum',
                        'items' => []
                    ];
                }
                $groupedOrders[$kid]['items'][] = $item;
            }
        }

        // Cek mode preview
        $autoPrint = !request()->query('preview');

        return view('admin.reservations.print_kitchen_thermal', compact('reservation', 'groupedOrders', 'autoPrint'));
    }

    public function downloadStrukImage($id, $type)
    {
        $reservation = Reservation::findOrFail($id);

        // --- [START: GD SCRIPT] ---
        // Kode ini men-generate gambar menggunakan Native PHP GD
        // Pastikan tidak ada karakter atau spasi sebelum tag <?php di atas file ini.

        $width = 380;
        $padding = 15;
        $lineHeight = 18;
        $charWidth = imagefontwidth(3);

        // Hitung Tinggi Dinamis
        $totalLines = 6 + 5 + (count($reservation->menus) * 2) + 6;
        $height = $totalLines * $lineHeight + 40;

        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $red = imagecolorallocate($im, 200, 50, 50);
        imagefill($im, 0, 0, $white);

        $y = 10;
        // Fungsi tulis teks (sama seperti sebelumnya)
        $writeLine = function ($text, $font = 3, $align = 'left', $color = null) use ($im, $width, $padding, $black, &$y, $lineHeight, $charWidth) {
            if ($color === null) $color = $black;
            $x = $padding;
            if ($align == 'center') {
                $textWidth = strlen($text) * $charWidth;
                $x = ($width - $textWidth) / 2;
            } elseif ($align == 'right') {
                $textWidth = strlen($text) * $charWidth;
                $x = $width - $padding - $textWidth;
            }
            imagestring($im, $font, $x, $y, $text, $color);
            $y += $lineHeight;
        };

        // Gambar Konten
        $writeLine('XEPPO', 5, 'center');
        $writeLine('Restoran & Cafe', 2, 'center');
        $writeLine('==============================', 2, 'center');
        $writeLine('Nama  : ' . $reservation->customer_name);
        $writeLine('Area  : ' . ($reservation->area->name ?? '-'));
        $writeLine('Tgl   : ' . date('d/m/Y', strtotime($reservation->reservation_date)));
        $writeLine('Jam   : ' . $reservation->reservation_time);
        $writeLine('Jml Tamu : ' . ($reservation->number_of_guests ?? '1') . ' Orang');
        $writeLine('==============================', 2, 'center');

        foreach ($reservation->menus as $item) {
            $writeLine($item['qty'] . 'x ' . $item['name'], 3, 'left');
            $priceText = 'Rp ' . number_format($item['subtotal'], 0, ',', '.');
            $priceWidth = strlen($priceText) * imagefontwidth(2);
            imagestring($im, 2, $width - $padding - $priceWidth, $y - $lineHeight, $priceText, $black);
        }

        $writeLine('==============================', 2, 'center');
        if ($type == 'dp') {
            $writeLine('Total      : Rp ' . number_format($reservation->total_price, 0, ',', '.'));
            $writeLine('DP (20%)   : Rp ' . number_format($reservation->dp_amount, 0, ',', '.'), 3, 'left', $red);
            $writeLine('Sisa       : Rp ' . number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.'));
        } else {
            $writeLine('TOTAL BAYAR', 3, 'center');
            $writeLine('Rp ' . number_format($reservation->total_price - $reservation->dp_amount, 0, ',', '.'), 4, 'center', $red);
        }
        $writeLine('==============================', 2, 'center');
        $y += 5;
        $writeLine('Terima Kasih', 2, 'center');

        // Crop Sisa Ruang Kosong
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $y]);
        if ($im2 !== FALSE) {
            imagedestroy($im);
            $im = $im2;
        }
        // --- [END: GD SCRIPT] ---

        // LOGIKA PREVIEW VS DOWNLOAD
        // Jika ada parameter ?download=1 di URL, maka paksa download.
        // Jika tidak, tampilkan gambar di browser (preview).
        if (request()->query('download') == 1) {
            header('Content-Description: File Transfer');
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="struk-xeppo.png"');
        } else {
            header('Content-Type: image/png');
            header('Content-Disposition: inline; filename="struk-xeppo.png"');
        }

        imagepng($im);
        imagedestroy($im);
        exit;
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Reservasi dihapus');
    }
}
