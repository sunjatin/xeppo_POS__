@extends('admin.layout')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Kelola Dapur (Cetak Struk)</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Tambah Dapur
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
                    <th>Nama Dapur</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Menu</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Perhatikan variabel $kitchens di sini -->
                @forelse($kitchens as $key => $kitchen)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $kitchen->name }}</td>
                    <td>{{ $kitchen->description }}</td>
                    <td>{{ $kitchen->menus_count ?? 0 }} Menu</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                data-id="{{ $kitchen->id }}" 
                                data-name="{{ $kitchen->name }}" 
                                data-desc="{{ $kitchen->description }}"
                                data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <form action="{{ route('admin.kitchens.destroy', $kitchen->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada data dapur.</td>
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
            <form action="{{ route('admin.kitchens.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dapur Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Dapur</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Dapur 1 - Ayam Bakakak" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" placeholder="Keterangan singkat"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dapur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label>Nama Dapur</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" id="edit-desc" class="form-control"></textarea>
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
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editModal');
        
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var desc = button.getAttribute('data-desc');
            
            var form = document.getElementById('editForm');
            form.action = "{{ url('admin/kitchens') }}/" + id;
            
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-desc').value = desc;
        });
    });
</script>
@endsection