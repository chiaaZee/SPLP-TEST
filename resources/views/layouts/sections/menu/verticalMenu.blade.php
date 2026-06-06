@php
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  @if(!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{route('dashboard')}}" class="app-brand-link">
        <span class="app-brand-logo demo" style="margin-top: 2px;">
          @include('_partials.macros', ["height" => 35])
        </span>
        <div class="d-flex flex-column ms-2">
            <span class="app-brand-text demo menu-text fw-bold" style="line-height: 1.2; font-size: 1.3rem; padding-top: 3px;">{{config('variables.templateName')}}</span>
            <span class="app-brand-text demo menu-text fw-normal text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">{{config('variables.templateSuffix')}}</span>
        </div>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
      </a>
    </div>
  @endif


  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)
      @php
        // Check permission restrictions
        if (isset($menu->permission)) {
          if (!auth()->user() || !auth()->user()->can($menu->permission)) {
            continue;
          }
        }

        // Check excludeRole - hide menu from users with this role
        if (isset($menu->excludeRole)) {
          if (auth()->user() && auth()->user()->hasRole($menu->excludeRole)) {
            continue;
          }
        }
      @endphp

      {{-- adding active and open class if child is active --}}

      {{-- menu headers --}}
      @if (isset($menu->menuHeader))
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>

      @else

        {{-- active menu method --}}
        @php
          $activeClass = null;
          $currentRouteName = Route::currentRouteName();

          if ($currentRouteName === $menu->slug) {
            $activeClass = 'active';
          } elseif (request()->routeIs($menu->slug)) {
            $activeClass = 'active';
          } elseif (isset($menu->submenu)) {
            if (gettype($menu->slug) === 'array') {
              foreach ($menu->slug as $slug) {
                if (str_contains($currentRouteName, $slug) and strpos($currentRouteName, $slug) === 0) {
                  $activeClass = 'active open';
                }
              }
            } else {
              if (str_contains($currentRouteName, $menu->slug) and strpos($currentRouteName, $menu->slug) === 0) {
                $activeClass = 'active open';
              }
            }
          }
        @endphp

        {{-- main menu --}}
        <li class="menu-item {{$activeClass}}">
          <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
            @isset($menu->icon)
              <i class="{{ $menu->icon }}"></i>
            @endisset
            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
            @isset($menu->badge)
              <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>

            @endisset
          </a>

          {{-- submenu --}}
          @isset($menu->submenu)
            @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
          @endisset
        </li>
      @endif
    @endforeach
  </ul>

</aside>
