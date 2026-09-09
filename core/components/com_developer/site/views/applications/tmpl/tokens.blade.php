{{--
  Application tokens sub-template — access token list with create/revoke.

  Variables (set by parent view.blade.php):
    $application  — Application model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$total = $application->accessTokens()->count();
$filters = [
    'limit' => Request::getInt('limit', 25),
    'start' => Request::getInt('limitstart', Request::getInt('start', 0)),
];
$tokens = $application->accessTokens()
    ->limit($filters['limit'], $filters['start'])
    ->ordered()
    ->paginated()
    ->rows()
    ->sort('expires', false);

$createPatUrl = Route::url($application->link('createpat'));
$revokeAllUrl = Route::url($application->link('revokeall'));
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h3 class="text-lg font-semibold">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKENS') }}
    </h3>
    <p class="text-sm text-base-content/60">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_AUTHORIZED_TO') }}
    </p>
  </div>
  <div class="flex gap-2">
    <a class="btn btn-sm btn-success"
       href="{{ $createPatUrl }}"
       data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ADD_PERSONAL_ACCESS_TOKEN_CONFIRM') }}">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ADD_PERSONAL_ACCESS_TOKEN') }}
    </a>
    @if ($total > 0)
      <a class="btn btn-sm btn-error btn-outline"
         href="{{ $revokeAllUrl }}"
         data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_ALL_TOKEN_CONFIRM') }}">
        {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_ALL_TOKEN') }}
      </a>
    @endif
  </div>
</div>

@if ($total > 0)
  <ul class="list bg-base-100 rounded-box shadow-sm">
    @foreach ($tokens as $token)
      @php
      $tokenUser = \Hubzero\User\User::oneOrNew($token->get('uidNumber'));
      $revokeUrl = Route::url(
          $application->link('revoke')
          . '&token=' . $token->get('id')
          . '&return=tokens'
      );
      @endphp
      <li class="list-row">
        <div class="list-col-grow">
          <h4 class="text-base font-semibold">
            {{ $tokenUser->get('name') }}
          </h4>
          <div class="flex flex-wrap gap-x-4 text-sm text-base-content/60 mt-0.5">
            <span>
              {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKEN_CREATED',
                           $token->created('m/d/Y @ g:ia')) }}
            </span>
            <span>
              {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKEN_EXPIRES',
                           $token->expires('m/d/Y @ g:ia')) }}
            </span>
          </div>
        </div>
        <div>
          <a class="btn btn-sm btn-error btn-outline"
             href="{{ $revokeUrl }}"
             data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_TOKEN_CONFIRM') }}">
            {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_TOKEN') }}
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
    {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKENS_NONE') }}
  </div>
@endif
