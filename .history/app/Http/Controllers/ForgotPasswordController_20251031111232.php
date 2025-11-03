<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman form lupa password
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Membuat request reset password (simpan ke database untuk admin)
     */
    public function createResetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate kode 6 digit unik
        do {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (PasswordResetRequest::where('code', $code)->where('status', 'pending')->exists());

        // Batalkan request lama yang masih pending untuk email ini
        PasswordResetRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Buat request baru
        $resetRequest = PasswordResetRequest::create([
            'email' => $request->email,
            'code' => $code,
            'status' => 'pending',
            'expires_at' => now()->addHours(24) // Berlaku 24 jam
        ]);

        // Simpan email ke session untuk proses selanjutnya
        session(['email' => $request->email]);

        return redirect()->route('verify.code.form')
            ->with('success', 'Permintaan reset password telah dibuat. Silakan hubungi admin untuk mendapatkan kode verifikasi.');
    }

    /**
     * Menampilkan halaman form verifikasi kode
     */
    public function showVerifyCodeForm()
    {
        if (!session('email')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan masukkan email terlebih dahulu');
        }

        return view('auth.verify-code');
    }

    /**
     * Memverifikasi kode yang diinput oleh user
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ], [
            'code.required' => 'Kode verifikasi harus diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit'
        ]);

        $email = session('email');

        if (!$email) {
            return redirect()->route('forgot.password')
                ->with('error', 'Sesi telah berakhir. Silakan ulangi proses dari awal.');
        }

        // Cari request reset password yang valid
        $resetRequest = PasswordResetRequest::where('email', $email)
            ->where('code', $request->code)
            ->where('status', 'pending')
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Kode verifikasi tidak valid atau sudah digunakan')->withInput();
        }

        // Cek apakah sudah expired
        if ($resetRequest->isExpired()) {
            $resetRequest->update(['status' => 'expired']);
            return back()->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan buat permintaan baru.');
        }

        // Kode valid, simpan status verified ke session
        session(['verified' => true, 'reset_request_id' => $resetRequest->id]);

        return redirect()->route('reset.password.form')
            ->with('success', 'Kode verifikasi berhasil! Silakan buat password baru.');
    }

    /**
     * Menampilkan halaman form reset password
     */
    public function showResetPasswordForm()
    {
        if (!session('verified') || !session('email')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan verifikasi kode terlebih dahulu');
        }

        return view('auth.forgot-password');
    }

    /**
     * Proses reset password - simpan password baru
     */
//   }
