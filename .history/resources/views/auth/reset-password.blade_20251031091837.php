@extends('layouts.app')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 480px;
        width: 100%;
        padding: 40px;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .icon-wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .icon-wrapper svg {
        width: 40px;
        height: 40px;
        stroke: white;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    h1 {
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 12px;
    }

    .subtitle {
        color: #718096;
        text-align: center;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 8px;
    }

    .email-info {
        background: #edf2f7;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        margin-bottom: 20px;
    }

    .email-info span {
        color: #667eea;
        font-weight: 600;
        font-size: 15px;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: #f0fdf4;
        border-left: 4px solid #22c55e;
        color: #166534;
    }

    .alert-error {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }

    .info-box {
        background: #fff5f5;
        border-left: 4px solid #fc8181;
        padding: 16px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .info-box-warning {
        background: #fffaf0;
        border-left-color: #f6ad55;
    }

    .info-box p {
        color: #742a2a;
        font-size: 14px;
        line-height: 1.6;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .info-box-warning p {
        color: #7c2d12;
    }

    .info-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .timer {
        background: #edf2f7;
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .timer svg {
        width: 20px;
        height: 20px;
        stroke: #667eea;
    }

    .timer span {
        color: #4a5568;
        font-size: 14px;
        font-weight: 600;
    }

    .timer .time {
        color: #667eea;
        font-weight: 700;
    }

    .actions {
        margin-top: 32px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .btn {
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-secondary:hover {
        background: #f7fafc;
        transform: translateY(-2px);
    }

    .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .back-link a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .container {
            padding: 30px 20px;
        }

        h1 {
            font-size: 24px;
        }
    }
</style>

<div class="container">
    <div class="icon-wrapper">
        <svg viewBox="0 0 24 24">
            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
        </svg>
    </div>

    <h1>Kode Reset Password</h1>
    <p class="subtitle">Kode verifikasi telah dikirim ke email:</p>

    <div class="email-info">
        <span>{{ session('email') }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('verify.code') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">

        <div class="info-box info-box-warning">
            <p>
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Masukkan kode verifikasi 6 digit yang telah dikirim ke email Anda untuk melanjutkan proses reset password.
            </p>
        </div>

        <div class="timer">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Kode berlaku selama <span class="time">15 menit</span></span>
        </div>

        <div class="info-box">
            <p>
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Jangan bagikan kode ini kepada siapa pun untuk keamanan akun Anda.
            </p>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">
                ✓ Verifikasi Kode
            </button>
        </div>
    </form>

    <form action="{{ route('resend.code') }}" method="POST">
        @csrf
        <div class="actions" style="margin-top: 12px;">
            <button type="submit" class="btn btn-secondary">
                🔄 Tidak menerima kode? Kirim Ulang
            </button>
        </div>
    </form>

    <div class="back-link">
        <a href="{{ route('login') }}">← Kembali ke Login</a>
    </div>
</div>
@endsection
