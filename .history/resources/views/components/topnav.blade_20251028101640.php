<nav class="navbar navbar-top navbar-expand-md navbar-light bg-white border-bottom">
  <div class="container-fluid">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
      <img src="{{ asset('/') }}argonpro/assets/img/brand/pln iconplus.png"
           class="navbar-brand-img mr-2" alt="logo" style="height:50px;">
    </a>

    <!-- Hamburger (Bootstrap 4) -->
    <button class="navbar-toggler custom-hamburger" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="hamburger-box">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">

        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('dashboard') }}">
            <i class="ni ni-shop text-primary mr-1"></i> Dashboard
          </a>
        </li>

        <!-- Sales Report -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-dark" href="#" id="navbar-sales"
             role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ni ni-chart-bar-32 text-info mr-1"></i> Sales Report
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-sales">
            <a class="dropdown-item" href="{{ route('reports.activity') }}">Report Activity</a>
            <a class="dropdown-item" href="{{ route('reports.competitor') }}">Report Competitor</a>
          </div>
        </li>

        <!-- Operational Report -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-dark" href="#" id="navbar-operational"
             role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ni ni-settings-gear-65 text-orange mr-1"></i> Operational Report
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-operational">
           <!-- Update link menu menjadi: -->
            <a class="dropdown-item" href="{{ route('report.operational.index') }}">Input Data Pelanggan</a>
           <a class="dropdown-item" href="{{ route('report.customer.search') }}">Cari Pelanggan & kode FAT</a>
          </div>
        </li>

         <!-- Tambah User -->
       <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle text-dark" href="#" id="navbar-sales"
     role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="ni ni-single-02 text-info mr-1"></i> User Management
  </a>
  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-sales">
    <a class="dropdown-item" href="{{ route('users.index') }}">Daftar User</a>
  </div>
</li>

       <!-- Export Data -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle text-dark" href="#" id="navbar-export"
     role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="ni ni-cloud-download-95 text-success mr-1"></i> Export Data
  </a>
  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-export">
    <a class="dropdown-item" href="{{ route('export.activity') }}">Report Activity</a>
    <a class="dropdown-item" href="{{ route('export.competitor') }}">Report Competitor</a>
    <a class="dropdown-item" href="{{ route('export.operational') }}">Report Operational</a>
  </div>
</li>

<!-- Debug: Cek data user -->
@php
    $user = Auth::user();
     //dd($user->profile_photo_path); // Uncomment untuk debug
@endphp
      <!-- User Profile -->
      <ul class="navbar-nav align-items-center ml-4">
        <li class="nav-item dropdown">
          <a class="nav-link pr-0 text-dark" href="#" id="userDropdown" role="button"
             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle">
                @if(Auth::user()->profile_photo_path)
                  <img alt="Profile Image" src="{{ Storage::url(Auth::user()->profile_photo_path) }}" class="rounded-circle">
                @else
                  <img alt="Default Avatar" src="{{ asset('argonpro/assets/img/theme/team-4.jpg') }}" class="rounded-circle">
                @endif
              </span>
              <div class="media-body ml-2 d-none d-lg-block">
                <span class="mb-0 text-sm font-weight-bold">{{ Auth::user()->name ?? 'User' }}</span>
              </div>
            </div>
          </a>
         <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
    <!-- Profile Link -->
    <a href="{{ route('profile.show') }}" class="dropdown-item">
        <i class="ni ni-single-02"></i>
        <span>Profile</span>
    </a>

    <!-- Settings/Change Password Link
    <a href="{{ route('profile.change.password') }}" class="dropdown-item">
        <i class="ni ni-settings-gear-65"></i>
        <span>Settings</span>
    </a>
     -->
    <div class="dropdown-divider"></div>

    <!-- Logout Link -->
    <a href="{{ route('logout') }}" class="dropdown-item"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="ni ni-user-run"></i>
        <span>Logout</span>
    </a>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <!-- Sedikit CSS utk bentuk hamburger seperti gambar, TANPA mengubah warna tema -->
  <style>
    .custom-hamburger { border:2px solid rgba(0,0,0,.25); border-radius:12px; padding:6px 10px; }
    .custom-hamburger:focus { outline: none; box-shadow: 0 0 0 0.2rem rgba(0,0,0,.05); }
    .custom-hamburger .hamburger-box { display:inline-block; }
    .custom-hamburger .hamburger-line {
      display:block; width:24px; height:2px; margin:5px 0; background: currentColor; opacity:.6;
    }

    /* Tambahan agar dropdown muncul tepat di bawah parent */
    .navbar-nav .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      margin-top: 0;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }
    .navbar-nav .dropdown {
      position: relative;
    }

    /* Styling untuk avatar image */
    .avatar img {
      width: 36px;
      height: 36px;
      object-fit: cover;
    }

  </style>
</nav>
