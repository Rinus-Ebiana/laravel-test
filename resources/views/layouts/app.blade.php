<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <title>@yield('title', 'Sistem Perwalian')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/button-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sort-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/search-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/title-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/layout-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/form-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/icon-style.css') }}">

  @stack('styles')
</head>
<body class="loaded">

  @include('layouts.partials.navbar')

  @include('layouts.partials.navigasi')

  <main>
    @if (session('success'))
      <div class="container" style="max-width: 1285px; margin-top: 20px;">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    @endif
    @yield('content')
  </main>
  
  @include('components.modal')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/modal.js') }}"></script>

  @stack('scripts')
</body>
</html>