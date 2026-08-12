<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Menampilkan halaman laporan + data di browser
    public function index(Request $request)
    {
        $data = null;
        $totalSales = 0;
        $totalDp = 0;
        $title = '';
        $dateLabel = '';

        // Cek apakah user sudah memilih filter
        if ($request->has('type') && $request->type != '') {
            $query = Reservation::with('area')->whereIn('status', ['confirmed', 'pending']); // Tampilkan yang sudah ada

            switch ($request->type) {
                case 'daily':
                    $date = $request->date;
                    if ($date) {
                        $query->whereDate('reservation_date', $date);
                        $title = 'Laporan Harian';
                        $dateLabel = date('d F Y', strtotime($date));
                    }
                    break;

                case 'weekly':
                    $start = $request->week_start;
                    $end = $request->week_end;
                    if ($start && $end) {
                        $query->whereBetween('reservation_date', [$start, $end]);
                        $title = 'Laporan Mingguan';
                        $dateLabel = date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end));
                    }
                    break;

                case 'monthly':
                    $month = $request->month; // Format: 2024-01
                    if ($month) {
                        $query->whereMonth('reservation_date', date('m', strtotime($month)))
                            ->whereYear('reservation_date', date('Y', strtotime($month)));
                        $title = 'Laporan Bulanan';
                        $dateLabel = date('F Y', strtotime($month));
                    }
                    break;
            }

            $data = $query->orderBy('reservation_date', 'ASC')->get();
            $totalSales = $data->sum('total_price');
            $totalDp = $data->sum('dp_amount');
        }

        return view('admin.reports.index', compact('data', 'title', 'dateLabel', 'totalSales', 'totalDp', 'request'));
    }

    // Buat export kr pdff 
    public function exportPdf(Request $request)
    {
        // buat ambil parameter dari URL (get)
        $type = $request->get('type');
        $date = $request->get('date');
        $week_start = $request->get('week_start');
        $week_end = $request->get('week_end');
        $month = $request->get('month');

        $query = Reservation::with('area')->whereIn('status', ['confirmed', 'pending']);

        $title = '';
        $dateLabel = '';

        switch ($type) {
            case 'daily':
                $query->whereDate('reservation_date', $date);
                $title = 'Laporan Penjualan Harian';
                $dateLabel = date('d F Y', strtotime($date));
                break;
            case 'weekly':
                $query->whereBetween('reservation_date', [$week_start, $week_end]);
                $title = 'Laporan Penjualan Mingguan';
                $dateLabel = date('d M Y', strtotime($week_start)) . ' - ' . date('d M Y', strtotime($week_end));
                break;
            case 'monthly':
                $query->whereMonth('reservation_date', date('m', strtotime($month)))
                    ->whereYear('reservation_date', date('Y', strtotime($month)));
                $title = 'Laporan Penjualan Bulanan';
                $dateLabel = date('F Y', strtotime($month));
                break;
        }

        $data = $query->orderBy('reservation_date', 'ASC')->get();
        $totalSales = $data->sum('total_price');
        $totalDp = $data->sum('dp_amount');

        $pdf = Pdf::loadView('admin.reports.pdf', compact('data', 'title', 'dateLabel', 'totalSales', 'totalDp'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-penjualan.pdf');
    }
}
