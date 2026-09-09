{{--
  Member Account — set local password form.

  Variables from plugin (onMembers):
    $option         — component option
    $id             — member ID
    $notifications  — notification messages array
    $password_rules — password validation rules array
    $change         — password change error messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()
      ->css('providers.css', 'com_login')
      ->js()
      ->js('jquery.hoverIntent', 'system');

  $formUrl = Route::url('index.php?option=' . $option . '&id=' . $id . '&active=account&task=setlocalpass');
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD') }}</h3>

@if (!empty($notifications))
  @foreach ($notifications as $notification)
    <div class="alert {{ $notification['type'] === 'error' ? 'alert-error' : 'alert-info' }} mb-4" role="alert">
      {{ $notification['message'] }}
    </div>
  @endforeach
@endif

@if ($__view->getError())
  <div class="alert alert-error mb-4" role="alert">{{ $__view->getError() }}</div>
@endif

<div class="card bg-base-100 shadow-sm">
  <div class="card-body">
    <form action="{{ $formUrl }}" method="post" class="space-y-4">
      <fieldset>
        <legend class="text-sm font-medium mb-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD') }}</legend>

        <div class="grid grid-cols-1 {{ count($password_rules) > 0 ? 'md:grid-cols-2' : '' }} gap-6">
          <div class="space-y-4" id="password-group">
            <div class="form-control w-full">
              <label class="label" for="password1">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD') }}</span>
              </label>
              <input id="password1" name="password1" type="password" class="input input-bordered w-full" />
            </div>
            <div class="form-control w-full">
              <label class="label" for="password2">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_VERIFY_PASSWORD') }}</span>
              </label>
              <input id="password2" name="password2" type="password" class="input input-bordered w-full" />
            </div>
          </div>

          @if (count($password_rules) > 0)
            <div id="passrules-container">
              <h5 class="text-sm font-medium mb-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD_RULES') }}</h5>
              <ul id="passrules" class="list-disc list-inside text-sm space-y-1">
                @foreach ($password_rules as $rule)
                  @if (!empty($rule))
                    @php
                      $isError = !empty($change) && is_array($change) && in_array($rule, $change);
                    @endphp
                    <li class="{{ $isError ? 'text-error font-medium' : 'text-base-content/60' }}">{{ $rule }}</li>
                  @endif
                @endforeach
                @if (!empty($change) && is_array($change))
                  @foreach ($change as $msg)
                    @if (!in_array($msg, $password_rules))
                      <li class="text-error font-medium">{{ $msg }}</li>
                    @endif
                  @endforeach
                @endif
              </ul>
            </div>
          @endif
        </div>
      </fieldset>

      <div class="flex gap-2">
        <input type="hidden" name="change" value="1" />
        <input type="hidden" name="no_html" id="pass_no_html" value="0" />
        <input type="hidden" name="redirect" id="pass_redirect" value="1" />
        <button type="submit" id="password-change-save" class="btn btn-primary">
          {{ Lang::txt('PLG_MEMBERS_ACCOUNT_SUBMIT') }}
        </button>
      </div>

      {!! Html::input('token') !!}
    </form>
  </div>
</div>
