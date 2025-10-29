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
    // Menampilkan form lupa password
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Mengirim kode verifikasi ke email
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        // Generate 6 digit kode verifikasi
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan kode ke database dengan masa berlaku 15 menit
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email dengan kode verifikasi
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

    // Menampilkan form verifikasi kode
    public function showVerifyCodeForm()
    {
        if (!session('email')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan masukkan email terlebih dahulu');
        }

        return view('auth.verify-code');
    }

    // Verifikasi kode yang dimasukkan user
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6'
        ], [
            'code.required' => 'Kode verifikasi harus diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit'
        ]);

        // Cek kode di database
        $resetData = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->first();

        if (!$resetData) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan');
        }

        // Cek apakah kode sudah expired (15 menit)
        $createdAt = Carbon::parse($resetData->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            DB::table('password_reset_codes')->where('email', $request->email)->delete();
            return back()->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan kirim ulang.');
        }

        // Verifikasi kode
        if (Hash::check($request->code, $resetData->code)) {
            return redirect()->route('reset.password.form')
                ->with('email', $request->email)
                ->with('verified', true);
        }

        return back()->with('error', 'Kode verifikasi salah')->withInput();
    }

    // Menampilkan form reset password
    public function showResetPasswordForm()
    {
        if (!session('verified')) {
            return redirect()->route('forgot.password')
                ->with('error', 'Silakan verifikasi kode terlebih dahulu');
        }

        return view('auth.reset-password');
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Update password user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus kode verifikasi dari database
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    // Kirim ulang kode verifikasi
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        return $this->sendResetCode($request);
    }
}
