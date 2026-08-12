@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Kelola Menu</h3>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Menu
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Dapur</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $key => $menu)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>
                        @if($menu->image)
                            <img src="{{ asset('storage/'.$menu->image) }}" width="60" class="rounded">
                        @else
                            <img src="https://via.placeholder.com/60" class="rounded">
                        @endif
                    </td>
                    <td>{{ $menu->name }}</td>
                    <!-- TAMBAHAN DAPUR -->
                    <td>
                        @if($menu->kitchen)
                            <span class="badge bg-info">{{ $menu->kitchen->name }}</span>
                        @else
                            <span class="badge bg-secondary">Belum Ditentukan</span>
                        @endif
                    </td>
                    <!-- AKHIR TAMBAHAN -->
                    <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td>
                        @if($menu->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada menu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $menus->links() }}
    </div>
</div>
@endsection