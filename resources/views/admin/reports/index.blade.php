@extends('admin.layout')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Laporan Penjualan</h6>
    </div>
    <div class="card-body">
        <!-- Form Filter -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label>Tipe Laporan</label>
                <select name="type" id="reportType" class="form-select" required onchange="toggleFields()">
                    <option value="">-- Pilih --</option>
                    <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>

            <!-- Input Harian -->
            <div class="col-md-3 {{ request('type') != 'daily' ? 'd-none' : '' }}" id="dailyField">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>

            <!-- Input Mingguan -->
            <div class="col-md-3 {{ request('type') != 'weekly' ? 'd-none' : '' }}" id="weeklyField">
                <label>Mulai</label>
                <input type="date" name="week_start" class="form-control mb-2" value="{{ request('week_start') }}">
                <label>Sampai</label>
                <input type="date" name="week_end" class="form-control" value="{{ request('week_end') }}">
            </div>

            <!-- Input Bulanan -->
            <div class="col-md-3 {{ request('type') != 'monthly' ? 'd-none' : '' }}" id="monthlyField">
                <label>Bulan</label>
                <input type="month" name="month" class="form-control" value="{{ request('month') }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Tampilkan
                </button>
            </div>
        </form>

        <hr class="my-4">

        <!-- Area Hasil Data -->
        @isset($data)
            @if($data->count() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0">{{ $title }}</h5>
                        <small class="text-muted">{{ $dateLabel }}</small>
                    </div>
                    <!-- Tombol Download PDF -->
                    <a href="{{ route('admin.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Customer</th>
                                <th>Area</th>
                                <th>Total Tagihan</th>
                                <th>DP Dibayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($data as $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ date('d/m/Y', strtotime($row->reservation_date)) }}</td>
                                <td>{{ $row->customer_name }}</td>
                                <td>{{ $row->area->name ?? '-' }}</td>
                                <td>Rp {{ number_format($row->total_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($row->dp_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $row->status == 'confirmed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                                <td>Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($totalDp, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-warning text-center">Tidak ada data ditemukan untuk filter ini.</div>
            @endif
        @endif
    </div>
</div>

<script>
    function toggleFields() {
        var type = document.getElementById('reportType').value;
        
        // Hide all
        document.getElementById('dailyField').classList.add('d-none');
        document.getElementById('weeklyField').classList.add('d-none');
        document.getElementById('monthlyField').classList.add('d-none');

        // Show selected
        if (type === 'daily') {
            document.getElementById('dailyField').classList.remove('d-none');
        } else if (type === 'weekly') {
            document.getElementById('weeklyField').classList.remove('d-none');
        } else if (type === 'monthly') {
            document.getElementById('monthlyField').classList.remove('d-none');
        }
    }
    
    // Run on load just in case
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
    });
</script>
@endsection