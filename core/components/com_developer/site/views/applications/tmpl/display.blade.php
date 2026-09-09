{{--
  Application listing page — daisyUI layout.

  Variables from controller:
    $applications  — Collection of user's Application models
    $tokens        — Paginated Collection of authorized Accesstoken models

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$newAppUrl = Route::url('index.php?option=com_developer&controller=applications&task=new');
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_APPLICATIONS')">
  @slot('actions')
    <a class="btn btn-primary btn-sm" href="{{ $newAppUrl }}">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW') }}
    </a>
  @endslot

  {{-- My Applications --}}
  <h3 class="text-lg font-semibold mb-3">
    {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE') }}
  </h3>

  @if ($applications->count() > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm mb-8">
      @foreach ($applications as $application)
        @php
        $appUrl = Route::url($application->link());
        $desc = e(\Hubzero\Utility\Str::truncate($application->get('description'), 500));
        @endphp
        <li class="list-row">
          <div class="list-col-grow">
            <h4 class="text-base font-semibold">
              <a class="link link-hover text-primary" href="{{ $appUrl }}">
                {{ e($application->get('name')) }}
              </a>
            </h4>
            <div class="flex items-baseline gap-x-3 text-sm text-base-content/60 mt-0.5">
              <time>{{ $application->created('date') }}</time>
              <span>{{ $application->created('time') }}</span>
              <span>{{ $application->users() }} active users</span>
            </div>
            @if ($desc)
              <p class="text-sm text-base-content/60 mt-1 line-clamp-2">{{ $desc }}</p>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  @else
    <x-empty-state :title="Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE_NONE', $newAppUrl)">
      <a class="btn btn-primary btn-sm" href="{{ $newAppUrl }}">
        {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW') }}
      </a>
    </x-empty-state>
  @endif

  {{-- Authorized Applications --}}
  <div class="divider"></div>

  <h3 class="text-lg font-semibold mb-3" id="authorized">
    {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED') }}
  </h3>

  @if ($tokens->count() > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm">
      @foreach ($tokens as $token)
        @php
        $tokenApp = $token->application;
        $revokeUrl = Route::url($tokenApp->link('revoke') . '&token=' . $token->get('id'));
        $desc = e(\Hubzero\Utility\Str::truncate($tokenApp->get('description'), 500));
        @endphp
        <li class="list-row">
          <div class="list-col-grow">
            <h4 class="text-base font-semibold">
              {{ e($tokenApp->get('name')) }}
            </h4>
            <div class="text-sm text-base-content/60 mt-0.5">
              {{ Lang::txt('Authorization Date: %s', $token->created('m/d/Y @ g:ia')) }}
            </div>
            @if ($desc)
              <p class="text-sm text-base-content/60 mt-1 line-clamp-2">{{ $desc }}</p>
            @endif
          </div>
          <div>
            <a class="btn btn-sm btn-error btn-outline"
               href="{{ $revokeUrl }}"
               data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS_CONFIRM') }}">
              {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS') }}
            </a>
          </div>
        </li>
      @endforeach
    </ul>

    <nav aria-label="Page navigation" class="flex justify-center mt-6">
      {!! $tokens->pagination !!}
    </nav>
  @else
    <div class="text-base-content/60 text-sm py-4">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_NONE') }}
    </div>
  @endif

</x-page-container>
