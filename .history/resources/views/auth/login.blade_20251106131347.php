@extends('layouts.auth')

@section('content')
<body class="bg-default">
  <style>
    /* Custom CSS for enhanced design */
    :root {
      --primary-blue: #1e3a8a;
      --secondary-blue: #1e40af;
      --dark-blue: #0f172a;
      --light-blue: #3b82f6;
      --accent-blue: #60a5fa;
    }

    .bg-gradient-enhanced {
      background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 50%, var(--secondary-blue) 100%);
      position: relative;
      overflow: hidden;
    }

    .bg-gradient-enhanced::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .floating-shapes {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 1;
    }

    .shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
      width: 80px;
      height: 80px;
      top: 20%;
      left: 20%;
      animation-delay: 0s;
    }

    .shape:nth-child(2) {
      width: 120px;
      height: 120px;
      top: 60%;
      right: 20%;
      animation-delay: 2s;
    }

    .shape:nth-child(3) {
      width: 60px;
      height: 60px;
      bottom: 20%;
      left: 10%;
      animation-delay: 4s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    .enhanced-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      z-index: 10;
    }

    .enhanced-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(30, 58, 138, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
      border-radius: 20px;
      z-index: -1;
    }

    .form-control-enhanced {
      border-radius: 12px;
      border: 2px solid #e5e7eb;
      padding: 12px 16px;
      transition: all 0.3s ease;
      font-size: 16px;
    }

    .form-control-enhanced:focus {
      border-color: var(--light-blue);
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
      outline: none;
    }

    .input-group-enhanced {
      position: relative;
      margin-bottom: 20px;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
      z-index: 5;
    }

    .input-group-enhanced input {
      padding-left: 50px;
    }

    .btn-enhanced {
      background: linear-gradient(135deg, var(--primary-blue) 0%, var(--light-blue) 100%);
      border: none;
      border-radius: 12px;
      padding: 14px 32px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.4);
    }

    .btn-enhanced:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 15px 35px -5px rgba(30, 58, 138, 0.5);
      background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--accent-blue) 100%);
    }

    .btn-enhanced:disabled {
      background: #9ca3af;
      box-shadow: none;
      cursor: not-allowed;
    }

    .toggle-password {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
      z-index: 5;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: var(--light-blue);
    }

    .custom-checkbox-enhanced .custom-control-label::before {
      border-radius: 6px;
      border: 2px solid #d1d5db;
      transition: all 0.3s ease;
    }

    .custom-checkbox-enhanced .custom-control-input:checked ~ .custom-control-label::before {
      background-color: var(--light-blue);
      border-color: var(--light-blue);
    }

    .navbar-enhanced {
      backdrop-filter: blur(10px);
      background: rgba(15, 23, 42, 0.9);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-link-enhanced {
      color: rgba(255, 255, 255, 0.8) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      border-radius: 8px;
      padding: 8px 16px !important;
      margin: 0 4px;
    }

    .nav-link-enhanced:hover {
      color: white !important;
      background: rgba(59, 130, 246, 0.2);
      transform: translateY(-1px);
    }

    .alert-enhanced {
      border-radius: 12px;
      border: none;
      padding: 16px;
      margin-bottom: 24px;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
      color: #dc2626;
      border-left: 4px solid #dc2626;
    }

    .alert-success {
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      color: #16a34a;
      border-left: 4px solid #16a34a;
    }

    .footer-links {
      backdrop-filter: blur(10px);
      background: rgba(15, 23, 42, 0.8);
      border-radius: 12px;
      padding: 16px 24px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .footer-links a:hover {
      color: var(--accent-blue);
      text-decoration: none;
    }

    .company-logo {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: 8px 16px;
      margin-bottom: 32px;
      display: inline-block;
    }

    .welcome-text {
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .main-container {
      min-height: 100vh;
      position: relative;
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .enhanced-card {
        margin: 20px;
        border-radius: 16px;
      }

      .shape {
        display: none;
      }
    }
  </style>

  <!-- Navbar -->
  <nav id="navbar-main" class="navbar navbar-horizontal navbar-transparent navbar-main navbar-expand-lg navbar-light navbar-enhanced">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse navbar-custom-collapse collapse" id="navbar-collapse">
        <div class="navbar-collapse-header">
          <div class="row">
            <div class="col-6 collapse-brand">
              <a href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/img/brand/blue.png') }}">
              </a>
            </div>
            <div class="col-6 collapse-close">
              <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
              </button>
            </div>
          </div>
        </div>
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a href="{{ route('login') }}" class="nav-link nav-link-enhanced">
              <span class="nav-link-inner--text">Login</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('register') }}" class="nav-link nav-link-enhanced">
              <span class="nav-link-inner--text">Register</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <div class="main-content main-container">
    <!-- Header -->
    <div class="header bg-gradient-enhanced py-7 py-lg-8 pt-lg-9">
      <!-- Floating shapes for visual interest -->
      <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
      </div>

      <div class="container" style="position: relative; z-index: 5;">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 px-5">
              <div class="company-logo">
                <h5 class="text-white mb-0">PT PLN ICON PLUS BALI</h5>
              </div>
              <h1 class="text-white welcome-text mb-4" style="font-size: 3rem; font-weight: 700;">!</h1>
              <p class="text-lead text-white" style="font-size: 1.2rem; opacity: 0.9;">Silakan login dengan email & password Anda untuk mengakses sistem</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card enhanced-card border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center mb-4">
                <h3 style="color: var(--primary-blue); font-weight: 600; margin-bottom: 8px;">Masuk ke Akun Anda</h3>
                <p class="text-muted mb-0">Login dengan email & password</p>
              </div>

              <!-- pesan error -->
              @if(session('error'))
                <div class="alert alert-danger alert-enhanced">
                  <i class="ni ni-support-16 mr-2"></i>
                  {{ session('error') }}
                </div>
              @endif

              <!-- pesan success -->
              @if(session('success'))
                <div class="alert alert-success alert-enhanced">
                  <i class="ni ni-check-bold mr-2"></i>
                  {{ session('success') }}
                </div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger alert-enhanced">
                  <i class="ni ni-support-16 mr-2"></i>
                  <ul class="mb-0" style="list-style: none; padding-left: 0;">
                    @foreach ($errors->all() as $error)
                      <li style="margin-bottom: 8px;"><i class="ni ni-fat-remove mr-2"></i>{{ $error }}</li>
                    @endforeach
                  </ul>
                  @if(session('show_forgot_password'))
                    <hr class="my-3">
                    <div class="text-center">
                      <a href="{{ route('forgot.password') }}" class="btn btn-outline-warning btn-sm" style="border-radius: 8px;">
                        <i class="ni ni-key-25"></i> Lupa Password?
                      </a>
                    </div>
                  @endif
                </div>
              @endif

              <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email Input -->
                <div class="input-group-enhanced">
                  <div class="input-icon">
                    <i class="ni ni-email-83"></i>
                  </div>
                  <input name="email" class="form-control form-control-enhanced" placeholder="Masukkan email Anda" type="email" required value="{{ old('email') }}">
                </div>

                <!-- Password Input -->
                <div class="input-group-enhanced">
                  <div class="input-icon">
                    <i class="ni ni-lock-circle-open"></i>
                  </div>
                  <input name="password" id="passwordField" class="form-control form-control-enhanced" placeholder="Masukkan password Anda" type="password" required>
                  <div class="toggle-password" id="togglePassword">
                    <i class="ni ni-fat-remove" id="eyeIcon"></i>
                  </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="custom-control custom-control-alternative custom-checkbox custom-checkbox-enhanced mb-4">
                  <input class="custom-control-input" id="customCheckLogin" type="checkbox" name="remember" value="1">
                  <label class="custom-control-label" for="customCheckLogin">
                    <span style="color: var(--primary-blue); font-weight: 500;">Remember me</span>
                  </label>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                  <button type="submit" id="loginButton" class="btn btn-enhanced my-4 w-100" disabled style="opacity: 0.5;">
                    <i class="ni ni-button-power mr-2"></i>
                    Sign In
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Footer Links -->
          <div class="footer-links mt-4 text-center">
            <div class="row">
              <div class="col-6">
                <a href="{{ route('forgot.password') }}">
                  <i class="ni ni-key-25 mr-1"></i>
                  <small>Lupa password?</small>
                </a>
              </div>
              <div class="col-6">
                <a href="{{ route('register') }}">
                  <i class="ni ni-circle-08 mr-1"></i>
                  <small>Buat akun baru</small>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const rememberCheckbox = document.getElementById('customCheckLogin');
      const loginButton = document.getElementById('loginButton');
      const togglePassword = document.getElementById('togglePassword');
      const passwordField = document.getElementById('passwordField');
      const eyeIcon = document.getElementById('eyeIcon');

      // Function to update button state
      function updateButtonState() {
        if (rememberCheckbox.checked) {
          loginButton.disabled = false;
          loginButton.style.opacity = '1';
        } else {
          loginButton.disabled = true;
          loginButton.style.opacity = '0.5';
        }
      }

      // Listen for changes on the checkbox
      rememberCheckbox.addEventListener('change', updateButtonState);

      // Initial state check
      updateButtonState();

      // Toggle password visibility
      togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle eye icon - menggunakan icon yang sama dengan register
        if (type === 'text') {
          eyeIcon.className = 'ni ni-fat-add'; // Password terlihat (mata terbuka)
        } else {
          eyeIcon.className = 'ni ni-fat-remove'; // Password tersembunyi (mata tertutup)
        }
      });

      // Prevent form submission if remember is not checked
      document.querySelector('form').addEventListener('submit', function(e) {
        if (!rememberCheckbox.checked) {
          e.preventDefault();
          alert('Anda harus mencentang "Remember me" untuk dapat login.');
          return false;
        }
      });

      // Add smooth animations for form interactions
      const inputs = document.querySelectorAll('.form-control-enhanced');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.style.transform = 'translateY(-2px)';
        });

        input.addEventListener('blur', function() {
          this.parentElement.style.transform = 'translateY(0)';
        });
      });
    });
  </script>
</body>
@endsection
