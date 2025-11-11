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

  .login-container {
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

  /* Bagian Kanan - Form Login */
  .login-form-section {
    flex: 0 0 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: white;
  }

  .login-form-wrapper {
    max-width: 480px;
    width: 100%;
  }

  .login-form-wrapper h2 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
  }

  .login-form-wrapper > p {
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

  .remember-me {
    display: flex;
    align-items: center;
    margin-bottom: 32px;
  }

  .remember-me input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
  }

  .remember-me label {
    font-size: 14px;
    font-weight: 400;
    cursor: pointer;
    margin: 0;
    color: white;
  }

  .btn-login {
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
  }

  .btn-login:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  .btn-login:active {
    transform: translateY(0);
  }

  .links {
    margin-top: 24px;
    display: flex;
    justify-content: space-between;
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

    .login-form-wrapper h2 {
      font-size: 40px;
    }
  }

  @media (max-width: 768px) {
    .login-container {
      flex-direction: column;
    }

    .logo-section {
      flex: 0 0 40%;
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
      max-width: 260px;
    }

    .login-form-section {
      flex: 0 0 60%;
      padding: 40px 30px;
    }

    .login-form-wrapper h2 {
      font-size: 36px;
    }
  }
</style>

<div class="login-container">
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

  <!-- Bagian Kanan: Form Login -->
  <div class="login-form-section">
    <div class="login-form-wrapper">
      <h2>Login</h2>
      <p>Silahkan login dengan email &amp; password anda untuk mengakses sistem</p>

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

      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Masukan email anda" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Masukan password anda" required>
        </div>

        <div class="remember-me">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn-login">Login</button>

        <div class="links">
          <a href="{{ route('forgot.password') }}">Forgot Password?</a>
          <a href="{{ route('register') }}">Sign Up</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection