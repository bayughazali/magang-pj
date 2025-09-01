<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use Illuminate\Http\Request;
use App\Exports\CompetitorExport;
use Maatwebsite\Excel\Facades\Excel;

class CompetitorController extends Controller
{
    // READ (Index)
    public function index(Request $request)
    {
        $query = Competitor::query();

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        // ✅ gunakan pagination agar punya firstItem(), currentPage(), dll
        $competitors = $query->latest()->paginate(10);

        return view('report.competitor', compact('competitors'));
    }

    // CREATE (Store)
    public function store(Request $request)
    {
        $request->validate([
            'cluster'          => 'required|string',
            'competitor_name'  => 'required|array',
            'paket'            => 'nullable|array',
            'kecepatan'        => 'nullable|array',
            'kuota'            => 'nullable|array',
            'harga'            => 'required|array',
            'fitur_tambahan'   => 'nullable|array',
            'keterangan'       => 'nullable|array',
        ]);

        foreach ($request->competitor_name as $key => $name) {
            Competitor::create([
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

        return redirect()->route('competitor.index')->with('success', 'Data competitor berhasil ditambahkan');
    }

    // EDIT (Form Edit)
    public function edit($id)
    {
        $competitor = Competitor::findOrFail($id);
        return view('competitor_edit', compact('competitor'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'cluster'         => 'required|string',
            'competitor_name' => 'required|string',
            'paket'           => 'nullable|string',
            'kecepatan'       => 'nullable|string',
            'kuota'           => 'nullable|string',
            'harga'           => 'required|numeric',
            'fitur_tambahan'  => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);

        $competitor = Competitor::findOrFail($id);
        $competitor->update($request->all());

        return redirect()->route('competitor.index')->with('success', 'Data competitor berhasil diperbarui');
    }

    // DELETE
    public function destroy(Request $request, $id)
    {
        $competitor = Competitor::findOrFail($id);
        $competitor->delete();

        // ✅ Balas JSON kalau request-nya AJAX/fetch
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data competitor berhasil dihapus',
            ]);
        }

        return redirect()->route('competitor.index')->with('success', 'Data competitor berhasil dihapus');
    }
    
}
