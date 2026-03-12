{{--
  Admin submenu — component sub-navigation rendered as daisyUI tabs.
--}}
@php
  $mod       = app()->bound('module') ? app('module') : null;
  $hideLinks = Request::getBool('hidemainmenu');
@endphp
@if(!$hideLinks && $mod && $mod->count('submenu'))
  <nav class="bg-base-100 border-b border-base-300 px-6"
       aria-label="{{ Lang::txt('TPL_HZADMIN_COMPONENT_NAV', 'Component navigation') }}">
    <div class="admin-submenu">
      {!! $mod->position('submenu') !!}
    </div>
  </nav>
@endif
