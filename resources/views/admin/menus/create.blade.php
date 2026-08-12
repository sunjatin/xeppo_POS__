@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-white fw-bold">Tambah Menu Baru</div>
            <div class="card-body">
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <!-- TAMBAHKAN INI -->
                    <div class="mb-3">
                        <label>Dapur Pengelola</label>
                        <select name="kitchen_id" class="form-select">
                            <option value="">-- Pilih Dapur --</option>
                            @foreach($kitchens as $kitchen)
                                <option value="{{ $kitchen->id }}">{{ $kitchen->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                        <label class="form-check-label">Aktifkan Menu?</label>
                    </div>

                    <button class="btn btn-primary w-100">Simpan</button>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection