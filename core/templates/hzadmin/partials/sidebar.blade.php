{{--
  Admin sidebar — site name, admin menu, version info.
  Rendered inside the daisyUI drawer-side panel.
--}}
@php
  $sitename = Config::get('sitename', 'Hubzero');
  $mod      = app()->bound('module') ? app('module') : null;
@endphp
<div class="drawer-side z-40">
  <label for="admin-drawer" aria-label="Close sidebar" class="drawer-overlay"></label>
  <aside class="bg-base-300 text-base-content w-48 h-screen sticky top-0 flex flex-col">
    {{-- Site name / logo --}}
    <div class="p-4 border-b border-base-content/10">
      <a href="/admin/" class="text-lg font-bold text-primary-dark hover:text-primary-focus">
        {{ $sitename }}
      </a>
      <div class="text-xs text-muted-foreground mt-0.5">Administration</div>
    </div>

    {{-- Admin menu --}}
    <nav class="flex-1 p-2"
         aria-label="{{ Lang::txt('TPL_HZADMIN_MAIN_NAV', 'Admin navigation') }}">
      @if($mod && $mod->count('menu'))
        {!! $mod->position('menu') !!}
      @endif
    </nav>

    {{-- Version info --}}
    <div class="p-4 border-t border-base-content/10 text-xs text-muted-foreground">
      Hubzero {{ defined('HVERSION') ? HVERSION : '' }}
    </div>
  </aside>
</div>
