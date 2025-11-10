<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\ReportActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf; // Import ini

class ReportActivityController extends Controller
{

     public function create()
    {
        $competitors = collect(['A','B','C','D','E','F','G','H','I','J'])
            ->map(fn($cluster) => (object)['cluster' => $cluster]);
        return view('report.activity', compact('competitors'));
    }

    public function index()
    {
        $reports = ReportActivity::latest()->get();

        $competitors = collect(['A','B','C','D','E','F','G','H','I','J'])
            ->map(fn($cluster) => (object)['cluster' => $cluster]);

        return view('report.activity', compact('reports','competitors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'cluster' => 'required|string|in:A,B,C,D,E,F,G,H,I,J',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        // Handle file upload dengan error handling yang lebih baik
        if ($request->hasFile('evidence')) {
            try {
                $file = $request->file('evidence');

                // Pastikan file valid
                if ($file->isValid()) {
                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Store file
                    $path = $file->storeAs('evidence', $filename, 'public');

                    // Verify file was stored
                    if (Storage::disk('public')->exists($path)) {
                        $validated['evidence'] = $path;
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan file evidence')->withInput();
                    }
                } else {
                    return redirect()->back()->with('error', 'File evidence tidak valid')->withInput();
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error saat upload: ' . $e->getMessage())->withInput();
            }
        }

        try {
            ReportActivity::create($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'cluster' => 'required|string|in:A,B,C,D,E,F,G,H,I,J',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        $report = ReportActivity::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('evidence')) {
            try {
                $file = $request->file('evidence');

                if ($file->isValid()) {
                    // Delete old file if exists
                    if ($report->evidence && Storage::disk('public')->exists($report->evidence)) {
                        Storage::disk('public')->delete($report->evidence);
                    }

                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Store new file
                    $path = $file->storeAs('evidence', $filename, 'public');

                    if (Storage::disk('public')->exists($path)) {
                        $validated['evidence'] = $path;
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan file evidence baru');
                    }
                } else {
                    return redirect()->back()->with('error', 'File evidence tidak valid');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error saat upload: ' . $e->getMessage());
            }
        }

        try {
            $report->update($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $report = ReportActivity::findOrFail($id);

            // Delete file if exists
            if ($report->evidence) {
                Storage::disk('public')->delete($report->evidence);
            }

            $report->delete();
            return redirect()->route('reports.activity')->with('success', 'Report berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            // Ambil semua data reports
            $reports = ReportActivity::orderBy('tanggal', 'desc')->get();

            // Data tambahan
            $data = [
                'reports' => $reports,
                'title'   => 'Laporan Aktivitas Sales',
                'date'    => date('d F Y')
            ];

            // Generate PDF dengan opsi remote enabled (agar gambar muncul)
           $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
              ->loadView('report.activity-pdf', compact('reports'));

            // Download PDF
            return $pdf->download('laporan-aktivitas-sales-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    public function debugStorage()
    {
        $reports = ReportActivity::whereNotNull('evidence')->get();
        $debug = [];

        foreach ($reports as $report) {
            $debug[] = [
                'id' => $report->id,
                'evidence_path' => $report->evidence,
                'file_exists_public' => Storage::disk('public')->exists($report->evidence),
                'file_exists_storage' => file_exists(storage_path('app/public/' . $report->evidence)),
                'full_path' => storage_path('app/public/' . $report->evidence),
                'public_url' => asset('storage/' . $report->evidence),
                'storage_link_exists' => is_link(public_path('storage'))
            ];
        }

        $info = [
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage'),
            'storage_link_exists' => is_link(public_path('storage')),
            'storage_link_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : 'Not a symlink',
            'reports_with_evidence' => $reports->count(),
            'debug_data' => $debug
        ];

        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
    }

    // Method untuk fix storage link
    public function fixStorage()
    {
        try {
            // Hapus storage link lama jika ada
            if (is_link(public_path('storage'))) {
                unlink(public_path('storage'));
            }

            // Buat ulang storage link
            Artisan::call('storage:link');

            return redirect()->back()->with('success', 'Storage link berhasil diperbaiki!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbaiki storage link: ' . $e->getMessage());
        }
    }

    // Method untuk clean up file yang tidak ada
    public function cleanupMissingFiles()
    {
        $reports = ReportActivity::whereNotNull('evidence')->get();
        $cleaned = 0;

        foreach ($reports as $report) {
            if (!Storage::disk('public')->exists($report->evidence)) {
                $report->update(['evidence' => null]);
                $cleaned++;
            }
        }

        return redirect()->back()->with('success', "Berhasil membersihkan {$cleaned} file yang hilang dari database.");
    }
}