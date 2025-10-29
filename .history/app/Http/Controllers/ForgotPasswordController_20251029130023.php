<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\ResetPasswordMail;
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
     * Mengirim kode verifikasi ke email user
     */
    public function sendResetCode(Request $request)
    {
        // Validasi input email
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        // Generate kode verifikasi 6 digit random
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan kode ke database dengan hash untuk keamanan
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email berisi kode verifikasi
        try {
            Mail::to($request->email)->send(new ResetPasswordMail($code));

            return redirect()->route('verify.code.form')
                ->with('success', 'Kode verifikasi telah dikirim ke email Anda')
                ->with('email', $request->email);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengirim email. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Menampilkan halaman form verifikasi kode
     */
    public function showVerifyCodeForm()
    {
        // Pastikan user sudah input email terlebih dahulu
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
        // Validasi input kode
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6'
        ], [
            'code.required' => 'Kode verifikasi harus diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit'
        ]);

        // Ambil data kode dari database
        $resetData = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->first();

        // Cek apakah kode ditemukan
        if (!$resetData) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan');
        }

        // Cek apakah kode sudah expired (lewat 15 menit)
        $createdAt = Carbon::parse($resetData->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            // Hapus kode yang sudah expired
            DB::table('password_reset_codes')->where('email', $request->email)->delete();
            return back()->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan kirim ulang.');
        }

        // Verifikasi kode dengan hash
        if (Hash::check($request->code, $resetData->code)) {
            // Kode benar, redirect ke halaman reset password
            return redirect()->route('reset.password.form')
                ->with('email', $request->email)
                ->with('verified', true);
        }

        // Kode salah
        return back()->with('error', 'Kode verifikasi salah')->withInput();
    }

    /**
     * Menampilkan halaman form reset password
     */
    public function showResetPasswordForm()
    {
        // Pastikan user sudah verifikasi kode terlebih dahulu
        if (!session('verified')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan verifikasi kode terlebih dahulu');
        }

        return view('auth.reset-password');
    }

    /**
     * Proses reset password - simpan password baru
     */
    public function resetPassword(Request $request)
    {
        // Validasi input password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        // Update password user dengan hash
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus kode verifikasi dari database (sudah tidak diperlukan)
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Kirim ulang kode verifikasi
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Panggil fungsi sendResetCode untuk kirim ulang
        return $this->sendResetCode($request);
    }
}
