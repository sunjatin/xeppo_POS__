@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-white fw-bold">Edit Menu</div>
            <div class="card-body">
                <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="name" class="form-control" value="{{ $menu->name }}" required>
                    </div>

                    <!-- Dropdown Dapur -->
                    <div class="mb-3">
                        <label>Dapur Pengelola</label>
                        <select name="kitchen_id" class="form-select">
                            <option value="">-- Tidak Ada Dapur --</option>
                            @foreach($kitchens as $kitchen)
                                <option value="{{ $kitchen->id }}" {{ $menu->kitchen_id == $kitchen->id ? 'selected' : '' }}>
                                    {{ $kitchen->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih dapur yang akan mengerjakan menu ini (untuk cetak struk koki).</small>
                    </div>
                    <!-- SELESAI -->
                    
                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control" value="{{ $menu->price }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ $menu->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label>Gambar Saat Ini</label><br>
                        @if($menu->image)
                            <img src="{{ asset('storage/'.$menu->image) }}" width="100" class="mb-2 rounded">
                        @endif
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin ganti gambar.<br>*maks 1mb</br></small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $menu->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Aktifkan Menu?</label>
                    </div>

                    <button class="btn btn-warning w-100">Update</button>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection