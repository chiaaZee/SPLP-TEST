@php
$customizerHidden = 'customizer-hide';
$configData = \App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Halaman Tidak Ditemukan - 404')

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
<!-- Error -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h2 class="mb-1 mt-4">Halaman Tidak Ditemukan :(</h2>
    <p class="mb-4 mx-2">Maaf, URL yang Anda tuju tidak ditemukan di server ini.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Home</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="page-misc-error" width="225" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="page-misc-error" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
<!-- /Error -->
@endsection
