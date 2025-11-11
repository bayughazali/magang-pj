<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;

class PasswordResetRequestController extends Controller
{
    /**
     * Menampilkan daftar request reset password
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = PasswordResetRequest::with('user')
            ->when($status === 'pending', function ($query) {
                $query->where('status', 'pending');
            })
            ->when($status === 'used', function ($query) {
                $query->where('status', 'used');
            })
            ->when($status === 'expired', function ($query) {
                $query->where('status', 'expired')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')
                            ->where('expires_at', '<=', now());
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.password-resets.index', compact('requests', 'status'));
    }


    /**
     * Generate kode baru untuk request tertentu
     */
    public function regenerateCode($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'Tidak dapat generate ulang kode untuk request yang sudah ' . $resetRequest->status);
        }

        // Generate kode baru
        do {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (PasswordResetRequest::where('code', $code)->where('status', 'pending')->exists());

        $resetRequest->update([
            'code' => $code,
            'expires_at' => now()->addHours(24)
        ]);

        return back()->with('success', 'Kode baru berhasil digenerate: ' . $code);
    }

    /**
     * Batalkan request
     */
    public function cancel($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'Request sudah ' . $resetRequest->status);
        }

        $resetRequest->update(['status' => 'expired']);

        return back()->with('success', 'Request berhasil dibatalkan');
    }

    /**
     * Hapus request
     */
    public function destroy($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);
        $resetRequest->delete();

        return back()->with('success', 'Request berhasil dihapus');
    }

    /**
     * Update status expired otomatis untuk semua request yang lewat waktu
     */
   public function updateExpiredStatus()
{
    $updated = PasswordResetRequest::where('status', 'pending')
        ->where('expires_at', '<=', now())
        ->update(['status' => 'expired']);

    return back()->with('success', "{$updated} request telah diupdate menjadi expired");
}

}
