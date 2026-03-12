{{--
  Admin top bar — drawer toggle, page title, toolbar buttons, user menu.
--}}
@php
  $mod  = app()->bound('module') ? app('module') : null;
  $user = User::getInstance();

  $logoutLink = Route::url(
      'index.php?option=com_login&task=logout&' . Session::getFormToken() . '=1',
      false, false
  );
@endphp
<header class="admin-topbar">
  {{-- Left: hamburger + page title --}}
  <div class="admin-topbar-start">
    <label for="admin-drawer"
           class="admin-topbar-hamburger"
           aria-label="{{ Lang::txt('TPL_HZADMIN_TOGGLE_MENU', 'Open menu') }}">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
      </svg>
    </label>

    @if($mod && $mod->count('title'))
      <div class="admin-topbar-title">
        {!! $mod->position('title') !!}
      </div>
    @endif
  </div>

  {{-- Right: toolbar buttons + user --}}
  <div class="admin-topbar-end">
    @if($mod && $mod->count('toolbar'))
      {!! $mod->position('toolbar') !!}
    @endif

    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button" class="admin-topbar-user">
        <svg class="admin-topbar-user-icon" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" />
        </svg>
        <span class="admin-user-name">{{ $user->get('name', 'Admin') }}</span>
      </div>
      <ul tabindex="0"
          class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg border border-base-300">
        <li>
          <a href="{{ Route::url('index.php?option=com_members&id=' . $user->get('id'), false) }}">
            {{ Lang::txt('TPL_HZADMIN_PROFILE', 'Profile') }}
          </a>
        </li>
        <li>
          <a href="{{ $logoutLink }}">
            {{ Lang::txt('TPL_HZADMIN_LOG_OUT', 'Log out') }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
