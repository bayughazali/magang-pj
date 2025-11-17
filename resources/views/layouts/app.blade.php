<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Start your development with a dshboard for Bootstrap 4.">
  <meta name="author" content="Creative Tim">
  <title>Argon Dashboard PRO - Premium Bootstrap 4 Admin Template</title>
  <!-- Favicon -->
  <link rel="icon" href="{{ asset('/') }}argonpro/assets/img/brand/favicon.png" type="image/png">
  <!-- Fonts -->
  <link rel="stylesheet" href="{{ asset('/') }}argonpro/https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('/') }}argonpro/assets/vendor/nucleo/css/nucleo.css" type="text/css">
  <link rel="stylesheet" href="{{ asset('/') }}argonpro/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
  <!-- Page plugins -->
  <!-- Argon CSS -->
  <link rel="stylesheet" href="{{ asset('/') }}argonpro/assets/css/argon.css?v=1.1.0" type="text/css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @stack('styles')
</head>

<body>
  <!-- Sidenav -->
  <x-sidenav />
  <!-- Main content -->
  <div class="main-content" id="panel">
    <!-- Topnav -->
    <x-topnav />
    <!-- Header -->
    <!-- Header -->

    <!-- Page content -->
     @yield('content')

  </div>
  <!-- Argon Scripts -->
  <!-- Core -->
  <script src="{{ asset('/') }}argonpro/assets/vendor/jquery/dist/jquery.min.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/js-cookie/js.cookie.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>
  <!-- Optional JS -->
  <script src="{{ asset('/') }}argonpro/assets/vendor/chart.js/dist/Chart.min.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/chart.js/dist/Chart.extension.js"></script>
  <script src="{{ asset('/') }}argonpro/assets/vendor/chart.js/dist/Chart.extension.js"></script>
  <!-- Argon JS -->
  <script src="{{ asset('/') }}argonpro/assets/js/argon.js?v=1.1.0"></script>
  <!-- Demo JS - remove this in your project -->
  <script src="{{ asset('/') }}argonpro/assets/js/demo.min.js"></script>
  @stack('scripts')
</body>

</html>
