<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display statistics for the dashboard.
     */
    public function index()
    {
        // Total laporan yang masuk
        $totalReports = Report::count();

        // Jumlah penerima bantuan per program
        $recipientsPerProgram = Report::with('program')
            ->selectRaw('program_id, SUM(jumlah_penerima) as total_recipients')
            ->groupBy('program_id')
            ->get()
            ->map(function ($report) {
                return [
                    'program_name' => $report->program->name,
                    'total_recipients' => $report->total_recipients,
                ];
            });

        // Grafik penyaluran bantuan per wilayah (provinsi dan kabupaten)
        $distributionPerRegion = Report::select('provinsi', 'kabupaten')
            ->selectRaw('SUM(jumlah_penerima) as total_recipients')
            ->groupBy('provinsi', 'kabupaten')
            ->get();

        // Data untuk response
        $data = [
            'total_reports' => $totalReports,
            'recipients_per_program' => $recipientsPerProgram,
            'distribution_per_region' => $distributionPerRegion,
        ];

        return response()->json($data, 200);
    }
}
