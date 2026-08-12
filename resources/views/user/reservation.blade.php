@extends('layouts.app')

@section('styles')
<style>
    .form-control:focus { border-color: var(--xepo-red); box-shadow: 0 0 0 0.2rem rgba(139,0,0,0.25); }
    /* Styling untuk item menu di form */
    .menu-item-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        background: #fff;
    }
    .menu-item-card img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 10px;
    }
    .qty-control {
        display: flex;
        align-items: center;
    }
    .qty-control button {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #ccc;
        background: white;
        font-weight: bold;
    }
    .qty-control input {
        width: 40px;
        text-align: center;
        border: none;
        font-weight: bold;
    }
</style>
@endsection

@section('content')

<div class="pb-5">
    <!-- HEADER FORM -->
    <div class="py-3 mb-3" style="background-color: var(--xepo-red);">
        <div class="d-flex align-items-center px-3">
            <a href="{{ route('home') }}" class="text-white me-3"><i class="fas fa-arrow-left"></i></a>
            <h5 class="text-white mb-0">Form Reservasi</h5>
        </div>
    </div>

    <div class="px-3">
         <!-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN ERROR -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups!</strong> Ada kesalahan pengisian.<br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
         <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <!-- AKHIR BLOK ERROR -->
        <form action="{{ route('reservasi.store') }}" method="POST" id="reservationForm">
            @csrf
            
            <!-- Data Diri -->
            <div class="card border-0 shadow-sm mb-4 p-3 rounded-4">
                <h6 class="fw-bold mb-3">Data Diri</h6>
                <div class="mb-3">
                    <label class="form-label small text-muted">Nama Lengkap</label>
                    <input type="text" name="customer_name" class="form-control rounded-3" placeholder="Nama Anda" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Alamat</label>
                    <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Alamat Lengkap" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">No. HP (WhatsApp)</label>
                    <input type="text" name="phone_number" class="form-control rounded-3" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>

            <!-- Detail Reservasi -->
            <div class="card border-0 shadow-sm mb-4 p-3 rounded-4">
                <h6 class="fw-bold mb-3">Detail Kedatangan</h6>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small text-muted">Tanggal</label>
                        <input type="date" name="reservation_date" id="date-input" class="form-control rounded-3" required>
                        <small id="date-status" class="text-danger mt-1 d-none">Tanggal ini sudah penuh!</small>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small text-muted">Jam Datang</label>
                        <input type="time" name="reservation_time" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small text-muted">Jumlah Tamu</label>
                    <input type="number" name="number_of_guests" class="form-control rounded-3" placeholder="2 Orang" min="1" required>
                </div>
                <div class="mb-3">
                <label class="form-label small text-muted">Pilih Area</label>
                <select name="area_id" id="area-select" class="form-select rounded-3" required>
                    <option value="" disabled selected>-- Pilih Area --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
                </div>
            </div>

            <!-- Pilih Menu dengan Quantity -->
            <div class="card border-0 shadow-sm mb-4 p-3 rounded-4">
                <h6 class="fw-bold mb-3">Pilih Pesanan</h6>
                <div id="menu-list">
                    @foreach($menus as $menu)
                        <!-- TAMBAHKAN BLOK PHP INI -->
                        @php
                            // Cek apakah ada data keranjang dari session untuk menu ini
                            // Jika ada ambil qty-nya, jika tidak default 0
                            $currentQty = $cart[$menu->id] ?? 0;
                        @endphp

                        <div class="menu-item-card">
                            <img src="{{ $menu->image ? asset('storage/'.$menu->image) : 'https://via.placeholder.com/50' }}" alt="{{ $menu->name }}">
                            <div class="flex-grow-1">
                                <div class="fw-bold small">{{ $menu->name }}</div>
                                <div class="text-danger small">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            </div>
                            
                            <!-- Kontrol Quantity -->
                            <div class="qty-control">
                                <button type="button" onclick="updateQty({{ $menu->id }}, -1)">-</button>
                                <!-- Sekarang $currentQty sudah terdefinisi -->
                                <input type="text" id="qty-{{ $menu->id }}" value="{{ $currentQty }}" readonly>
                                <button type="button" onclick="updateQty({{ $menu->id }}, 1)">+</button>
                            </div>
                            
                            <!-- Hidden input untuk dikirim ke server -->
                            <input type="hidden" name="menus[{{ $menu->id }}][id]" value="{{ $menu->id }}">
                            <input type="hidden" name="menus[{{ $menu->id }}][qty]" id="input-qty-{{ $menu->id }}" value="{{ $currentQty }}">
                            <input type="hidden" name="menus[{{ $menu->id }}][price]" value="{{ $menu->price }}">
                            <input type="hidden" name="menus[{{ $menu->id }}][name]" value="{{ $menu->name }}">
                        </div>
                    @endforeach
                </div>
                
                <!-- Total Harga Realtime -->
                <div class="border-top mt-3 pt-3 d-flex justify-content-between">
                    <span class="fw-bold">Total Sementara:</span>
                    <span class="fw-bold text-danger" id="total-display">Rp 0</span>
                </div>
            </div>

            <!-- Pembayaran DP -->
            <div class="card border-0 shadow-sm mb-4 p-3 rounded-4">
                <h6 class="fw-bold mb-3">Metode Bayar DP</h6>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="transfer" checked onchange="togglePayment()">
                        <label class="form-check-label" for="transfer">Transfer Bank</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="qris" id="qris" onchange="togglePayment()">
                        <label class="form-check-label" for="qris">QRIS</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-xepo w-100 py-3 shadow rounded-4 fw-bold">
                LANJUTKAN KE PEMBAYARAN
            </button>
        </form>
    </div>
</div>

<script>
    // Objek untuk menyimpan status qty sementara di JS
    const prices = {};
    @foreach($menus as $menu)
        prices[{{ $menu->id }}] = {{ $menu->price }};
    @endforeach

    function updateQty(id, change) {
        let currentQty = parseInt(document.getElementById('qty-' + id).value);
        let newQty = currentQty + change;
        
        if (newQty < 0) newQty = 0; // Minimal 0

        // Update tampilan
        document.getElementById('qty-' + id).value = newQty;
        // Update input hidden yang akan dikirim
        document.getElementById('input-qty-' + id).value = newQty;
        
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        for (let id in prices) {
            let qty = parseInt(document.getElementById('qty-' + id).value);
            total += (prices[id] * qty);
        }
        document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener("DOMContentLoaded", function() {
        calculateTotal();
    });

</script>

<script>
    // ... script lama ...

    const checkUrl = "{{ route('availability.check') }}";
    const dateInput = document.getElementById('date-input');
    const areaSelect = document.getElementById('area-select');
    const dateStatus = document.getElementById('date-status');

    function checkDate() {
        const date = dateInput.value;
        const areaId = areaSelect.value;

        if (date && areaId) {
            fetch(checkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ date: date, area_id: areaId })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.available) {
                    // Jika Penuh
                    alert(data.message); // Tampilkan popup
                    dateInput.value = ""; // Kosongkan input
                    dateStatus.classList.remove('d-none');
                    dateStatus.innerText = data.message;
                } else {
                    dateStatus.classList.add('d-none');
                }
            });
        }
    }

    // Pasang event listener
    dateInput.addEventListener('change', checkDate);
    areaSelect.addEventListener('change', checkDate); // Cek ulang jika ganti area

</script>

@endsection