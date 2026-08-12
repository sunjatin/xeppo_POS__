@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Data Reservasi</h3>
    <form method="GET" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari nama/telp..." value="{{ request('search') }}">
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
</div>

<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telp</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $r)
                <tr>
                    <td>{{ $r->customer_name }}</td>
                    <td>{{ $r->phone_number }}</td>
                    <td>{{ $r->reservation_date }}</td>
                    <td>Rp {{ number_format($r->total_price, 0, ',', '.') }}</td>
                    <td>
                        <select class="form-select form-select-sm status-dropdown" data-url="{{ route('admin.reservations.status', $r->id) }}">
                            <option value="pending" {{ $r->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $r->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ $r->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        
                        <!-- Tombol DP -->
                        <button onclick="openReceipt(
                            '{{ route('admin.reservations.download_image', [$r->id, 'dp']) }}', 
                            '{{ route('admin.reservations.download_pdf', [$r->id, 'dp']) }}',
                            '{{ route('admin.reservations.print_thermal', [$r->id, 'dp']) }}'
                        )" class="btn btn-info btn-sm mt-1 w-100" title="Lihat Struk DP">
                            <i class="fas fa-eye"></i> DP
                        </button>
                    </td>
                    <td>
                        <!-- Tombol Lunas -->
                        <button onclick="openReceipt(
                            '{{ route('admin.reservations.download_image', [$r->id, 'pelunasan']) }}', 
                            '{{ route('admin.reservations.download_pdf', [$r->id, 'pelunasan']) }}',
                            '{{ route('admin.reservations.print_thermal', [$r->id, 'pelunasan']) }}'
                        )" class="btn btn-success btn-sm mb-1">
                            <i class="fas fa-eye"></i> Lunas
                        </button>

                        <!-- Tombol Dapur -->
                        <button onclick="openReceiptKitchen(
                            '{{ route('admin.reservations.print_kitchen_thermal', $r->id) }}', 
                            '{{ route('admin.reservations.struk', [$r->id, 'dapur']) }}'
                        )" class="btn btn-dark btn-sm mb-1">
                            <i class="fas fa-utensils"></i> Dapur
                        </button>

                        <a href="{{ route('admin.reservations.edit', $r->id) }}" class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <form action="{{ route('admin.reservations.destroy', $r->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm mb-1"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $reservations->links() }}
    </div>
</div>

{{-- <!-- MODAL POPUP -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm"> <!-- Ukuran disesuaikan -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Struk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" style="background-color: #f8f9fa; padding: 10px; height: 500px; overflow: hidden;">
                <!-- Iframe untuk Preview HTML -->
                <iframe id="receiptFrame" src="" style="width: 100%; height: 100%; border: none; background: white;"></iframe>
            </div>
            <div class="modal-footer d-block text-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                
                <!-- Tombol Download PDF (Ukuran Thermal) -->
                <a id="printBtn" href="#" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print"></i> Download PDF
                </a>

                <!-- Tombol Download PNG -->
                <a id="downloadBtn" href="#" class="btn btn-info">
                    <i class="fas fa-image"></i> Download PNG
                </a>
            </div>
        </div>
    </div>
</div> --}}

<!-- MODAL POPUP (Preview Gambar) -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Struk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" style="background-color: #f0f0f0; padding: 10px;">
                <!-- GANTI IFRAME MENJADI IMG -->
                <img id="receiptImage" src="" style="width: 100%; height: auto; border: 1px solid #ddd; background: white;">
                <small class="text-muted mt-2 d-block">Tahan gambar untuk menyimpan (Mobile)</small>
            </div>
            <div class="modal-footer d-block text-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                
                <!-- TOMBOL DOWNLOAD UNTUK PC -->
                <a id="downloadBtn" href="#" class="btn btn-primary" download="struk-xeppo.png">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    
function openReceipt(imageUrl, pdfUrl, thermalUrl) {
    var myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    var imgTag = document.getElementById('receiptImage');
    var dlBtn = document.getElementById('downloadBtn');
    
    // 1. Set Source Gambar (Preview Mode - tanpa ?download=1)
    // Kita gunakan imageUrl sebagai base, atau thermalUrl jika ingin HTML.
    // Di sini kita pakai imageUrl (Native GD) yang sudah kita modifikasi di controller.
    imgTag.src = imageUrl; 

    // 2. Set Tombol Download (Force Download Mode - dengan ?download=1)
    dlBtn.href = imageUrl + "?download=1";
    
    myModal.show();
}

    // Fungsi untuk Dapur (Jika masih pakai logika lama, sesuaikan)
    // Jika tombol dapur juga mau jadi gambar, gunakan fungsi yang sama.
    // Jika ingin tetap HTML/IFrame, pisahkan modalnya atau biarkan pakai fungsi lama.
    function openReceiptKitchen(printUrl, a4Url) {
        // Untuk Dapur, kita bisa biarkan user pilih: Mau lihat HTML atau Download PDF A4
        // Contoh: Langsung buka printUrl (HTML) di new tab untuk print thermal
        window.open(printUrl, '_blank');
    }

    // Fungsi untuk Dapur
    function openReceiptKitchen(printUrl, a4Url) {
        var myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        var frame = document.getElementById('receiptFrame');
        var dlBtn = document.getElementById('downloadBtn');
        var printBtn = document.getElementById('printBtn');
        
        // Preview & Print Thermal
        frame.src = printUrl + "?preview=true";
        printBtn.href = printUrl;
        
        // Download (A4 Layout)
        dlBtn.href = a4Url;
        dlBtn.removeAttribute('download');
        
        myModal.show();
    }

    // Script Update Status
    document.querySelectorAll('.status-dropdown').forEach(select => {
        select.addEventListener('change', function() {
            var url = this.getAttribute('data-url');
            var status = this.value;
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.style.border = '2px solid green';
                    setTimeout(() => this.style.border = '', 1000);
                } else {
                    alert('Gagal update status');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endsection