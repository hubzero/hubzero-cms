{{--
  Login form sub-template.

  Displays the sign-in form for guest users with local authentication
  and/or third-party provider buttons.

  Loaded via $this->loadTemplate('login') from the dispatcher.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
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

  // Build provider button HTML for third-party authenticators
  $providerButtons = [];
  foreach ($authenticators as $a) {
      $authClass = 'Plugins\\Authentication\\' . ucfirst($a['name'])
                 . '\\' . ucfirst($a['name']);
      if (class_exists($authClass)) {
          $refl = new \ReflectionClass($authClass);
          if ($refl->hasMethod('onRenderOption')) {
              $html = $refl->getMethod('onRenderOption')
                           ->invoke(null, $returnQueryString);
              $providerButtons[] = is_array($html) ? implode("\n", $html) : $html;
              continue;
          }
      }
      $providerUrl = Route::url(
          'index.php?option=' . $option . '&authenticator=' . $a['name']
          . $returnQueryString
      );
      $providerButtons[] = '<a class="btn btn-outline w-full login-provider-'
          . e($a['name']) . '" href="' . $providerUrl . '">'
          . e(Lang::txt('COM_USERS_LOGIN_SIGN_IN_WITH_METHOD', $a['display']))
          . '</a>';
  }

  $usersConfig   = Component::params('com_members');
  $allowRegister = $usersConfig->get('allowUserRegistration') != '0';
  $errorText     = Request::getString('errorText', '');
  $formAction    = Route::url('index.php', true, true);
  $remindUrl     = Route::url('index.php?option=com_members&task=remind');
  $resetUrl      = Route::url('index.php?option=com_members&task=reset');
  $registerUrl   = Request::base(true) . '/register'
                 . ($return ? '?return=' . $return : '');
  $siteName      = Config::get('sitename');
@endphp

@if($params->get('show_page_heading', 1))
  <header class="page-header">
    <h1>{{ e($params->get('page_heading', Lang::txt('COM_USERS_LOGIN'))) }}</h1>
  </header>
@endif

<section class="py-8">
  <div class="login-wrapper">

    {{-- Error message from failed login attempt --}}
    @if($errorText)
      <div class="alert alert-error mb-6" role="alert">
        {{ e($errorText) }}
      </div>
    @endif

    {{-- Description from menu params --}}
    @if($description)
      <p class="text-center text-base-content/70 mb-6">
        {{ e($description) }}
      </p>
    @endif

    <div class="login-card card bg-base-100 shadow-sm">
      <div class="card-body">

        <div class="login-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
        </div>
        <h2 class="login-heading">
          {{ Lang::txt('COM_USERS_LOGIN_TO', $siteName) }}
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
              <span>{{ Lang::txt('COM_USERS_OR', 'or') }}</span>
            </div>
          @endif
        @endif

        {{-- Local username/password form --}}
        @if($local)
          <form method="post"
                action="{{ $formAction }}"
                class="login-form">

            <div class="form-control mb-4">
              <label class="label" for="field-username">
                <span class="label-text">
                  {{ Lang::txt('COM_USERS_LOGIN_USERNAME') }}
                </span>
              </label>
              <input type="text"
                     id="field-username"
                     name="username"
                     class="input input-bordered w-full"
                     autocomplete="username"
                     required />
            </div>

            <div class="form-control mb-4">
              <label class="label" for="field-password">
                <span class="label-text">
                  {{ Lang::txt('COM_USERS_LOGIN_PASSWORD') }}
                </span>
              </label>
              <input type="password"
                     id="field-password"
                     name="passwd"
                     class="input input-bordered w-full"
                     autocomplete="current-password"
                     required />
            </div>

            @if(Plugin::isEnabled('system', 'remember'))
              <div class="form-control mb-4">
                <label class="label cursor-pointer justify-start gap-3">
                  <input type="checkbox"
                         class="checkbox"
                         name="remember"
                         id="field-remember"
                         value="yes"
                         @if($remember_me_default) checked @endif />
                  <span class="label-text">
                    {{ Lang::txt('COM_USERS_LOGIN_KEEP_LOGGED_IN') }}
                  </span>
                </label>
              </div>
            @endif

            <button type="submit"
                    class="btn btn-primary w-full">
              {{ Lang::txt('COM_USERS_LOGIN') }}
            </button>

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="authenticator" value="hubzero" />
            <input type="hidden" name="task" value="login" />
            <input type="hidden" name="return" value="{{ e($return) }}" />
            <input type="hidden" name="freturn" value="{{ e($freturn) }}" />
            {!! Html::input('token') !!}
          </form>
        @endif

        {{-- Forgot links --}}
        <div class="login-footer">
          <a class="link link-hover" href="{{ $remindUrl }}">
            {{ Lang::txt('COM_USERS_LOGIN_REMIND') }}
          </a>
          <a class="link link-hover" href="{{ $resetUrl }}">
            {{ Lang::txt('COM_USERS_LOGIN_RESET') }}
          </a>
        </div>

      </div>
    </div>

    {{-- Create account --}}
    @if($allowRegister)
      <div class="login-register">
        <span class="login-register-text">
          {{ Lang::txt('COM_USERS_LOGIN_REGISTER') }}
        </span>
        <a class="btn btn-outline btn-sm" href="{{ $registerUrl }}">
          {{ Lang::txt('COM_USERS_LOGIN_CREATE_ACCOUNT') }}
        </a>
      </div>
    @endif

    {{-- No auth available --}}
    @if(!$local && count($providerButtons) === 0)
      <div class="alert alert-warning" role="alert">
        {{ Lang::txt('COM_USERS_LOGIN_UNAVAILABLE') }}
      </div>
    @endif

  </div>
</section>
