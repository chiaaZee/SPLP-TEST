@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Reset Kata Sandi')

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
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg ">
  <div class="authentication-inner row">

    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-'.$configData['style'].'.png') }}" alt="auth-reset-password-cover" class="img-fluid my-5 auth-illustration" data-app-light-img="illustrations/auth-reset-password-illustration-light.png" data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-reset-password-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Reset Password -->
    <div class="d-flex col-12 col-lg-5 align-items-center p-4 p-sm-5">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-4">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',['height'=>80,'withbg' => "fill: #fff;"])</span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-1">Ubah Kata Sandi 🔒</h4>

        <p class="mb-4">untuk <span class="fw-medium">{{ $email ?? '' }}</span></p>
        <form id="formAuthentication" class="mb-3" action="{{ route('password.update') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          <input type="hidden" name="email" value="{{ $email ?? '' }}">
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Kata Sandi Baru</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="confirm-password">Konfirmasi Kata Sandi</label>
            <div class="input-group input-group-merge">
              <input type="password" id="confirm-password" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
           </div>
             <div id="password-feedback" class="mt-2 d-none">
                <small class="fw-bold mb-1 d-block">Syarat Kata Sandi:</small>
                <ul class="list-unstyled mb-0 small text-muted">
                    <li id="rule-length"><i class="ti ti-circle me-1"></i> Minimal 8 karakter</li>
                    <li id="rule-mixed"><i class="ti ti-circle me-1"></i> Huruf besar & kecil</li>
                    <li id="rule-number"><i class="ti ti-circle me-1"></i> Angka & Simbol</li>
                </ul>
             </div>
             <div id="password-match" class="mt-2 d-none">
                 <small class="fw-bold" id="match-message"></small>
             </div>
          <button class="btn btn-primary d-grid w-100 mb-3">
            Atur Kata Sandi Baru
          </button>
          <div class="text-center">
            <a href="{{ route('login') }}">
              <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
              Kembali ke Masuk
            </a>
          </div>
        </form>
      </div>
    </div>
    <!-- /Reset Password -->
  </div>
</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Modern Password Validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm-password');
    const feedbackBox = document.getElementById('password-feedback');
    const matchBox = document.getElementById('password-match');
    const matchMessage = document.getElementById('match-message');

    const ruleLength = document.getElementById('rule-length');
    const ruleMixed = document.getElementById('rule-mixed');
    const ruleNumber = document.getElementById('rule-number');

    function validatePassword() {
        if(!passwordInput) return;
        const val = passwordInput.value;
        if(feedbackBox) feedbackBox.classList.remove('d-none');

        // Length Rule
        if (ruleLength) {
            if (val.length >= 8) {
                ruleLength.classList.replace('text-muted', 'text-success');
                ruleLength.querySelector('i').classList.replace('ti-circle', 'ti-check');
            } else {
                ruleLength.classList.replace('text-success', 'text-muted');
                ruleLength.querySelector('i').classList.replace('ti-check', 'ti-circle');
            }
        }

        // Mixed Case Rule
        if (ruleMixed) {
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) {
                ruleMixed.classList.replace('text-muted', 'text-success');
                ruleMixed.querySelector('i').classList.replace('ti-circle', 'ti-check');
            } else {
                ruleMixed.classList.replace('text-success', 'text-muted');
                ruleMixed.querySelector('i').classList.replace('ti-check', 'ti-circle');
            }
        }

        // Number & Symbol Rule
        if (ruleNumber) {
            if (/\d/.test(val) && /[\W_]/.test(val)) {
                ruleNumber.classList.replace('text-muted', 'text-success');
                ruleNumber.querySelector('i').classList.replace('ti-circle', 'ti-check');
            } else {
                ruleNumber.classList.replace('text-success', 'text-muted');
                ruleNumber.querySelector('i').classList.replace('ti-check', 'ti-circle');
            }
        }

        validateMatch();
    }

    function validateMatch() {
        if(!confirmInput || !matchBox || !matchMessage) return;

        const pass = passwordInput.value;
        const confirm = confirmInput.value;

        if (confirm.length > 0) {
            matchBox.classList.remove('d-none');
            if (pass === confirm) {
                matchMessage.textContent = '✅ Kata sandi cocok!';
                matchMessage.className = 'fw-bold text-success';
            } else {
                matchMessage.textContent = '❌ Kata sandi tidak cocok';
                matchMessage.className = 'fw-bold text-danger';
            }
        } else {
            matchBox.classList.add('d-none');
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', validatePassword);
        passwordInput.addEventListener('focus', () => feedbackBox.classList.remove('d-none'));
    }
    if (confirmInput) {
        confirmInput.addEventListener('input', validateMatch);
    }
  });
</script>
@endsection
