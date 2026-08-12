<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_active', true)->latest()->get();

        $jumbotron_title = Setting::get('jumbotron_title', 'Be Ready for Iftar');
        $jumbotron_subtitle = Setting::get('jumbotron_subtitle', 'Segera pesan tempat.');
        $jumbotron_image = Setting::get('jumbotron_image', null);

        // Ambil isi keranjang untuk ditampilkan di landing page
        $cart = session()->get('cart', []);
        $cartCount = array_sum($cart);

        return view('user.landing', compact('menus', 'jumbotron_title', 'jumbotron_subtitle', 'jumbotron_image', 'cartCount', 'cart'));
    }

    // Fungsi baru untuk mengelola update keranjang (Tambah/Kurang)
    public function updateCart(Request $request)
    {
        $menuId = $request->menu_id;
        $action = $request->action; // 'add' atau 'reduce'

        $cart = session()->get('cart', []);

        if ($action == 'add') {
            if (isset($cart[$menuId])) {
                $cart[$menuId]++;
            } else {
                $cart[$menuId] = 1;
            }
        }

        if ($action == 'reduce') {
            if (isset($cart[$menuId])) {
                $cart[$menuId]--;
                if ($cart[$menuId] <= 0) {
                    unset($cart[$menuId]); // Hapus dari keranjang jika 0
                }
            }
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'count' => array_sum($cart),
            'itemQty' => $cart[$menuId] ?? 0 // Kirim qty item spesifik ini
        ]);
    }

    // Untuk menambah item ke keranjang via AJAX
    public function addToCart(Request $request)
    {
        $menuId = $request->menu_id;

        // Ambil data keranjang dari session, default array kosong
        $cart = session()->get('cart', []);

        // Jika menu sudah ada, tambah qty
        if (isset($cart[$menuId])) {
            $cart[$menuId]++;
        } else {
            $cart[$menuId] = 1;
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);

        // Kembalikan response JSON untuk AJAX
        return response()->json([
            'success' => true,
            'count' => array_sum($cart) // Total semua item
        ]);
    }
}
