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

  .register-container {
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

  /* Bagian Kanan - Form Register */
  .register-form-section {
    flex: 0 0 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: white;
    overflow-y: auto;
  }

  .register-form-wrapper {
    max-width: 480px;
    width: 100%;
    padding: 20px 0;
  }

  .register-form-wrapper h2 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
  }

  .register-form-wrapper > p {
    font-size: 16px;
    margin-bottom: 40px;
    line-height: 1.5;
    font-weight: 400;
    color: white;
  }

  .alert-box {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
  }

  .alert-error {
    background: rgba(255, 82, 82, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 82, 82, 0.4);
  }

  .alert-success {
    background: rgba(76, 217, 100, 0.2);
    color: #fff;
    border: 1px solid rgba(76, 217, 100, 0.4);
  }

  .alert-box ul {
    margin: 0;
    padding-left: 20px;
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

  .form-group input[type="text"],
  .form-group input[type="email"],
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

  .password-wrapper {
    position: relative;
  }

  .password-wrapper input {
    padding-right: 50px;
  }

  .toggle-password {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    font-size: 18px;
    transition: color 0.3s ease;
  }

  .toggle-password:hover {
    color: #333;
  }

  .btn-register {
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
    margin-top: 8px;
  }

  .btn-register:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  .btn-register:active {
    transform: translateY(0);
  }

  .links {
    margin-top: 24px;
    text-align: center;
    font-size: 14px;
  }

  .links a {
    color: white;
    text-decoration: underline;
    font-weight: 400;
    transition: color 0.3s ease;
  }

  .links a:hover {
    color: #d0e8f2;
  }

  .privacy-notice {
    margin-top: 20px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    font-size: 13px;
    text-align: center;
    color: white;
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

    .register-form-wrapper h2 {
      font-size: 40px;
    }
  }

  @media (max-width: 768px) {
    .register-container {
      flex-direction: column;
      height: auto;
      min-height: 100vh;
    }

    .logo-section {
      flex: 0 0 auto;
      padding: 30px;
      min-height: 35vh;
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
      max-width: 260px;
    }

    .register-form-section {
      flex: 0 0 auto;
      padding: 40px 30px;
      overflow-y: visible;
    }

    .register-form-wrapper h2 {
      font-size: 36px;
    }

    .register-form-wrapper > p {
      font-size: 14px;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }
  }

  /* Custom scrollbar untuk form section */
  .register-form-section::-webkit-scrollbar {
    width: 8px;
  }

  .register-form-section::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
  }

  .register-form-section::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
  }

  .register-form-section::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
  }
</style>

<div class="register-container">
  <!-- Bagian Kiri: Logo -->
  <div class="logo-section">
    <div class="logo-content">
      <img src="{{ asset('argonpro/assets/img/brand/iconnet2.png') }}" alt="PLN Icon Plus" class="iconnet-logo">
      <div class="powered-by">
        <br />
      </div>
    </div>
  </div>

  <!-- Bagian Kanan: Form Register -->
  <div class="register-form-section">
    <div class="register-form-wrapper">
      <h2>Register</h2>
      <p>Silahkan daftar dengan data Anda untuk bergabung dengan sistem kami</p>

      @if(session('error'))
        <div class="alert-box alert-error">
          {{ session('error') }}
        </div>
      @endif

      @if(session('success'))
        <div class="alert-box alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="alert-box alert-error">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register.post') }}">
        @csrf
        
        <div class="form-group">
          <label for="name">Nama Lengkap</label>
          <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap Anda" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Masukkan alamat email Anda" required value="{{ old('email') }}">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input id="password" name="password" type="password" placeholder="Buat password yang kuat" required>
            <span class="toggle-password" onclick="togglePassword('password', 'eyePassword')">
              <i class="ni ni-fat-remove" id="eyePassword"></i>
            </span>
          </div>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Konfirmasi Password</label>
          <div class="password-wrapper">
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Konfirmasi password Anda" required>
            <span class="toggle-password" onclick="togglePassword('password_confirmation', 'eyePasswordConfirm')">
              <i class="ni ni-fat-remove" id="eyePasswordConfirm"></i>
            </span>
          </div>
        </div>

        <button type="submit" class="btn-register">Daftar Sekarang</button>

        <div class="privacy-notice">
          🔒 Data Anda akan dijaga keamanannya sesuai kebijakan privasi PT Iconet Bali
        </div>

        <div class="links">
          Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);
    
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      eyeIcon.className = "ni ni-fat-add";
    } else {
      passwordInput.type = "password";
      eyeIcon.className = "ni ni-fat-remove";
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    if (passwordInput) {
      passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (strength >= 3) {
          this.style.borderColor = '#10b981';
        } else if (strength >= 2) {
          this.style.borderColor = '#f59e0b';
        } else if (password.length > 0) {
          this.style.borderColor = '#ef4444';
        } else {
          this.style.borderColor = '';
        }
      });
    }

    // Real-time password confirmation check
    function checkPasswordMatch() {
      if (passwordInput && confirmPasswordInput) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword.length > 0) {
          if (password === confirmPassword) {
            confirmPasswordInput.style.borderColor = '#10b981';
          } else {
            confirmPasswordInput.style.borderColor = '#ef4444';
          }
        } else {
          confirmPasswordInput.style.borderColor = '';
        }
      }
    }

    if (passwordInput) {
      passwordInput.addEventListener('input', checkPasswordMatch);
    }
    
    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }
  });
</script>
@endsection