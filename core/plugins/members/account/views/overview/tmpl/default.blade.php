{{--
  Member Account — auth providers, password management, SSH keys.

  Variables from plugin (onMembers):
    $member          — member profile object
    $notifications   — notification messages array
    $hzalaccounts    — linked auth accounts array
    $domains_unused  — available auth domains
    $passtype        — password action type (changelocal/changehub/set)
    $passinfo        — password expiration info array
    $password_rules  — password validation rules array
    $change          — password change error messages
    $params          — plugin params
    $option          — component option
    $key             — SSH key text or false

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css()
      ->css('providers.css', 'com_login')
      ->js()
      ->js('jquery.hoverIntent', 'system');
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_ACCOUNT') }}</h3>

@if (!empty($notifications))
  @foreach ($notifications as $notification)
    <div class="alert {{ $notification['type'] === 'error' ? 'alert-error' : 'alert-info' }} mb-4" role="alert">
      {{ $notification['message'] }}
    </div>
  @endforeach
@endif

<div class="space-y-6">

  {{-- Linked accounts --}}
  @if (count($domains_unused) > 0 || !empty($hzalaccounts[0]))
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h4 class="card-title text-base">{{ Lang::txt('PLG_MEMBERS_LINKED_ACCOUNTS') }}</h4>

        @if ($hzalaccounts)
          <h5 class="text-sm font-medium mt-4 mb-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_ACTIVE_PROVIDERS') }}:</h5>
          <div class="flex flex-wrap gap-3">
            @foreach ($hzalaccounts as $hzala)
              @php
                $plugin = Plugin::byType('authentication', $hzala['auth_domain_name']);
                $pparams = new \Hubzero\Config\Registry($plugin->params);
                $displayName = $pparams->get('display_name', ucfirst($hzala['auth_domain_name']));
                $unlinkUrl = Route::url($member->link() . '&active=account&action=unlink&hzal_id=' . $hzala['id']);
              @endphp
              <div class="flex items-center gap-2 rounded-lg border border-base-300 px-4 py-3">
                <div>
                  <span class="text-xs text-base-content/60">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE') }}:</span>
                  <span class="font-medium">{{ $displayName }}</span>
                </div>
                <a href="{{ $unlinkUrl }}" class="btn btn-ghost btn-xs text-error"
                   title="{{ Lang::txt('PLG_MEMBERS_ACCOUNT_REMOVE_ACCOUNT') }}"
                   aria-label="{{ Lang::txt('PLG_MEMBERS_ACCOUNT_REMOVE_ACCOUNT') }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </a>
              </div>
            @endforeach
          </div>
        @endif

        @if ($domains_unused)
          <h5 class="text-sm font-medium mt-4 mb-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_AVAILABLE_PROVIDERS') }}:</h5>
          <div class="flex flex-wrap gap-3">
            @foreach ($domains_unused as $domain)
              @php
                $plugin = Plugin::byType('authentication', $domain->name);
                $pparams = new \Hubzero\Config\Registry($plugin->params);
                $displayName = $pparams->get('display_name', ucfirst($domain->name));
                $refl = new \ReflectionClass('plgauthentication' . $domain->name);
                $hasRender = $refl->hasMethod('onRenderOption');
                $html = $hasRender ? $refl->getMethod('onRenderOption')->invoke(null) : null;
                $loginUrl = Route::url('index.php?option=com_users&view=login&authenticator=' . $domain->name);
              @endphp

              @if ($html)
                {!! is_array($html) ? implode("\n", $html) : $html !!}
              @else
                <a href="{{ $loginUrl }}" class="rounded-lg border border-base-300 px-4 py-3 hover:bg-base-200 transition-colors">
                  <span class="text-xs text-base-content/60">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE') }}:</span>
                  <span class="font-medium">{{ $displayName }}</span>
                </a>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </div>
  @endif

  {{-- Password section --}}
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h4 class="card-title text-base">
        @if ($passtype === 'changelocal')
          {{ Lang::txt('PLG_MEMBERS_CHANGE_LOCAL_PASSWORD') }}
        @elseif ($passtype === 'changehub')
          {{ Lang::txt('PLG_MEMBERS_CHANGE_HUB_PASSWORD') }}
        @elseif ($passtype === 'set')
          {{ Lang::txt('PLG_MEMBERS_SET_LOCAL_PASSWORD') }}
        @endif
      </h4>

      @if ($passtype === 'changelocal' || $passtype === 'changehub')
        <form action="index.php" method="post" data-section-registration="password" data-section-profile="password"
              class="space-y-4 mt-4">
          @if (is_array($passinfo))
            <div class="alert {{ $passinfo['message_style'] === 'error' ? 'alert-error' : 'alert-warning' }}" role="alert">
              @if ($passinfo['diff'] < 0)
                {{ Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD_EXPIRED_EXPLANATION', -$passinfo['diff'], $passinfo['max']) }}
              @else
                {{ Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD_EXPIRATION_EXPLANATION', $passinfo['diff'], $passinfo['max']) }}
              @endif
            </div>
          @endif

          <div class="grid grid-cols-1 {{ count($password_rules) > 0 ? 'md:grid-cols-2' : '' }} gap-6">
            <div class="space-y-4" id="password-group">
              <div class="form-control w-full">
                <label class="label" for="oldpass">
                  <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_CURRENT_PASSWORD') }}</span>
                </label>
                <input type="password" name="oldpass" id="oldpass" class="input input-bordered w-full" />
              </div>
              <div class="form-control w-full">
                <label class="label" for="newpass1">
                  <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_NEW_PASSWORD') }}</span>
                </label>
                <input type="password" name="newpass" id="newpass1" class="input input-bordered w-full" />
              </div>
              <div class="form-control w-full">
                <label class="label" for="newpass2">
                  <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_CONFIRM_NEW_PASSWORD') }}</span>
                </label>
                <input type="password" name="newpass2" id="newpass2" class="input input-bordered w-full" />
              </div>

              <div class="flex gap-2">
                <button type="submit" id="password-change-save" class="btn btn-primary">
                  {{ Lang::txt('PLG_MEMBERS_ACCOUNT_SAVE') }}
                </button>
                <button type="reset" id="pass-cancel" class="btn btn-ghost">
                  {{ Lang::txt('PLG_MEMBERS_ACCOUNT_CANCEL') }}
                </button>
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

          <input type="hidden" name="change" value="1" />
          <input type="hidden" name="option" value="com_members" />
          <input type="hidden" name="id" value="{{ $member->get('id') }}" />
          <input type="hidden" name="task" value="changepassword" />
          <input type="hidden" name="no_html" id="pass_no_html" value="0" />
          {!! Html::input('token') !!}
        </form>
      @else
        <p class="text-base-content/70 mt-2">{{ Lang::txt('PLG_MEMBERS_ACCOUNT_LOCAL_PASS_EXPLANATION') }}</p>
        <a class="btn btn-primary mt-4"
           href="{{ Route::url($member->link() . '&active=account&task=sendtoken') }}">
          {{ Lang::txt('PLG_MEMBERS_ACCOUNT_REQUEST_TOKEN') }}
        </a>
      @endif
    </div>
  </div>

  {{-- SSH keys --}}
  @if ($params->get('ssh_key_upload', 0))
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h4 class="card-title text-base">{{ Lang::txt('PLG_MEMBERS_LOCAL_SERVICES') }}</h4>

        <h5 class="text-sm font-medium mt-4 mb-1">{{ Lang::txt('PLG_MEMBERS_LOCAL_SERVICES_USERNAME') }}</h5>
        <p class="text-base-content/70 mb-4">
          {{ Lang::txt('PLG_MEMBERS_LOCAL_SERVICES_USERNAME_DESC') }}
          <code class="badge badge-ghost">{{ User::get('username') }}</code>
        </p>

        <h5 class="text-sm font-medium mb-2">{{ Lang::txt('PLG_MEMBERS_MANAGE_KEYS') }}</h5>
        @if ($key !== false)
          @php
            $uploadKeyUrl = Route::url($member->link() . '&active=account&task=uploadkey', true, true);
          @endphp
          <form action="{{ $uploadKeyUrl }}" method="post" class="space-y-4">
            <div class="form-control w-full">
              <label class="label" for="keytext">
                <span class="label-text">{{ Lang::txt('PLG_MEMGERS_ACCOUNT_KEY_HINT') }}</span>
              </label>
              <textarea id="keytext" name="keytext" class="textarea textarea-bordered w-full font-mono text-sm"
                        rows="6">{{ $key }}</textarea>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary">{{ Lang::txt('PLG_MEMBERS_SUBMIT') }}</button>
              <button type="reset" class="btn btn-ghost">{{ Lang::txt('PLG_MEMBERS_CANCEL') }}</button>
            </div>
          </form>
        @else
          <div class="alert alert-error" role="alert">
            {{ Lang::txt('PLG_MEMBERS_ACCOUNT_KEY_ERROR_ACCESSING_HOME_DIR') }}
          </div>
        @endif
      </div>
    </div>
  @endif

</div>
