<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Area;
use App\Models\Kitchen;

class ReservationController extends Controller
{
    public function create()
    {
        $menus = Menu::all();
        $cart = session()->get('cart', []);
        $areas = Area::all(); // Ambil data area

        return view('user.reservation', compact('menus', 'cart', 'areas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'reservation_date' => 'required|date|after:today',
            'reservation_time' => 'required',
            'payment_method' => 'required|in:qris,transfer',
            'area_id' => 'required|exists:areas,id',
            'menus' => 'required|array',
        ]);

        // Validasi 24 Jam
        $lastReservation = Reservation::where('phone_number', $request->phone_number)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($lastReservation) {
            return back()->with('error', 'Maaf, Anda hanya bisa melakukan reservasi 1x dalam 24 jam.')->withInput();
        }

        // 2. Proses Data Menu & Hitung Total
        $orderDetails = [];
        $totalPrice = 0; // Inisialisasi awal
        $hasItems = false;

        foreach ($request->menus as $item) {
            $qty = (int) $item['qty'];
            if ($qty > 0) {
                $hasItems = true;
                $subtotal = $item['price'] * $qty;
                $totalPrice += $subtotal;

                $menuData = Menu::find($item['id']); //untuk ambil data menu nanti dipecah

                $orderDetails[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'kitchen_id' => $menuData->kitchen_id ?? null
                ];
            }
        }

        if (!$hasItems) {
            return back()->with('error', 'Anda harus memesan minimal 1 menu.')->withInput();
        }

        // 3. Hitung DP 20%
        // Rumus: Total Harga * 20% (0.2)
        $dpAmount = $totalPrice * 0.20;

        // 4. Simpan ke Database
        $reservation = Reservation::create([
            'customer_name' => $request->customer_name,
            'address'       => $request->address,
            'phone_number'  => $request->phone_number,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'number_of_guests' => $request->number_of_guests ?? 1,
            'area_id'       => $request->area_id, // Simpan Area
            'menus'         => $orderDetails,
            'total_price'   => $totalPrice,       // Simpan Total Harga
            'dp_amount'     => $dpAmount,         // Simpan Nominal DP
            'payment_method' => $request->payment_method,
            'notes'         => $request->notes,
            'status'        => 'pending'
        ]);

        // 5. Arahkan ke Halaman Pembayaran
        return redirect()->route('payment.show', $reservation->id);
    }

    // Menampilkan Halaman Pembayaran
    public function showPayment($id)
    {
        $reservation = Reservation::with('area')->findOrFail($id); // Tambahkan ->with('area')

        $bank_name = Setting::get('bank_name');
        $bank_account = Setting::get('bank_account');
        $bank_holder = Setting::get('bank_holder');
        $qris_image = Setting::get('qris_image');
        $whatsapp = Setting::get('whatsapp_number');

        return view('user.payment', compact(
            'reservation',
            'bank_name',
            'bank_account',
            'bank_holder',
            'qris_image',
            'whatsapp'
        ));
    }

    // FUNGSI BARU UNTUK KONFIRMASI WA
    public function confirmWhatsapp($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            // Update status
            $reservation->status = 'confirmed';
            $reservation->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkAvailability(Request $request)
    {
        $date = $request->date;
        $areaId = $request->area_id;

        // Cari area untuk mendapat kapasitas max
        $area = Area::find($areaId);
        if (!$area) return response()->json(['available' => false, 'message' => 'Area tidak valid']);

        // Hitung jumlah reservasi di tanggal tsb untuk area tsb
        $count = Reservation::where('reservation_date', $date)
            ->where('area_id', $areaId)
            ->where('status', '!=', 'cancelled') // Jangan hitung yang dibatalkan
            ->count();

        if ($count >= $area->capacity) {
            return response()->json(['available' => false, 'message' => 'Maaf, area ini sudah penuh untuk tanggal tersebut.']);
        }

        return response()->json(['available' => true]);
    }
}
