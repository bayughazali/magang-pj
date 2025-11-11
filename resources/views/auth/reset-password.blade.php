@extends('layouts.auth')

@section('content')
<style>
  :root {
    --blue-dark: #1e3a5f;
    --blue-mid: #2d5f8d;
    --blue-light: #5da5c8;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body, html {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    overflow: hidden;
  }

  .reset-password-container {
    height: 100vh;
    width: 100vw;
    display: flex;
    position: relative;
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-light) 100%);
  }

  /* Bagian Kiri - Logo dengan bentuk melengkung */
  .logo-section {
    flex: 0 0 50%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
  }

  .logo-section::before {
    content: '';
    position: absolute;
    right: -100px;
    top: 0;
    bottom: 0;
    width: 120%;
    background: white;
    border-radius: 0 50% 50% 0;
    z-index: 1;
  }

  .logo-content {
    position: relative;
    z-index: 2;
    text-align: center;
    margin-right: 100px;
  }

  .logo-content img.iconnet-logo {
    max-width: 400px;
    width: 100%;
    height: auto;
    margin-bottom: 20px;
  }

  .powered-by {
    margin-top: 25px;
    color: #2d5f8d;
    font-weight: 500;
    font-size: 16px;
  }

  .powered-by img {
    max-width: 140px;
    margin-top: 8px;
  }

  /* Bagian Kanan - Form Reset Password */
  .reset-password-form-section {
    flex: 0 0 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: white;
  }

  .reset-password-form-wrapper {
    max-width: 480px;
    width: 100%;
  }

  .icon-wrapper {
    background: rgba(255, 255, 255, 0.2);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 0 24px 0;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
  }

  .icon-wrapper svg {
    width: 40px;
    height: 40px;
    stroke: white;
    fill: none;
    stroke-width: 2;
  }

  .reset-password-form-wrapper h2 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
  }

  .reset-password-form-wrapper > p {
    font-size: 16px;
    margin-bottom: 30px;
    line-height: 1.5;
    font-weight: 400;
    color: white;
  }

  .alert-box {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.3s ease;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .alert-error {
    background: rgba(239, 68, 68, 0.2);
    color: #fff;
    border: 1px solid rgba(239, 68, 68, 0.4);
  }

  .alert-success {
    background: rgba(34, 197, 94, 0.2);
    color: #fff;
    border: 1px solid rgba(34, 197, 94, 0.4);
  }

  .alert-box svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
  }

  .form-group {
    margin-bottom: 24px;
  }

  .form-group label {
    display: block;
    font-size: 15px;
    margin-bottom: 10px;
    font-weight: 500;
    color: white;
  }

  .form-group input[type="password"] {
    width: 100%;
    padding: 14px 18px;
    border-radius: 10px;
    border: none;
    font-size: 15px;
    background: white;
    color: #333;
    outline: none;
    transition: all 0.3s ease;
  }

  .form-group input::placeholder {
    color: #999;
  }

  .form-group input:focus {
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
  }

  .password-requirements {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 18px 20px;
    margin-top: 24px;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .password-requirements h4 {
    color: white;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 14px;
  }

  .password-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .password-requirements li {
    color: white;
    font-size: 13px;
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.5;
  }

  .password-requirements li::before {
    content: "✓";
    color: #4ade80;
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0;
  }

  .btn-submit {
    width: 100%;
    padding: 16px;
    background: white;
    border: none;
    border-radius: 10px;
    color: #1e3a5f;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 28px;
  }

  .btn-submit:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  .btn-submit:active {
    transform: translateY(0);
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .logo-section::before {
      right: -80px;
      width: 110%;
    }

    .logo-content {
      margin-right: 60px;
    }

    .logo-content img.iconnet-logo {
      max-width: 320px;
    }

    .reset-password-form-wrapper h2 {
      font-size: 40px;
    }
  }

  @media (max-width: 768px) {
    .reset-password-container {
      flex-direction: column;
      overflow-y: auto;
    }

    .logo-section {
      flex: 0 0 30%;
      min-height: 30vh;
      padding: 30px;
    }

    .logo-section::before {
      right: 0;
      bottom: -80px;
      width: 100%;
      height: 110%;
      border-radius: 0 0 50% 50%;
    }

    .logo-content {
      margin-right: 0;
      margin-bottom: 60px;
    }

    .logo-content img.iconnet-logo {
      max-width: 240px;
    }

    .reset-password-form-section {
      flex: 1;
      padding: 40px 30px;
    }

    .reset-password-form-wrapper h2 {
      font-size: 36px;
    }

    .icon-wrapper {
      width: 70px;
      height: 70px;
    }

    .icon-wrapper svg {
      width: 35px;
      height: 35px;
    }
  }
</style>

<div class="reset-password-container">
  <div class="logo-section">
    <div class="logo-content">
      <img src="{{ asset('argonpro/assets/img/brand/iconnet2.png') }}" alt="PLN Icon Plus" class="pln-logo">
      <div class="powered-by">
        <br />
        <!-- <img src="{{ asset('assets/img/logo/plniconplus.png') }}" alt="PLN Icon Plus" /> -->
      </div>
    </div>
  </div>

  <!-- Bagian Kanan: Form Reset Password -->
  <div class="reset-password-form-section">
    <div class="reset-password-form-wrapper">
      <div class="icon-wrapper">
        <svg viewBox="0 0 24 24">
          <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
      </div>

      <h2>Buat Password Baru</h2>
      <p>Masukkan password baru untuk akun Anda</p>

      @if(session('success'))
        <div class="alert-box alert-success">
          <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="alert-box alert-error">
          <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <form action="{{ route('reset.password') }}" method="POST">
        @csrf
        
        <div class="form-group">
          <label for="password">Password Baru</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password baru"
            required
            autofocus
          >
          @error('password')
            <div class="alert-box alert-error" style="margin-top: 12px;">
              <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation">Konfirmasi Password</label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Masukkan ulang password"
            required
          >
          @error('password_confirmation')
            <div class="alert-box alert-error" style="margin-top: 12px;">
              <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        <div class="password-requirements">
          <h4>Password harus memenuhi:</h4>
          <ul>
            <li>Minimal 8 karakter</li>
            <li>Kombinasi huruf dan angka (direkomendasikan)</li>
            <li>Password dan konfirmasi harus sama</li>
          </ul>
        </div>

        <button type="submit" class="btn-submit">
          <span>🔒</span>
          <span>Reset Password</span>
        </button>
      </form>
    </div>
  </div>
</div>
@endsection