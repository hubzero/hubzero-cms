{{--
  Login module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $hash = App::hash(App::get('client')->name . ':authenticator');

  if (($cookie = \Hubzero\Utility\Cookie::eat('authenticator')) && !Request::getInt('reset', false)) {
      $primary = $cookie->authenticator;
      if (array_key_exists($primary, $authenticators) || (isset($local) && $local && $primary == 'hubzero')) {
          if (isset($cookie->user_id)) {
              $user = User::getInstance($cookie->user_id);
              $user_img = $cookie->user_img;
              Request::setVar('primary', $primary);
          }
      }
  }

  $usersConfig = Component::$params('com_members');
  $primary = Request::getWord('primary', false);

  $login_provider_html = '';
  $refl = [];
  foreach ($authenticators as $a) {
      $authClass = 'Plugins\\Authentication\\' . ucfirst($a['name']) . '\\' . ucfirst($a['name']);
      $refl[$a['name']] = new \ReflectionClass($authClass);
      if ($refl[$a['name']]->hasMethod('onRenderOption')) {
          $html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(null, $returnQueryString);
          $login_provider_html .= is_array($html) ? implode("\n", $html) : $html;
      } else {
          $authUrl = Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $returnQueryString);
          $login_provider_html .= '<a class="btn btn-outline btn-sm w-full gap-2 ' . $a['name'] . '" href="' . $authUrl . '">';
          $login_provider_html .= Lang::txt('MOD_LOGIN_SIGN_IN_WITH_METHOD', $a['display']);
          $login_provider_html .= '</a>';
      }
  }

  if ($primary != 'hubzero' && !isset($refl[$primary])) {
      $primary = null;
  }

  $current = $uri->toString();
  $current .= (strstr($current, '?') ? '&' : '?');
@endphp

<div class="hz_user">
  @if ($primary && $primary != 'hubzero')
    @php
      $primaryUrl = Route::url('index.php?option=com_users&view=login&authenticator=' . $primary . $returnQueryString);
    @endphp
    <a class="btn btn-primary btn-block mb-4" href="{{ $primaryUrl }}">
      @if (isset($user_img) && file_exists($user_img))
        <img src="{{ $user_img }}" alt="{{ Lang::txt('MOD_LOGIN_USER_PICTURE') }}"
             class="size-8 rounded-full" />
      @endif
      @php
        if (isset($refl[$primary]) && $refl[$primary]->hasMethod('onGetSubsequentLoginDescription')) {
            $desc = $refl[$primary]->getMethod('onGetSubsequentLoginDescription')->invoke(null, $returnQueryString);
        } else {
            $desc = Lang::txt('MOD_LOGIN_SIGN_IN_WITH_METHOD', ucfirst($primary));
        }
      @endphp
      {{ $desc }}
    </a>
  @else
    <div>
      @if (!$primary && count($authenticators) > 0)
        <div class="space-y-2 mb-4">
          <p class="text-sm text-base-content/60">{{ Lang::txt('MOD_LOGIN_CHOOSE_METHOD') }}</p>
          <div class="space-y-2">
            {!! $login_provider_html !!}
          </div>
          @php
            $siteName = isset($site_display) ? $site_display : Config::get('sitename');
          @endphp
          <div class="divider text-xs">{{ Lang::txt('MOD_LOGIN_OR', 'or') }}</div>
          <a href="{{ $current }}primary=hubzero&reset=1" class="btn btn-outline btn-sm w-full">
            {{ Lang::txt('MOD_LOGIN_SIGN_IN_WITH_ACCOUNT', $siteName) }}
          </a>
        </div>
      @endif

      @if ($primary == 'hubzero' || $login_provider_html == '')
        <p class="text-sm font-medium mb-3">{{ Lang::txt('MOD_LOGIN_TO', Config::get('sitename')) }}</p>
        <form action="{{ Route::url('index.php', true, true) }}" method="post" class="space-y-3">
          @if (isset($user) && is_object($user))
            <input type="hidden" name="username" value="{{ $user->get('username') }}" />
            <div class="font-medium">{{ $user->get('name') }}</div>
            <div class="text-sm text-base-content/60">{{ $user->get('email') }}</div>
          @else
            <label class="floating-label">
              <span>{{ Lang::txt('MOD_LOGIN_USERNAME') }}</span>
              <input type="text" name="username" id="username"
                     class="input input-bordered w-full"
                     placeholder="{{ Lang::txt('MOD_LOGIN_USERNAME') }}" tabindex="1" />
            </label>
          @endif

          <label class="floating-label">
            <span>{{ Lang::txt('MOD_LOGIN_PASSWORD') }}</span>
            <input type="password" name="passwd" id="password"
                   class="input input-bordered w-full"
                   placeholder="{{ Lang::txt('MOD_LOGIN_PASSWORD') }}"
                   autocomplete="off" tabindex="2" />
          </label>

          <div class="flex items-center justify-between gap-2">
            <button type="submit" class="btn btn-primary">{{ Lang::txt('Sign in') }}</button>
            @if (Plugin::isEnabled('system', 'remember'))
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" value="yes"
                       class="checkbox checkbox-sm"
                       {{ $remember_me_default ? 'checked' : '' }} />
                <span class="text-sm">{{ Lang::txt('MOD_LOGIN_KEEP_LOGGED_IN') }}</span>
              </label>
            @endif
          </div>

          <div class="flex gap-3 text-sm">
            @if (!isset($user))
              <a class="link link-hover"
                 href="{{ Route::url('index.php?option=com_members&task=remind') }}">
                {{ Lang::txt('MOD_LOGIN_REMIND') }}
              </a>
            @endif
            <a class="link link-hover"
               href="{{ Route::url('index.php?option=com_members&task=reset') }}">
              {{ Lang::txt('MOD_LOGIN_RESET') }}
            </a>
          </div>

          <input type="hidden" name="option" value="com_login" />
          <input type="hidden" name="authenticator" value="hubzero" />
          <input type="hidden" name="task" value="login" />
          <input type="hidden" name="return" value="{{ e($return) }}" />
          <input type="hidden" name="freturn" value="{{ e($freturn) }}" />
          {!! Html::input('token') !!}
        </form>
      @endif
    </div>
  @endif

  @if (isset($user) && is_object($user))
    <div class="mt-3 text-center">
      <a href="{{ Route::url($current . 'reset=1') }}" class="link link-hover text-sm">
        {{ Lang::txt('MOD_LOGIN_SIGN_IN_WITH_DIFFERENT_ACCOUNT') }}
      </a>
    </div>
  @elseif ($usersConfig->get('allowUserRegistration') != '0')
    <p class="mt-4 text-center">
      @php $regUrl = Request::base(true) . '/register' . ($return ? '?return=' . $return : ''); @endphp
      <a href="{{ $regUrl }}" class="btn btn-outline btn-sm">
        {{ Lang::txt('MOD_LOGIN_CREATE_ACCOUNT') }}
      </a>
    </p>
  @endif
</div>
