@extends('layouts.app')

@section('styles')
<style>
    .jumbotron-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 20px;
    }
    .bottom-nav-xepo {
        background-color: var(--xepo-red);
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    /* Agar konten tidak ketutupan nav bawah */
    body { padding-bottom: 70px; }
    
    /* Styling tombol aksi di card */
    .card-action-wrapper {
        position: absolute;
        bottom: 10px;
        right: 10px;
        left: 10px;
        display: flex;
        justify-content: flex-end;
    }

    /* Tombol Tambah Awal */
    .btn-add-init {
        background-color: white;
        color: #8B0000;
        border: 1px solid #8B0000;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .btn-add-init:hover {
        background-color: #8B0000;
        color: white;
    }

    /* Tombol Stepper (Plus Minus) */
    .stepper-input {
        display: flex;
        align-items: center;
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .stepper-input button {
        width: 28px;
        height: 28px;
        border: none;
        background-color: #8B0000;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }
    .stepper-input button:first-child { border-radius: 20px 0 0 20px; }
    .stepper-input button:last-child { border-radius: 0 20px 20px 0; }
    .stepper-input .qty-display {
        width: 30px;
        text-align: center;
        font-weight: bold;
        color: #333;
        font-size: 13px;
        background: white;
    }

</style>
@endsection

@section('content')

<div class="p-3">
    <!-- HEADER -->
    <header class="d-flex justify-content-between align-items-center py-2 mb-3">
        <h2 class="fw-bolder mb-0" style="color: var(--xepo-red);">XEPPO</h2>
        <div class="d-flex gap-3 align-items-center">
            <!-- ICON KERANJANG BARU -->
            <a href="{{ route('reservasi.create') }}" class="text-dark position-relative" id="cart-icon">
                <i class="fas fa-shopping-cart fs-5"></i>
                <!-- Badge Angka -->
                @if($cartCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;" id="cart-badge">
                    {{ $cartCount }}
                </span>
                @endif
            </a>
            <!-- End Icon -->
            <i class="fas fa-bell fs-5 text-dark"></i>
            <i class="fas fa-user-circle fs-5 text-dark"></i>
        </div>
    </header>

       <!-- Quick Actions (Refactor) -->
    <section class="container mt-4 mb-3">
        <div class="row g-2">
            <!-- Tombol Reservasi -->
            <div class="col-12">
                <a href="{{ route('reservasi.create') }}" class="btn btn-danger w-100 py-3 rounded-4 shadow-sm d-flex justify-content-center align-items-center">
                    <i class="fas fa-calendar-check me-2"></i> 
                    <span>Reservasi Tempat</span>
                </a>
            </div>
            
            <!-- Tombol Menu Makan & Minuman -->
            <div class="col-6">
                <button class="btn btn-outline-danger w-100 py-2 rounded-pill small">
                    <i class="fas fa-utensils me-1"></i> Makanan
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-outline-danger w-100 py-2 rounded-pill small">
                    <i class="fas fa-coffee me-1"></i> Minuman
                </button>
            </div>
        </div>
    </section>

    <!-- ALERT SUKSES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- QUICK ACTIONS -->
    <section class="mb-4">
        <div class="row g-2 text-center">
            <div class="col">
                <a href="{{ route('reservasi.create') }}" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-semibold">Booking</a>
            </div>
            <div class="col">
                <button class="btn btn-outline-secondary rounded-pill w-100 py-2 fw-semibold disabled">Menu Makan</button>
            </div>
            <div class="col">
                <button class="btn btn-outline-secondary rounded-pill w-100 py-2 fw-semibold disabled">Menu Minuman</button>
            </div>
        </div>
    </section>

    <!-- MENU LIST -->
    <section>
        <h5 class="fw-bold mb-3">Menu Kami</h5>
        <div class="row g-3">
                
        @foreach($menus as $menu)
            
            @php
                $currentQty = $cart[$menu->id] ?? 0; 
            @endphp
            

            <div class="col-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden" style="position: relative;">
                        <div style="height: 120px; overflow: hidden;">
                            <img src="{{ $menu->image ? asset('storage/'.$menu->image) : 'https://via.placeholder.com/300' }}" 
                                class="card-img-top w-100 h-100" 
                                style="object-fit: cover;" alt="{{ $menu->name }}">
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 text-truncate" style="font-size: 0.9rem;">{{ $menu->name }}</h6>
                            <small class="text-danger fw-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</small>
                        </div>

                        <!-- Wrapper Tombol Aksi -->
                        <div class="card-action-wrapper">
                            <div id="action-area-{{ $menu->id }}">
                                @if($currentQty > 0)
                                    <!-- Tampilan jika sudah ada di keranjang -->
                                    <div class="stepper-input">
                                        <button type="button" onclick="updateCart({{ $menu->id }}, 'reduce')"><i class="fas fa-minus fa-xs"></i></button>
                                        <span class="qty-display" id="qty-{{ $menu->id }}">{{ $currentQty }}</span>
                                        <button type="button" onclick="updateCart({{ $menu->id }}, 'add')"><i class="fas fa-plus fa-xs"></i></button>
                                    </div>
                                @else
                                    <!-- Tampilan Awal -->
                                    <button class="btn btn-add-init" onclick="updateCart({{ $menu->id }}, 'add')">
                                        <i class="fas fa-plus me-1"></i> Tambah
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<!-- BOTTOM NAVIGATION -->
<nav class="navbar bottom-nav-xepo py-2 shadow-lg">
    <div class="container d-flex justify-content-around">
        <a href="#" class="text-white-50 text-center text-decoration-none">
            <i class="fas fa-shopping-bag fs-5"></i>
            <div><small style="font-size:9px;">Order</small></div>
        </a>
        <!-- TOMBOL BOOKING BAWAH -->
        <a href="{{ route('reservasi.create') }}" class="text-warning text-center text-decoration-none fw-bold">
            <i class="fas fa-calendar-alt fs-5"></i>
            <div><small style="font-size:9px;">Booking</small></div>
        </a>
        <a href="#" class="text-white-50 text-center text-decoration-none">
            <i class="fas fa-map-marker-alt fs-5"></i>
            <div><small style="font-size:9px;">Check In</small></div>
        </a>
    </div>
</nav>

{{-- javascript buat keranjang --}}
<script>
    function addCart(btn) {
        var id = btn.getAttribute('data-id');
        
        // Kirim request AJAX
        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ menu_id: id })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Update angka badge
                var badge = document.getElementById('cart-badge');
                if(badge) {
                    badge.innerText = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    // Jika badge belum ada (sebelumnya 0), buat baru
                    var icon = document.getElementById('cart-icon');
                    var newBadge = document.createElement('span');
                    newBadge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
                    newBadge.style.fontSize = "9px";
                    newBadge.id = "cart-badge";
                    newBadge.innerText = data.count;
                    icon.appendChild(newBadge);
                }
                
                // Efek animasi sederhana (opsional)
                var icon = document.getElementById('cart-icon');
                icon.style.transform = "scale(1.2)";
                setTimeout(() => icon.style.transform = "scale(1)", 200);
            }
        });
    }
</script>

<script>
    function updateCart(id, action) {
        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ menu_id: id, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // 1. Update Badge Keranjang
                var badge = document.getElementById('cart-badge');
                if(data.count > 0) {
                    if(badge) {
                        badge.innerText = data.count;
                    } else {
                        var icon = document.getElementById('cart-icon');
                        var newBadge = document.createElement('span');
                        newBadge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
                        newBadge.style.fontSize = "9px";
                        newBadge.id = "cart-badge";
                        newBadge.innerText = data.count;
                        icon.appendChild(newBadge);
                    }
                } else if(badge) {
                    badge.remove();
                }

                // 2. Update Tampilan Card
                var actionArea = document.getElementById('action-area-' + id);
                var newQty = data.itemQty;

                if(newQty > 0) {
                    // Jika qty > 0, pastikan tampilan Stepper
                    actionArea.innerHTML = `
                        <div class="stepper-input">
                            <button type="button" onclick="updateCart(${id}, 'reduce')"><i class="fas fa-minus fa-xs"></i></button>
                            <span class="qty-display" id="qty-${id}">${newQty}</span>
                            <button type="button" onclick="updateCart(${id}, 'add')"><i class="fas fa-plus fa-xs"></i></button>
                        </div>
                    `;
                } else {
                    // Jika qty 0, kembali ke Tombol Tambah
                    actionArea.innerHTML = `
                        <button class="btn btn-add-init" onclick="updateCart(${id}, 'add')">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    `;
                }
            }
        });
    }
</script>

@endsection