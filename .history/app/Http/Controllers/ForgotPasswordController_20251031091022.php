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
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan');
        }

        // Buat kode acak 6 digit
        $code = rand(100000, 999999);

        // Hapus kode lama jika ada
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        // Simpan kode ke tabel password_reset_codes dengan hash
        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => Hash::make($code),
            'created_at' => now()
        ]);

        // Kirim email dengan kode plain (tidak di-hash)
        Mail::to($user->email)->send(new ResetPasswordMail($code));

        // Simpan email ke session untuk proses selanjutnya
        session(['email' => $request->email]);

        return redirect()->route('verify.code.form')
            ->with('success', 'Kode reset password telah dikirim ke email Anda');
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
            'code' => 'required|digits:6'
        ], [
            'code.required' => 'Kode verifikasi harus diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit'
        ]);

        // Ambil email dari session
        $email = session('email');

        if (!$email) {
            return redirect()->route('forgot.password')
                ->with('error', 'Sesi telah berakhir. Silakan ulangi proses dari awal.');
        }

        // Ambil data kode dari database
        $resetData = DB::table('password_reset_codes')
            ->where('email', $email)
            ->first();

        // Cek apakah kode ditemukan
        if (!$resetData) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan');
        }

        // Cek apakah kode sudah expired (lewat 15 menit)
        $createdAt = Carbon::parse($resetData->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            // Hapus kode yang sudah expired
            DB::table('password_reset_codes')->where('email', $email)->delete();
            return back()->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan kirim ulang.');
        }

        // Verifikasi kode dengan hash
        if (Hash::check($request->code, $resetData->code)) {
            // Kode benar, simpan status verified ke session
            session(['verified' => true]);

            // Redirect ke halaman reset password
            return redirect()->route('reset.password.form')
                ->with('success', 'Kode verifikasi berhasil! Silakan buat password baru.');
        }

        // Kode salah
        return back()->with('error', 'Kode verifikasi salah')->withInput();
    }

    /**
     * Menampilkan halaman form reset password
     */
    public function showResetPasswordForm(Request $request)
    {
        // Cek apakah sudah terverifikasi
        if (!session('verified') || !session('email')) {
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
        // Cek apakah sudah terverifikasi
        if (!session('verified') || !session('email')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Sesi telah berakhir. Silakan ulangi proses dari awal.');
        }

        // Validasi input password
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Ambil email dari session
        $email = session('email');

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        // Update password user dengan hash
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus kode verifikasi dari database (sudah tidak diperlukan)
        DB::table('password_reset_codes')
            ->where('email', $email)
            ->delete();

        // Hapus session
        session()->forget(['email', 'verified']);

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Kirim ulang kode verifikasi
     */
    public function resendCode(Request $request)
    {
        // Ambil email dari session
        $email = session('email');

        if (!$email) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan masukkan email terlebih dahulu');
        }

        // Buat request baru dengan email dari session
        $request->merge(['email' => $email]);

        // Panggil fungsi sendResetCode untuk kirim ulang
        return $this->sendResetCode($request);
    }
}
