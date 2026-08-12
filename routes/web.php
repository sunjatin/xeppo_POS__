<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\Admin\ReportController;

// Route tipuan
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// (Homescreen)
Route::get('/', [HomeController::class, 'index'])->name('home');

// (Tampilan Form)
Route::get('/reservasi', [ReservationController::class, 'create'])->name('reservasi.create');

// Simpan Reservasi (Ketika tombol submit diklik)
Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');
Route::get('/pembayaran/{id}', [ReservationController::class, 'showPayment'])->name('payment.show');

//konfirmasi pembayaran reservasi VIA WA
Route::post('/reservation/{id}/confirm-whatsapp', [ReservationController::class, 'confirmWhatsapp'])->name('reservation.confirm');

// Route buat keranjang 
// Route::post('/add-to-cart', [HomeController::class, 'addToCart'])->name('cart.add');
Route::post('/update-cart', [HomeController::class, 'updateCart'])->name('cart.update');

Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('availability.check');


// Route Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('areas', AreaController::class);

    // Route untuk download struk sebagai Gambar (PNG)
    Route::get('/reservations/{id}/download-image/{type}', [AdminReservationController::class, 'downloadStrukImage'])->name('reservations.download_image');

    // Route untuk download Struk format PDF (Ukuran Thermal)
    Route::get('/reservations/{id}/download-pdf/{type}', [AdminReservationController::class, 'downloadReceiptPdf'])->name('reservations.download_pdf');

    // Route untuk Print HTML langsung (Thermal Friendly)
    Route::get('/reservations/{id}/print-thermal/{type}', [AdminReservationController::class, 'printThermal'])->name('reservations.print_thermal');

    // Route khusus struk dapur (Thermal & A4)
    Route::get('/reservations/{id}/print-kitchen-thermal', [AdminReservationController::class, 'printKitchenThermal'])->name('reservations.print_kitchen_thermal');

    // Group yang butuh login
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // CRUD Menu
        Route::resource('menus', MenuController::class);

        // CRUD Reservasi
        Route::resource('reservations', AdminReservationController::class);

        // Route untuk update status via AJAX
        Route::post('/reservations/{id}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.status');

        // Download Struk admin
        Route::get('/reservations/{id}/struk/{type}', [AdminReservationController::class, 'printStruk'])->name('reservations.struk');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // profile admin
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Kitchen
        Route::resource('kitchens', KitchenController::class);

        // Route untuk halaman laporan (menampilkan tabel data)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        // Route untuk download PDF (aksi terpisah)
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });
});
