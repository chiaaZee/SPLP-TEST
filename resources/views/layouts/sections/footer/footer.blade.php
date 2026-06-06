@php
  $containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
      <div>
        ©
        <script>document.write(new Date().getFullYear())</script>
        <a href="https://lumajangkab.go.id" target="_blank" class="footer-link text-primary fw-medium">Dinas Komunikasi
          dan Informatika Kab. Lumajang</a>
      </div>
      <div class="d-none d-lg-inline-block">
        <span class="me-3">Made with ❤️</span>
        @php
            $footer = \App\Models\Footer::first();
        @endphp
        <span class="text-muted">{{ $footer->app_version ?? 'v1.0.0' }}</span>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
