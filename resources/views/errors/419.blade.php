@php
$customizerHidden = 'customizer-hide';
$configData = \App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Sesi Berakhir - 419')

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
<!-- Page Expired -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h2 class="mb-1 mx-2">Sesi Berakhir</h2>
    <p class="mb-4 mx-2">Maaf, sesi Anda telah berakhir. Silakan muat ulang halaman dan login kembali.</p>
    <a href="{{url('/login')}}" class="btn btn-primary mb-4">Login Kembali</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-you-are-not-authorized.png') }}" alt="page-misc-not-authorized" width="170" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="page-misc-not-authorized" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
<!-- /Page Expired -->
@endsection
