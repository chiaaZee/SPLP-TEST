@php
  $containerNav = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
  $navbarDetached = ($navbarDetached ?? '');
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
  <nav
    class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme"
    id="layout-navbar">
@endif
  @if(isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
      <div class="{{$containerNav}}">
  @endif

      <!--  Brand demo (display only for navbar-full and hide on below xl) -->
      @if(isset($navbarFull))
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
              @include('_partials.macros', ["height" => 20])
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
          </a>
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="ti ti-x ti-sm align-middle"></i>
          </a>
        </div>
      @endif

      <!-- ! Not required for layout-without-menu -->
      @if(!isset($navbarHideToggle))
        <div
          class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
          <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
          </a>
        </div>
      @endif

      <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        @if(!isset($menuHorizontal))
          <!-- Search -->
          <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
              <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                <i class="ti ti-search ti-md me-2"></i>
                <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
              </a>
            </div>
          </div>
          <!-- /Search -->
        @endif
        <ul class="navbar-nav flex-row align-items-center ms-auto">


          @if(isset($menuHorizontal))
            <!-- Search -->
            <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
              <a class="nav-link search-toggler" href="javascript:void(0);">
                <i class="ti ti-search ti-md"></i>
              </a>
            </li>
            <!-- /Search -->
          @endif
          @if($configData['hasCustomizer'] == true)
            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
              <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <i class='ti ti-md'></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                <li>
                  <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                    <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                    <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                    <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                  </a>
                </li>
              </ul>
            </li>
            <!--/ Style Switcher -->
          @endif

          <!-- Akses Cepat  -->
          <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
              data-bs-auto-close="outside" aria-expanded="false">
              <i class='ti ti-layout-grid-add ti-md'></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end py-0">
              <div class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h5 class="text-body mb-0 me-auto">Akses Cepat</h5>
                  <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Tambah Pintasan"><i class="ti ti-sm ti-apps"></i></a>
                </div>
              </div>
              <div class="dropdown-shortcuts-list scrollable-container">

                @role('admin')
                <!-- Menu Khusus Admin -->
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-layout-grid fs-4"></i>
                    </span>
                    <a href="{{url('admin/service-catalogs')}}" class="stretched-link">Katalog Layanan API</a>
                    <small class="text-muted mb-0">Daftar API</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-chart-bar fs-4"></i>
                    </span>
                    <a href="{{url('api-logs')}}" class="stretched-link">Monitoring API</a>
                    <small class="text-muted mb-0">Status Sistem</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-shield-lock fs-4"></i>
                    </span>
                    <a href="{{url('api-clients')}}" class="stretched-link">Kelola API Keys</a>
                    <small class="text-muted mb-0">Manajemen Kunci</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-messages fs-4"></i>
                    </span>
                    <a href="{{url('admin/tickets')}}" class="stretched-link">Kelola Bantuan Tiket</a>
                    <small class="text-muted mb-0">Support</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-users fs-4"></i>
                    </span>
                    <a href="{{url('admin/users')}}" class="stretched-link">Data User</a>
                    <small class="text-muted mb-0">Manajemen Pengguna</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-building fs-4"></i>
                    </span>
                    <a href="{{url('admin/agency')}}" class="stretched-link">Data Perangkat Daerah</a>
                    <small class="text-muted mb-0">Satuan Kerja</small>
                  </div>
                </div>

                @else
                <!-- Menu Khusus Dinas / Public -->
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-layout-grid fs-4"></i>
                    </span>
                    <a href="{{url('admin/service-catalogs')}}" class="stretched-link">Katalog Layanan API</a>
                    <small class="text-muted mb-0">Daftar API</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-chart-bar fs-4"></i>
                    </span>
                    <a href="{{url('api-logs')}}" class="stretched-link">Monitoring API</a>
                    <small class="text-muted mb-0">Status Saya</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-shield-lock fs-4"></i>
                    </span>
                    <a href="{{url('api-clients')}}" class="stretched-link">Kelola API Keys</a>
                    <small class="text-muted mb-0">Akses Saya</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                      <i class="ti ti-help-circle fs-4"></i>
                    </span>
                    <a href="{{url('tickets')}}" class="stretched-link">Tiket Bantuan</a>
                    <small class="text-muted mb-0">Lapor Masalah</small>
                  </div>
                </div>
                @endrole

              </div>
            </div>
          </li>
          <!-- Akses Cepat -->



          <!-- Notification -->
          @livewire('global.navbar-notifications')
          <!--/ Notification -->

          <!-- User -->
          <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                <img
                  src="{{ Auth::user() && Auth::user()->profile_photo_path ? Storage::url(Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'User').'&color=7F9CF5&background=EBF4FF' }}"
                  alt class="h-auto rounded-circle">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="{{ route('pages-profile-user') }}">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar avatar-online">
                        <img src="{{ Auth::user() && Auth::user()->profile_photo_path ? Storage::url(Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'User').'&color=7F9CF5&background=EBF4FF' }}" alt class="h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <span class="fw-medium d-block">
                        @if (Auth::check())
                          {{ Auth::user()->name }}
                        @else
                          John Doe
                        @endif
                      </span>
                      <small class="text-muted">{{ Auth::user()->jabatan ?? 'Pengguna' }}</small>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item"
                  href="{{ route('pages-profile-user') }}">
                  <i class="ti ti-user-check me-2 ti-sm"></i>
                  <span class="align-middle">Profil Saya</span>
                </a>
              </li>
              @if (Auth::User() && class_exists('Laravel\Jetstream\Jetstream') && Laravel\Jetstream\Jetstream::hasTeamFeatures())
                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li>
                  <h6 class="dropdown-header">Manage Team</h6>
                </li>
                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li>
                  <a class="dropdown-item"
                    href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
                    <i class='ti ti-settings me-2'></i>
                    <span class="align-middle">Team Settings</span>
                  </a>
                </li>
                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                  <li>
                    <a class="dropdown-item" href="{{ route('teams.create') }}">
                      <i class='ti ti-user me-2'></i>
                      <span class="align-middle">Create New Team</span>
                    </a>
                  </li>
                @endcan
                @if (Auth::user()->allTeams()->count() > 1)
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <h6 class="dropdown-header">Switch Teams</h6>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                @endif
                @if (Auth::user())
                  @foreach (Auth::user()->allTeams() as $team)
                    {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want
                    to use jetstream. --}}

                    {{-- <x-switchable-team :team="$team" /> --}}
                  @endforeach
                @endif
              @endif
              <li>
                <div class="dropdown-divider"></div>
              </li>
              @if (Auth::check())
                <li>
                  <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='ti ti-logout me-2'></i>
                    <span class="align-middle">Logout</span>
                  </a>
                </li>
                <form method="POST" id="logout-form" action="{{ route('logout') }}">
                  @csrf
                </form>
              @else
                <li>
                  <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                    <i class='ti ti-login me-2'></i>
                    <span class="align-middle">Login</span>
                  </a>
                </li>
              @endif
            </ul>
          </li>
          <!--/ User -->
        </ul>
      </div>

      <!-- Search Small Screens -->
      <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
        <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0"
          placeholder="Search..." aria-label="Search..." id="global-search" name="search">
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
      </div>
      @if(isset($navbarDetached) && $navbarDetached == '')
        </div>
      @endif
  </nav>
  <!-- / Navbar -->
