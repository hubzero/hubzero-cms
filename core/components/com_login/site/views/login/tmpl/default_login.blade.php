{{--
  Login form sub-template.

  Displays the sign-in form for guest users with local authentication
  and/or third-party provider buttons.

  Loaded via $__view->loadTemplate('login') from the dispatcher.

  Variables from controller (displayTask):
    $multiAuth          — bool: multiple auth providers enabled
    $authenticators     — array: available third-party authenticators
    $totalauths         — int: count of auth plugins
    $remember_me_default — int: default state for remember-me checkbox
    $pageclass_sfx      — string: CSS suffix from menu params
    $description        — string: menu item description
    $return             — string: base64-encoded return URL
    $freturn            — string: full base64-encoded return URL
    $status             — array: auth provider status
    $user               — object: user instance
    $params             — Registry: menu + component params
    $returnQueryString  — string: "&return={encoded_url}" or empty
    $local              — bool: whether hubzero auth is available
    $site_display       — string: site name for login display
    $option             — string: 'com_login'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Build provider button HTML via plugin reflection
  $providerButtons = [];
  foreach ($authenticators as $a) {
      $authClass = 'Plugins\\Authentication\\' . ucfirst($a['name'])
                 . '\\' . ucfirst($a['name']);
      if (class_exists($authClass)) {
          $refl = new \ReflectionClass($authClass);
          if ($refl->hasMethod('onRenderOption')) {
              $html = $refl->getMethod('onRenderOption')
                           ->invoke(null, $returnQueryString);
              $providerButtons[] = is_array($html)
                  ? implode("\n", $html) : $html;
              continue;
          }
      }
      $providerUrl = Route::url(
          'index.php?option=' . $option . '&authenticator='
          . $a['name'] . $returnQueryString,
          false
      );
      $providerButtons[] = '<a class="btn btn-outline w-full'
          . ' login-provider-' . e($a['name']) . '"'
          . ' href="' . $providerUrl . '">'
          . e(Lang::txt(
              'COM_LOGIN_LOGIN_SIGN_IN_WITH_METHOD',
              $a['display']
          ))
          . '</a>';
  }

  $usersConfig   = Component::params('com_members');
  $allowRegister = $usersConfig->get('allowUserRegistration') != '0';
  $errorText     = Request::getString('errorText', '');
  $formAction    = Route::url('index.php', true, true);
  $remindUrl     = Route::url(
      'index.php?option=com_members&task=remind', false
  );
  $resetUrl      = Route::url(
      'index.php?option=com_members&task=reset', false
  );
  $registerUrl   = Request::base(true) . '/register'
                 . ($return ? '?return=' . $return : '');
  $siteName      = isset($site_display)
      ? $site_display : Config::get('sitename');
@endphp

@if($params->get('show_page_heading', 1))
  <header class="page-header">
    <h1>
      {{ $params->get('page_heading', Lang::txt('COM_LOGIN_LOGIN')) }}
    </h1>
  </header>
@endif

<section class="py-8">
  <div class="login-wrapper">

    {{-- Error message from failed login attempt --}}
    @if($errorText)
      <div class="alert alert-error mb-6" role="alert">
        {{ $errorText }}
      </div>
    @endif

    {{-- Description from menu params --}}
    @if($description)
      <p class="text-center text-base-content/70 mb-6">
        {{ $description }}
      </p>
    @endif

    @if($totalauths)
      <div class="login-card card bg-base-100 shadow-sm">
        <div class="card-body">

          <div class="login-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
          </div>
          <h2 class="login-heading">
            {{ Lang::txt('COM_LOGIN_LOGIN_TO', $siteName) }}
          </h2>

          {{-- Third-party auth provider buttons --}}
          @if(count($providerButtons))
            <div class="login-providers">
              @foreach($providerButtons as $btnHtml)
                {!! $btnHtml !!}
              @endforeach
            </div>

            @if($local)
              <div class="login-divider">
                <span>{{ Lang::txt('COM_LOGIN_OR') }}</span>
              </div>
            @endif
          @endif

          {{-- Local username/password form --}}
          @if($local)
            <form method="post"
                  action="{{ $formAction }}"
                  class="login-form">

              <x-form-field name="username"
                            inputId="field-username"
                            :label="Lang::txt('COM_LOGIN_LOGIN_USERNAME')"
                            :required="true">
                <input type="text"
                       id="field-username"
                       name="username"
                       class="input input-bordered w-full"
                       autocomplete="username"
                       required />
              </x-form-field>

              <x-form-field name="passwd"
                            inputId="field-password"
                            :label="Lang::txt('COM_LOGIN_LOGIN_PASSWORD')"
                            :required="true">
                <input type="password"
                       id="field-password"
                       name="passwd"
                       class="input input-bordered w-full"
                       autocomplete="current-password"
                       required />
              </x-form-field>

              @if(Plugin::isEnabled('system', 'remember'))
                <x-form-field name="remember"
                              inputId="field-remember"
                              type="checkbox"
                              :label="Lang::txt('COM_LOGIN_LOGIN_KEEP_LOGGED_IN')">
                  <input type="checkbox"
                         class="checkbox"
                         name="remember"
                         id="field-remember"
                         value="yes"
                         @if($remember_me_default) checked @endif />
                </x-form-field>
              @endif

              <button type="submit"
                      class="btn btn-primary w-full">
                {{ Lang::txt('COM_LOGIN_LOGIN') }}
              </button>

              <input type="hidden" name="option"
                     value="{{ $option }}" />
              <input type="hidden" name="authenticator"
                     value="hubzero" />
              <input type="hidden" name="task" value="login" />
              <input type="hidden" name="return"
                     value="{{ e($return) }}" />
              <input type="hidden" name="freturn"
                     value="{{ e($freturn) }}" />
              {!! Html::input('token') !!}
            </form>
          @endif

          {{-- Forgot links --}}
          <div class="login-footer">
            <a class="link link-hover" href="{{ $remindUrl }}">
              {{ Lang::txt('COM_LOGIN_LOGIN_REMIND') }}
            </a>
            <a class="link link-hover" href="{{ $resetUrl }}">
              {{ Lang::txt('COM_LOGIN_LOGIN_RESET') }}
            </a>
          </div>

        </div>
      </div>

      {{-- Create account --}}
      @if($allowRegister)
        <div class="login-register">
          <span class="login-register-text">
            {{ Lang::txt('COM_LOGIN_LOGIN_REGISTER') }}
          </span>
          <a class="btn btn-outline btn-sm"
             href="{{ $registerUrl }}">
            {{ Lang::txt('COM_LOGIN_LOGIN_CREATE_ACCOUNT') }}
          </a>
        </div>
      @endif

    @else
      {{-- No auth providers available --}}
      <div class="alert alert-warning" role="alert">
        {{ Lang::txt('COM_LOGIN_LOGIN_UNAVAILABLE') }}
      </div>
    @endif

  </div>
</section>
