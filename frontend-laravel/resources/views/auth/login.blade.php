@extends('layouts.auth')

@section('title', 'Login - InApp Inventory Dashboard')

@section('content')
<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="card" style="max-width:420px; width:100%;">
    <div class="card-body p-5">
      <div class="text-center mb-3">
        <a href="/" class="mb-4 d-inline-block">
          <img src="data:image/svg+xml,%3csvg%20width='62'%20height='67'%20viewBox='0%200%2062%2067'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M30.604%2066.378L0.00805664%2048.1582V35.7825L30.604%2054.0023V66.378Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2048.1582L30.604%2066.378V54.0023L61.1996%2035.7825V48.1582Z'%20fill='%23E66239'/%3e%3cpath%20d='M30.5955%200L0%2018.2198V30.5955L30.5955%2012.3757V0Z'%20fill='%23657E92'/%3e%3cpath%20d='M61.191%2018.2198L30.5955%200V12.3757L61.191%2030.5955V18.2198Z'%20fill='%23A3B2BE'/%3e%3cpath%20d='M30.604%2048.8457L0.00805664%2030.6259V18.2498L30.604%2036.47V48.8457Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2030.6259L30.604%2048.8457V36.47L61.1996%2018.2498V30.6259Z'%20fill='%23E66239'/%3e%3c/svg%3e" alt="" width="36">
          <span class="ms-2"><img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
        </a>
        <h1 class="card-title mb-5 h5">Sign in to your account</h1>
      </div>

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

      <form action="{{ route('login') }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input id="email" type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label d-flex justify-content-between">
            <span>Password</span>
          </label>
          <input id="password" type="password" name="password" class="form-control" placeholder="Password" required minlength="6">
        </div>

        <button class="btn btn-primary w-100 mt-2" type="submit">Sign in</button>
      </form>
    </div>
  </div>
</div>
@endsection
