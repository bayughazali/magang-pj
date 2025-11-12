<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use Illuminate\Http\Request;
use App\Exports\CompetitorExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class CompetitorController extends Controller
{
    // READ (Index)
    public function index(Request $request)
    {
        $query = Competitor::query();

        // Filter by cluster if provided
        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        // Filter by sales (untuk admin bisa lihat semua, user biasa hanya datanya sendiri)
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        // Pagination dengan join ke tabel users untuk menampilkan nama sales
        $competitors = $query->with('user')->latest()->paginate(10);

        return view('report.competitor', compact('competitors'));
    }

    // CREATE (Store)
    public function store(Request $request)
    {
        $request->validate([
            'cluster'          => 'required|string',
            'competitor_name'  => 'required|array',
            'competitor_name.*'=> 'required|string',
            'paket'            => 'nullable|array',
            'kecepatan'        => 'nullable|array',
            'kuota'            => 'nullable|array',
            'harga'            => 'required|array',
            'harga.*'          => 'required|numeric',
            'fitur_tambahan'   => 'nullable|array',
            'keterangan'       => 'nullable|array',
        ]);

        // PAKSA sales_name dan user_id dari user yang login (KEAMANAN)
        $salesName = Auth::user()->name;
        $userId = Auth::id();

        foreach ($request->competitor_name as $key => $name) {
            Competitor::create([
                'sales_name'      => $salesName,        // ✅ Otomatis dari user login
                'user_id'         => $userId,           // ✅ ID user yang login
                'cluster'         => $request->cluster,
                'competitor_name' => $name,
                'paket'           => $request->paket[$key] ?? null,
                'kecepatan'       => $request->kecepatan[$key] ?? null,
                'kuota'           => $request->kuota[$key] ?? null,
                'harga'           => $request->harga[$key] ?? 0,
                'fitur_tambahan'  => $request->fitur_tambahan[$key] ?? null,
                'keterangan'      => $request->keterangan[$key] ?? null,
            ]);
        }

        return redirect()->route('competitor.index')
            ->with('success', '✅ Data competitor berhasil ditambahkan!');
    }

    // EDIT (Form Edit)
    public function edit($id)
    {
        $competitor = Competitor::findOrFail($id);
        
        // ✅ Authorization: Cek apakah user berhak edit data ini
        if (Auth::user()->role !== 'admin' && $competitor->user_id !== Auth::id()) {
            return redirect()->route('competitor.index')
                ->with('error', '❌ Anda tidak memiliki akses untuk mengedit data ini!');
        }

        return view('competitor_edit', compact('competitor'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $competitor = Competitor::findOrFail($id);
        
        // ✅ Authorization: Cek apakah user berhak update data ini
        if (Auth::user()->role !== 'admin' && $competitor->user_id !== Auth::id()) {
            return redirect()->route('competitor.index')
                ->with('error', '❌ Anda tidak memiliki akses untuk mengupdate data ini!');
        }

        $validated = $request->validate([
            'cluster'         => 'required|string',
            'competitor_name' => 'required|string',
            'paket'           => 'nullable|string',
            'kecepatan'       => 'nullable|string',
            'kuota'           => 'nullable|string',
            'harga'           => 'required|numeric',
            'fitur_tambahan'  => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);

        // ✅ HANYA ADMIN yang bisa ubah sales_name
        if (Auth::user()->role === 'admin' && $request->has('sales_name')) {
            $validated['sales_name'] = $request->sales_name;
        } else {
            // User biasa tidak bisa ubah sales_name (tetap gunakan yang lama)
            $validated['sales_name'] = $competitor->sales_name;
            $validated['user_id'] = $competitor->user_id;
        }

        $competitor->update($validated);

        return redirect()->route('competitor.index')
            ->with('success', '✅ Data competitor berhasil diperbarui!');
    }

    // DELETE
    public function destroy(Request $request, $id)
    {
        $competitor = Competitor::findOrFail($id);
        
        // ✅ Authorization: Hanya admin atau pemilik data yang bisa hapus
        if (Auth::user()->role !== 'admin' && $competitor->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Anda tidak memiliki akses untuk menghapus data ini!',
                ], 403);
            }
            
            return redirect()->route('competitor.index')
                ->with('error', '❌ Anda tidak memiliki akses untuk menghapus data ini!');
        }

        $competitor->delete();

        // Response untuk AJAX/fetch
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Data competitor berhasil dihapus!',
            ]);
        }

        return redirect()->route('competitor.index')
            ->with('success', '✅ Data competitor berhasil dihapus!');
    }

    // EXPORT (Optional - jika sudah ada fitur export)
    public function export(Request $request)
    {
        $cluster = $request->get('cluster');
        
        // Filter berdasarkan role
        if (Auth::user()->role !== 'admin') {
            return Excel::download(
                new CompetitorExport($cluster, Auth::id()), 
                'competitor-data-' . date('Y-m-d') . '.xlsx'
            );
        }

        return Excel::download(
            new CompetitorExport($cluster), 
            'competitor-data-' . date('Y-m-d') . '.xlsx'
        );
    }
}