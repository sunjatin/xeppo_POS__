@extends('admin.layout')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Kelola Area Restoran</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Tambah Area
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">No</th>
                    <th>Nama Area</th>
                    <th>Kapasitas Max (Rombongan/Hari)</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $key => $area)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $area->name }}</td>
                    <td>{{ $area->capacity }} Rombongan</td>
                    <td>
                        <!-- TOMBOL EDIT: Menambahkan data-id, data-name, dll -->
                        <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                data-id="{{ $area->id }}" 
                                data-name="{{ $area->name }}" 
                                data-capacity="{{ $area->capacity }}"
                                data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <form action="{{ route('admin.areas.destroy', $area->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus area ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada area.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.areas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Area Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Area</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Saung, Lesehan" required>
                    </div>
                    <div class="mb-3">
                        <label>Max Rombongan/Hari</label>
                        <input type="number" name="capacity" class="form-control" value="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT (Hanya 1, bukan loop) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label>Nama Area</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Max Rombongan/Hari</label>
                        <input type="number" name="capacity" id="edit-capacity" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Script untuk mengisi data ke Modal Edit
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editModal');
        
        editModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            var button = event.relatedTarget;
            
            // Ambil data dari tombol
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var capacity = button.getAttribute('data-capacity');
            
            // Isi ke form
            var form = document.getElementById('editForm');
            form.action = "{{ url('admin/areas') }}/" + id; // Set URL action: /admin/areas/{id}
            
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-capacity').value = capacity;
        });
    });
</script>
@endsection