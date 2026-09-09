{{--
  Application view page with tabs — daisyUI layout.

  Variables from controller:
    $application  — Application model
    $active       — Current active tab (defaults to 'details' via Request)
    $accesstoken  — Optional access token string (for personal access token display)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$active  = $active ?? Request::getCmd('active', 'details');
$appsUrl = Route::url('index.php?option=com_developer&controller=applications');

$tabs = [
    'details' => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_DETAILS'),
    'tokens'  => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_TOKENS'),
];
@endphp

<x-page-container :title="e($application->get('name'))">
  @slot('actions')
    <a class="btn btn-ghost btn-sm" href="{{ $appsUrl }}">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_ALL') }}
    </a>
  @endslot

  @slot('tabs')
    <div class="flex items-center justify-between w-full">
      <div class="tabs tabs-border">
        @foreach ($tabs as $alias => $label)
          <a href="{{ Route::url($application->link() . '&active=' . $alias) }}"
             @class(['tab', 'tab-active' => $active === $alias])>
            {{ $label }}
          </a>
        @endforeach
      </div>
      <a class="btn btn-ghost btn-sm"
         href="{{ Route::url($application->link('edit')) }}">
        {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_SETTINGS') }}
      </a>
    </div>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_DEVELOPER_API_APPLICATION_WHATS_NEXT', 'What\'s Next?')">
      <ul class="menu menu-sm p-0">
        <li>
          <a href="{{ Route::url('index.php?option=com_developer&controller=api&task=docs#authentication') }}">
            Authentication
          </a>
        </li>
        <li>
          <a href="{{ Route::url('index.php?option=com_developer&controller=api&task=docs#rate-limiting') }}">
            Rate Limiting
          </a>
        </li>
        <li>
          <a href="{{ Route::url('index.php?option=com_developer&controller=api&task=docs#schema') }}">
            Schema
          </a>
        </li>
        <li>
          <a href="{{ Route::url('index.php?option=com_developer&controller=api&task=docs#errors') }}">
            Errors
          </a>
        </li>
        <li>
          <a href="{{ Route::url('index.php?option=com_developer&controller=api&task=docs#endpoints') }}">
            Endpoints
          </a>
        </li>
      </ul>
    </x-sidebar-card>
  @endslot

  {{-- Render the active tab content --}}
  @if ($active === 'personalaccesstoken')
    {!! $__view->view('personalaccesstoken')
          ->set('application', $application)
          ->set('accesstoken', $accesstoken ?? null)
          ->loadTemplate() !!}
  @elseif ($active === 'tokens')
    {!! $__view->view('tokens')
          ->set('application', $application)
          ->loadTemplate() !!}
  @else
    {!! $__view->view('details')
          ->set('application', $application)
          ->loadTemplate() !!}
  @endif

</x-page-container>
