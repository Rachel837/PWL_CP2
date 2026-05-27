<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Authentication')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/site.webmanifest') }}">

  <script type="module" crossorigin src="{{ asset('assets/js/main.js') }}"></script>
  <link rel="stylesheet" crossorigin href="{{ asset('assets/css/main.css') }}">
</head>

<body>
  @yield('content')
</body>

</html>
