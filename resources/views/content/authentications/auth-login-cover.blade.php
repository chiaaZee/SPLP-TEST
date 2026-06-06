@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Masuk')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

@section('page-style')
  @vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/pages-auth.js'
  ])
  <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">


      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 p-0">


        <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
          <img src="{{ asset('assets/img/illustrations/auth-login-illustration-' . $configData['style'] . '.png') }}"
            alt="auth-login-cover" class="img-fluid my-5 auth-illustration"
            data-app-light-img="illustrations/auth-login-illustration-light.png"
            data-app-dark-img="illustrations/auth-login-illustration-dark.png">

          <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['style'] . '.png') }}"
            alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
            data-app-dark-img="illustrations/bg-shape-image-dark.png">
        </div>
      </div>
      <!-- /Left Text -->

      <!-- Login -->
      <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4">
            <a href="{{url('/')}}" class="app-brand-link gap-2">
              <span
                class="app-brand-logo demo">@include('_partials.macros', ["height" => 80, "withbg" => 'fill: #fff;'])</span>
            </a>
          </div>
          <!-- /Logo -->
          <!-- /Logo -->
          <h3 class=" mb-1">Selamat Datang di SPLP Daerah Pemkab Lumajang! 👋</h3>
          <p class="mb-4">Sistem Penghubung Layanan Pemerintah Daerah. Silakan masuk untuk mengelola integrasi data.</p>

          <form id="formAuthentication" class="mb-3" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                placeholder="Contoh: admin@dinas.go.id" value="{{ old('email') }}" autofocus>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Kata Sandi</label>
                <a href="{{ route('password.request') }}">
                  <small>Lupa Kata Sandi?</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                  name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                <label class="form-check-label" for="remember-me">
                  Ingat Saya
                </label>
              </div>
            </div>
            <!-- <div class="mb-3">
              <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div> -->
            <button class="btn btn-primary d-grid w-100" type="submit">
              Masuk
            </button>
          </form>

          <p class="text-center">
            <span>Belum memiliki akun?</span>
            <a href="{{ route('register') }}">
              <span>Daftar Sekarang</span>
            </a>
          </p>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
@endsection
