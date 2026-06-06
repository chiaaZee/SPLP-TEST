@php
    $customizerHidden = 'customizer-hide';
    $configData = \App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Menunggu Persetujuan - SPLPD Apps')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
    <style>
        .timeline-step {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #fff;
            font-weight: bold;
            z-index: 10;
        }

        .timeline-step.completed {
            background-color: #28c76f;
            /* Success green */
        }

        .timeline-step.active {
            background-color: #ff9f43;
            /* Warning orange */
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.2);
        }

        .timeline-line {
            flex-grow: 1;
            height: 2px;
            background-color: #e9ecef;
            margin: 0 10px;
        }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-' . $configData['style'] . '.png') }}"
                        alt="auth-verify-email-cover" class="img-fluid my-5 auth-illustration"
                        data-app-light-img="illustrations/auth-verify-email-illustration-light.png"
                        data-app-dark-img="illustrations/auth-verify-email-illustration-dark.png">

                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['style'] . '.png') }}"
                        alt="auth-verify-email-cover" class="platform-bg"
                        data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png">
                </div>
            </div>
            <!-- /Left Text -->

            <!--  Verify Email -->
            <div class="d-flex col-12 col-lg-5 align-items-center p-4 p-sm-5">
                <div class="w-px-400 mx-auto">
                    <div class="app-brand mb-4">
                        <a href="{{url('/')}}" class="app-brand-link gap-2">
                            <span
                                class="app-brand-logo demo">@include('_partials.macros', ["height" => 20, "withbg" => 'fill: #fff;'])</span>
                        </a>
                    </div>

                    @if(auth()->user()?->status === 'rejected')
                        <h3 class="mb-1 text-danger">Pendaftaran Ditolak 🚫</h3>
                        <p class="text-start mb-4">
                            Mohon maaf, pendaftaran akun Anda tidak disetujui oleh Administrator.
                        </p>

                        <div class="alert alert-danger mb-4" role="alert">
                            <h6 class="alert-heading mb-1"><i class="ti ti-ban me-1"></i> Akun Ditolak</h6>
                            <span>Silakan memperbaiki data pendaftaran Anda dengan cara mendaftar ulang.</span>
                        </div>

                        <form method="POST" action="{{ route('account.reset') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 mb-3">
                                <i class="ti ti-refresh me-1"></i> Daftar Ulang
                            </button>
                        </form>

                    @else
                        <h3 class="mb-1">Verifikasi Sedang Diproses ⏳</h3>
                        <p class="text-start mb-4">
                            Terima kasih telah mendaftar! Data perangkat daerah Anda sedang ditinjau oleh tim administrator kami.
                        </p>

                        <!-- Timeline -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="timeline-step completed"><i class="ti ti-check"></i></div>
                            <div class="timeline-line bg-success"></div>
                            <div class="timeline-step active"><i class="ti ti-clock"></i></div>
                            <div class="timeline-line"></div>
                            <div class="timeline-step"><i class="ti ti-lock"></i></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mb-4">
                            <span>Daftar</span>
                            <span class="fw-bold text-dark">Verifikasi</span>
                            <span>Aktif</span>
                        </div>

                        <div class="alert alert-warning mb-4" role="alert">
                            <h6 class="alert-heading mb-1"><i class="ti ti-alert-circle me-1"></i> Mohon Menunggu</h6>
                            <span>Fitur dashboard akan otomatis terbuka setelah akun disetujui. Silakan cek kembali secara
                                berkala.</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="ti ti-arrow-left me-1"></i> Kembali ke Beranda
                            </button>
                        </form>
                    @endif

                    <p class="text-center mb-0">
                        Salah akun?
                        <a href="javascript:void(0);"
                            onclick="event.preventDefault(); document.getElementById('logout-form-link').submit();">
                            Keluar
                        </a>
                    <form id="logout-form-link" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    </p>
                </div>
            </div>
            <!-- /Verify Email -->
        </div>
    </div>
@endsection
