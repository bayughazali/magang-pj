<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User; // Sesuaikan dengan model Anda
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Display the export page
     */
    public function index()
    {
        // Ambil statistik data untuk ditampilkan
        $totalUsers = User::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        
        return view('exports.index', compact('totalUsers', 'recentUsers'));
    }

    /**
     * Export users data to CSV
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'role' => 'nullable|string',
        ]);

        $fileName = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($request) {
            // Query builder dengan filter
            $query = User::query();
            
            // Filter berdasarkan tanggal
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Filter berdasarkan role
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            // Buka output stream
            $handle = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV
            fputcsv($handle, [
                'No',
                'Nama',
                'Email',
                'Role', 
                'Tanggal Dibuat',
                'Status'
            ]);

            // Export data dalam chunk untuk menghindari memory issues
            $no = 1;
            $query->chunk(1000, function ($users) use ($handle, &$no) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $no++,
                        $user->name,
                        $user->email,
                        $user->role ?? 'USER',
                        $user->created_at->format('d/m/Y H:i:s'),
                        $user->email_verified_at ? 'Aktif' : 'Tidak Aktif'
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export users data to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'role' => 'nullable|string',
        ]);

        // Query builder dengan filter
        $query = User::query();
        
        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        
        // Data untuk PDF
        $data = [
            'title' => 'PLN AKPOL - Data Export Users',
            'date' => now()->format('d/m/Y H:i:s'),
            'users' => $users,
            'total' => $users->count(),
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'role' => $request->role,
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('exports.pdf', $data)
                 ->setPaper('a4', 'landscape')
                 ->setOptions(['defaultFont' => 'sans-serif']);

        $fileName = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Preview data before export
     */
    public function preview(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'role' => 'nullable|string',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        // Query builder dengan filter
        $query = User::query();
        
        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Hitung total yang akan diexport
        $totalExport = $query->count();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                    ],
                    'total_export' => $totalExport,
                ]
            ]);
        }

        return view('exports.preview', compact('users', 'totalExport'));
    }

    /**
     * Get available roles for filter
     */
    public function getRoles()
    {
        $roles = User::select('role')
                    ->whereNotNull('role')
                    ->distinct()
                    ->pluck('role')
                    ->filter()
                    ->values();

        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }
}