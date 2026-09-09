{{--
  API portal home page — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$docsUrl     = Route::url('index.php?option=com_developer&controller=api&task=docs');
$appsUrl     = Route::url('index.php?option=com_developer&controller=applications');
$authAppsUrl = Route::url('index.php?option=com_developer&controller=applications#authorized');
$newAppUrl   = Route::url('index.php?option=com_developer&controller=applications&task=new');
$devUrl      = Route::url('index.php?option=com_developer');
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_HOME')">
  @slot('actions')
    <a class="btn btn-ghost btn-sm" href="{{ $devUrl }}">
      {{ Lang::txt('COM_DEVELOPER') }}
    </a>
  @endslot

  {{-- Getting Started --}}
  <div class="card bg-base-100 shadow-sm mb-6">
    <div class="card-body">
      <div class="flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex-1">
          <h3 class="card-title text-xl">{{ Lang::txt('COM_DEVELOPER_API_GETSTARTED') }}</h3>
          <p class="mt-2">{!! Lang::txt('COM_DEVELOPER_API_GETSTARTED_DESC') !!}</p>
        </div>
        <div class="card-actions">
          <a href="{{ $docsUrl }}" class="btn btn-primary">
            {{ Lang::txt('COM_DEVELOPER_API_LINK_DOCUMENTATION') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Applications grid --}}
  <h2 class="text-xl font-semibold mb-4">{{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS') }}</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title">
          <a class="link link-hover" href="{{ $appsUrl }}">
            {{ Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS') }}
          </a>
        </h3>
        <p>{!! Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS_DESC') !!}</p>
        <div class="card-actions justify-end mt-2">
          <a href="{{ $appsUrl }}" class="btn btn-sm btn-ghost">
            {{ Lang::txt('COM_DEVELOPER_API_MANAGE') }}
          </a>
        </div>
      </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title">
          <a class="link link-hover" href="{{ $authAppsUrl }}">
            {{ Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS') }}
          </a>
        </h3>
        <p>{!! Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS_DESC') !!}</p>
        <div class="card-actions justify-end mt-2">
          <a href="{{ $authAppsUrl }}" class="btn btn-sm btn-ghost">
            {{ Lang::txt('COM_DEVELOPER_API_MANAGE') }}
          </a>
        </div>
      </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title">
          <a class="link link-hover" href="{{ $newAppUrl }}">
            {{ Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION') }}
          </a>
        </h3>
        <p>{!! Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION_DESC') !!}</p>
        <div class="card-actions justify-end mt-2">
          <a href="{{ $newAppUrl }}" class="btn btn-sm btn-primary">
            {{ Lang::txt('COM_DEVELOPER_API_CREATE') }}
          </a>
        </div>
      </div>
    </div>
  </div>

</x-page-container>
