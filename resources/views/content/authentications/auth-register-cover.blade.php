@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Daftar')

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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const isAgencyCheckbox = document.getElementById('is_agency');
    const agencyFields = document.getElementById('agency-fields');

    if(isAgencyCheckbox && agencyFields){
      isAgencyCheckbox.addEventListener('change', function() {
        if (this.checked) {
          agencyFields.classList.remove('d-none');
        } else {
          agencyFields.classList.add('d-none');
        }
      });
    }
  });
</script>
@endsection


@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-register-illustration-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="img-fluid my-5 auth-illustration" data-app-light-img="illustrations/auth-register-illustration-light.png" data-app-dark-img="illustrations/auth-register-illustration-dark.png">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Register -->
    <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-4">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["height"=>80,"withbg"=>'fill: #fff;'])</span>
          </a>
        </div>
        <!-- /Logo -->
        <h3 class="mb-1">Petualangan dimulai di sini 🚀</h3>
        <p class="mb-4">Kelola aplikasi dinas Anda dengan mudah dan menyenangkan!</p>

        <form id="formAuthentication" class="mb-3" action="{{ route('register.post') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="username" class="form-label">Nama PIC</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan nama lengkap PIC" autofocus>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email Anda">
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Nomor Whatsapp</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Contoh: 08123456789">
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            <div class="input-group input-group-merge mt-2">
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
             </div>
             <div id="password-feedback" class="mt-2 d-none">
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="password-strength-bar"></div>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-2">
                   <small class="text-muted" id="pill-length"><i class="ti ti-circle me-1"></i>Min 8 Karakter</small>
                   <small class="text-muted" id="pill-mixed"><i class="ti ti-circle me-1"></i>Huruf Besar & Kecil</small>
                   <small class="text-muted" id="pill-number"><i class="ti ti-circle me-1"></i>Angka & Simbol</small>
                </div>
             </div>
             <div id="password-match" class="mt-2 d-none">
                 <small class="fw-bold" id="match-message"></small>
             </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_agency" name="is_agency" value="1">
              <label class="form-check-label" for="is_agency">
                Daftar sebagai Dinas / Instansi
              </label>
            </div>
          </div>

          <div id="agency-fields" class="d-none">
            <div class="mb-3">
                <label for="agency_id" class="form-label">Pilih Instansi / Dinas</label>
                <select class="form-select" id="agency_id" name="agency_id">
                    <option value="" selected disabled>Pilih Dinas Anda...</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->name }} ({{ $agency->code }})</option>
                    @endforeach
                    <option value="other">Lainnya (Belum Terdaftar)</option>
                </select>
            </div>

            <div id="new-agency-fields" class="d-none">
                <div class="mb-3">
                    <label for="agency_name" class="form-label">Nama Dinas Baru</label>
                    <input type="text" class="form-control" id="agency_name" name="agency_name" placeholder="Contoh: Dinas Kesehatan">
                </div>
                <div class="mb-3">
                    <label for="agency_code" class="form-label">Kode Dinas / Singkatan</label>
                    <input type="text" class="form-control" id="agency_code" name="agency_code" placeholder="Contoh: DINKES">
                    <div class="form-text">Kode unik atau singkatan resmi instansi Anda.</div>
                </div>
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
              <label class="form-check-label" for="terms-conditions">
                Saya setuju dengan
                <a href="javascript:void(0);" class="text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#privacyPolicyModal">kebijakan privasi & syarat ketentuan</a>
              </label>
            </div>
          </div>
          <div class="mb-3">
              <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
          </div>
          <button class="btn btn-primary d-grid w-100" type="submit">
            Daftar
          </button>
        </form>

        <p class="text-center">
          <span>Sudah punya akun?</span>
          <a href="{{ route('login') }}">
            <span>Masuk saja</span>
          </a>
        </p>

      </div>
    </div>
    <!-- /Register -->
  </div>
</div>

<!-- Premium Privacy Policy Modal -->
<div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-primary">
        <h5 class="modal-title fw-bold text-primary"><i class="ti ti-shield-lock me-2"></i>Kebijakan Privasi & Syarat Ketentuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <div class="avatar avatar-xl mx-auto mb-2">
            <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-file-description ti-lg"></i></span>
          </div>
          <h4 class="mb-1">Selamat Datang di SPLPD Apps</h4>
          <p class="text-muted">Harap membaca syarat dan ketentuan berikut dengan seksama sebelum melanjutkan.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center rounded bg-label-secondary p-3">
                    <span class="badge bg-white p-2 me-3 rounded shadow-sm"><i class="ti ti-user ti-md text-primary"></i></span>
                    <div>
                        <h6 class="mb-0 fw-bold">Data Pengguna</h6>
                        <small class="text-muted">Nama, Email & Kontak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center rounded bg-label-secondary p-3">
                    <span class="badge bg-white p-2 me-3 rounded shadow-sm"><i class="ti ti-building-skyscrapper ti-md text-info"></i></span>
                    <div>
                        <h6 class="mb-0 fw-bold">Data Perangkat Daerah</h6>
                        <small class="text-muted">Profil Dinas & Kode Unit</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center rounded bg-label-secondary p-3">
                    <span class="badge bg-white p-2 me-3 rounded shadow-sm"><i class="ti ti-lock ti-md text-success"></i></span>
                    <div>
                        <h6 class="mb-0 fw-bold">Keamanan Data</h6>
                        <small class="text-muted">Enkripsi & Proteksi Penuh</small>
                    </div>
                </div>
            </div>
             <div class="col-md-6">
                <div class="d-flex align-items-center rounded bg-label-secondary p-3">
                    <span class="badge bg-white p-2 me-3 rounded shadow-sm"><i class="ti ti-gavel ti-md text-danger"></i></span>
                    <div>
                        <h6 class="mb-0 fw-bold">Regulasi</h6>
                        <small class="text-muted">Sesuai UU PDP Pemerintah</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion mt-3" id="policyAccordion">
            <div class="accordion-item shadow-none border border-bottom-0 active mb-0">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  <i class="ti ti-info-circle me-2"></i> 1. Pengumpulan & Penggunaan Data
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#policyAccordion">
                <div class="accordion-body">
                  <p class="mb-0">
                    Kami mengumpulkan data identitas pengelola dan data perangkat daerah semata-mata untuk keperluan <strong>verifikasi layanan pemerintah</strong>. Data ini digunakan untuk memfasilitasi integrasi antar sistem daerah dan pelaporan audit. Kami tidak memperjualbelikan data Anda kepada pihak ketiga manapun.
                  </p>
                </div>
              </div>
            </div>
            <div class="accordion-item shadow-none border mb-0">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                   <i class="ti ti-shield-check me-2"></i> 2. Keamanan & Tanggung Jawab
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#policyAccordion">
                <div class="accordion-body">
                  <p class="mb-0">
                    Akun Anda diamankan dengan teknologi enkripsi terkini. Anda bertanggung jawab menjaga kerahasiaan kata sandi Anda. Segala aktivitas yang terjadi di bawah akun Anda adalah tanggung jawab pengguna.
                  </p>
                </div>
              </div>
            </div>
          </div>

      </div>
      <div class="modal-footer bg-label-secondary">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnAgree">
            <i class="ti ti-check me-1"></i> Saya Setuju & Lanjutkan
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Agency Toggle Logic
    const isAgencyCheckbox = document.getElementById('is_agency');
    const agencyFields = document.getElementById('agency-fields');

    if(isAgencyCheckbox && agencyFields){
      isAgencyCheckbox.addEventListener('change', function() {
        if (this.checked) {
          agencyFields.classList.remove('d-none');
        } else {
          agencyFields.classList.add('d-none');
        }
      });
    }

    // Toggle New Agency Fields based on Dropdown
    const agencySelect = document.getElementById('agency_id');
    const newAgencyFields = document.getElementById('new-agency-fields');

    if (agencySelect && newAgencyFields) {
        agencySelect.addEventListener('change', function() {
            if (this.value === 'other') {
                newAgencyFields.classList.remove('d-none');
            } else {
                newAgencyFields.classList.add('d-none');
            }
        });
    }

    // Modal & Checkbox Logic
    const btnAgree = document.getElementById('btnAgree');
    const termsCheckbox = document.getElementById('terms-conditions');
    const modalElement = document.getElementById('privacyPolicyModal');



    if(termsCheckbox && modalElement){
      termsCheckbox.addEventListener('click', function(e) {
        // If the user tries to check the box (it was unchecked, now checked)
        if(this.checked){
             e.preventDefault(); // Cancel the check immediately
             this.checked = false; // Ensure it visually stays unchecked

             // Open modal
             const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
             modalInstance.show();
        }
        // If they are unchecking it, let them do it (no preventDefault)
      });
    }

    if(btnAgree && termsCheckbox){
      btnAgree.addEventListener('click', function() {
        // Check the box programmatically
        termsCheckbox.checked = true;

        // Hide Modal properly using Bootstrap 5 API
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.hide();
      });
    }

    // Modern Password Validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const feedbackBox = document.getElementById('password-feedback');
    const matchBox = document.getElementById('password-match');
    const matchMessage = document.getElementById('match-message');

    const strengthBar = document.getElementById('password-strength-bar');
    const pillLength = document.getElementById('pill-length');
    const pillMixed = document.getElementById('pill-mixed');
    const pillNumber = document.getElementById('pill-number');

    function validatePassword() {
        const val = passwordInput.value;
        feedbackBox.classList.remove('d-none');

        let score = 0;
        let checks = {
            length: val.length >= 8,
            mixed: /[a-z]/.test(val) && /[A-Z]/.test(val),
            number: /\d/.test(val) && /[\W_]/.test(val)
        };

        // Update Pills
        updatePill(pillLength, checks.length);
        updatePill(pillMixed, checks.mixed);
        updatePill(pillNumber, checks.number);

        // Calculate Score
        if(checks.length) score += 33.33;
        if(checks.mixed) score += 33.33;
        if(checks.number) score += 33.34; // Total 100

        // Update Bar
        strengthBar.style.width = score + '%';
        if(score < 50) {
            strengthBar.className = 'progress-bar bg-danger';
        } else if (score < 100) {
            strengthBar.className = 'progress-bar bg-warning';
        } else {
            strengthBar.className = 'progress-bar bg-success';
        }

        validateMatch();
    }

    function updatePill(element, isValid) {
        if(isValid) {
            element.classList.remove('text-muted');
            element.classList.add('text-success', 'fw-bold');
            element.querySelector('i').classList.replace('ti-circle', 'ti-check');
        } else {
            element.classList.add('text-muted');
            element.classList.remove('text-success', 'fw-bold');
            element.querySelector('i').classList.replace('ti-check', 'ti-circle');
        }
    }

    function validateMatch() {
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
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
