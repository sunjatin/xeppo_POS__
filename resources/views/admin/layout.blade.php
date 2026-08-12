<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin RESTO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #8B0000; /* Warna XEPPO */
            padding-top: 20px;
            color: white;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar a {
            color: #fff;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #FFD700;
        }
        .sidebar .logo {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 10px;
        }
        .sidebar .logo h4 { color: #FFD700; font-weight: bold; margin: 0; }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
            /* Tambahkan logic JS untuk toggle sidebar di mobile jika perlu */
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h4>RESTO</h4>
            <small>Resto Admin</small>
        </div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.menus.index') }}" class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <i class="fas fa-utensils me-2"></i> Kelola Menu
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="{{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <i class="fas fa-book me-2"></i> Reservasi
            </a>
            <a href="{{ route('admin.areas.index') }}" class="{{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt me-2"></i> Kelola Area
            </a>
            <a href="{{ route('admin.kitchens.index') }}" class="{{ request()->routeIs('admin.kitchens.*') ? 'active' : '' }}">
                <i class="fas fa-fire me-2"></i> Kelola Dapur
            </a>
    
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs me-2"></i> Pengaturan
            </a>

            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line me-2"></i> Laporan Penjualan
            </a>

            <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fas fa-user-circle me-2"></i> Profil Saya
            </a>
            <a href="{{ route('admin.logout') }}" class="mt-5 text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
            
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>