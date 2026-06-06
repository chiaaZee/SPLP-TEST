@php
$customizerHidden = 'customizer-hide';
$configData = \App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Sedang Dalam Pemeliharaan - 503')

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
<!--Under Maintenance -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h2 class="mb-1 mx-2">Sedang Dalam Pemeliharaan!</h2>
    <p class="mb-4 mx-2">
      Maaf atas ketidaknyamanan ini, kami sedang melakukan pemeliharaan sistem saat ini.<br>
      Silakan cek kembali beberapa saat lagi.
    </p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mb-4">Kembali ke Beranda</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="page-misc-under-maintenance" width="550" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper misc-under-maintenance-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="page-misc-under-maintenance" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
<!-- /Under Maintenance -->
@endsection
