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

  .verify-code-container {
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

  /* Bagian Kanan - Form Verify Code */
  .verify-code-form-section {
    flex: 0 0 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: white;
  }

  .verify-code-form-wrapper {
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

  .verify-code-form-wrapper h2 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
  }

  .verify-code-form-wrapper > p {
    font-size: 16px;
    margin-bottom: 20px;
    line-height: 1.5;
    font-weight: 400;
    color: white;
  }

  .email-info {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 14px 18px;
    text-align: center;
    margin-bottom: 24px;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .email-info span {
    color: white;
    font-weight: 600;
    font-size: 16px;
    letter-spacing: 0.5px;
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

  .info-box {
    background: rgba(246, 173, 85, 0.15);
    border-left: 4px solid #f6ad55;
    padding: 16px 18px;
    border-radius: 10px;
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
  }

  .info-box p {
    color: white;
    font-size: 14px;
    line-height: 1.6;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 0;
  }

  .info-box strong {
    font-weight: 600;
  }

  .info-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    margin-top: 2px;
    fill: white;
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

  .form-group input[type="text"] {
    width: 100%;
    padding: 18px;
    border-radius: 10px;
    border: none;
    font-size: 28px;
    font-weight: 600;
    text-align: center;
    letter-spacing: 12px;
    font-family: 'Courier New', monospace;
    background: white;
    color: #1e3a5f;
    outline: none;
    transition: all 0.3s ease;
  }

  .form-group input::placeholder {
    color: #ccc;
    letter-spacing: 8px;
  }

  .form-group input:focus {
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    letter-spacing: 14px;
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
  }

  .btn-submit:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  .btn-submit:active {
    transform: translateY(0);
  }

  .back-link {
    margin-top: 24px;
    text-align: center;
  }

  .back-link a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .back-link a:hover {
    color: #d0e8f2;
    text-decoration: underline;
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

    .verify-code-form-wrapper h2 {
      font-size: 40px;
    }

    .form-group input[type="text"] {
      font-size: 24px;
      letter-spacing: 10px;
    }
  }

  @media (max-width: 768px) {
    .verify-code-container {
      flex-direction: column;
    }

    .logo-section {
      flex: 0 0 35%;
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

    .verify-code-form-section {
      flex: 0 0 65%;
      padding: 40px 30px;
    }

    .verify-code-form-wrapper h2 {
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

    .form-group input[type="text"] {
      font-size: 22px;
      letter-spacing: 8px;
      padding: 16px;
    }

    .form-group input:focus {
      letter-spacing: 10px;
    }
  }
</style>

<div class="verify-code-container">
  <!-- Bagian Kiri: Logo -->
  <div class="logo-section">
    <div class="logo-content">
      <img src="{{ asset('argonpro/assets/img/brand/iconnet2.png') }}" alt="PLN Icon Plus" class="pln-logo">
      <div class="powered-by">
        <br />
        <!-- <img src="{{ asset('assets/img/logo/plniconplus.png') }}" alt="PLN Icon Plus" /> -->
      </div>
    </div>
  </div>

  <!-- Bagian Kanan: Form Verify Code -->
  <div class="verify-code-form-section">
    <div class="verify-code-form-wrapper">
      <div class="icon-wrapper">
        <svg viewBox="0 0 24 24">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>

      <h2>Verifikasi Kode</h2>
      <p>Masukkan kode verifikasi 6 digit untuk email:</p>

      <div class="email-info">
        <span>{{ session('email') }}</span>
      </div>

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

      <div class="info-box">
        <p>
          <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <span><strong>Hubungi admin</strong> untuk mendapatkan kode verifikasi Anda. Kode berlaku 24 jam.</span>
        </p>
      </div>

      <form action="{{ route('verify.code') }}" method="POST">
        @csrf
        
        <div class="form-group">
          <label for="code">Kode Verifikasi (6 Digit)</label>
          <input
            type="text"
            id="code"
            name="code"
            placeholder="000000"
            maxlength="6"
            pattern="[0-9]{6}"
            value="{{ old('code') }}"
            required
            autofocus
          >
          @error('code')
            <div class="alert-box alert-error" style="margin-top: 12px;">
              <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        <button type="submit" class="btn-submit">
          <span>✓</span>
          <span>Verifikasi Kode</span>
        </button>
      </form>

      <div class="back-link">
        <a href="{{ route('forgot.password') }}">
          <span>←</span>
          <span>Kembali</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection