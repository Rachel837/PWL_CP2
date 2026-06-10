<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'InApp Inventory Dashboard')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/site.webmanifest') }}">

  <script type="module" crossorigin src="{{ asset('assets/js/main.js') }}"></script>
  <link rel="stylesheet" crossorigin href="{{ asset('assets/css/main.css') }}">
</head>

<body>
  <div id="overlay" class="overlay"></div>
  
  <!-- TOPBAR -->
  @include('layouts.partials.topbar')

  <!-- SIDEBAR -->
  @include('layouts.partials.sidebar')

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <!-- Flash Messages -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @yield('content')

      <!-- FOOTER -->
      @include('layouts.partials.footer')
    </div>
  </main>
  @yield('scripts')
</body>
</html>
