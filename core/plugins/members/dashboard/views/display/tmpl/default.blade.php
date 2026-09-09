{{--
  Member Dashboard — module grid with customization support.

  Variables from plugin (displayAction):
    $modules  — array of module objects with positioning data
    $admin    — boolean, admin context
    $params   — plugin params Registry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  $customizable = ($params->get('allow_customization', 1) != 0);
  $addUrl = Route::url(
      'index.php?option=com_members&id=' . User::get('id')
      . '&active=dashboard&action=add'
  );
@endphp

@if ($customizable)
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_DASHBOARD') }}</h3>
    <a class="add-module btn btn-sm btn-primary" href="{{ $addUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="2" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      {{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES') }}
    </a>
  </div>
@else
  <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_DASHBOARD') }}</h3>
@endif

<noscript>
  <div class="alert alert-warning" role="alert">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT') }}</div>
</noscript>

<div class="modules-container">
  <div class="modules {{ $customizable ? 'customizable' : '' }}"
       data-userid="{{ User::get('id') }}"
       data-token="{{ Session::getFormToken() }}">
    @foreach ($modules as $module)
      {!! $__view->view('module')
          ->set('admin', $admin)
          ->set('module', $module)
          ->loadTemplate() !!}
    @endforeach
  </div>
</div>

<div class="modules-empty hidden">
  <div class="text-center py-12">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
         stroke-width="1" stroke="currentColor" class="size-24 mx-auto text-base-content/20 mb-4" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" />
    </svg>
    <h3 class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_TITLE') }}</h3>
    <p class="text-base-content/60 mt-2">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_DESC') }}</p>
  </div>
</div>

{{-- Add Modules dialog (native <dialog>) --}}
@if ($customizable)
  <dialog id="add-modules-dialog" class="add-modules-dialog">
    <div class="dialog-header">
      <h2>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_TITLE') }}</h2>
      <button class="dialog-close btn btn-sm btn-ghost" aria-label="{{ Lang::txt('JCANCEL') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="2" stroke="currentColor" class="size-5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="dialog-body"></div>
  </dialog>
@endif
