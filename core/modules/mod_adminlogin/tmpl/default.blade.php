{{--
  Admin Login Module — card with dark header + stacked inputs

  Styled to match the original hzadmin login page:
  dark header bar ("ADMINISTRATION LOGIN" + hz watermark), plain stacked
  username/password inputs, full-width submit button.

  Variables (from mod_adminlogin.php via renderLayout):
    $return            — base64-encoded return URL
    $freturn           — base64-encoded factors return URL
    $returnQueryString — "&return=..." query string appended to OAuth URLs
    $authenticators    — array of OAuth plugins: [name => ['name', 'display']]
    $site_display      — site name for $local "Sign in with your X account" label
    $basic             — bool, true when the hubzero password plugin is enabled

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="card bg-base-100 shadow-lg rounded-box">

  {{-- Dark header: $title + hz watermark --}}
  <header class="login-card-header">
    <h1 class="text-xs font-semibold tracking-widest uppercase text-white m-0 leading-none">
      {{ Lang::txt('COM_LOGIN_ADMINISTRATION_LOGIN') }}
    </h1>
  </header>

  <div class="card-body gap-4 pt-4 pb-5 px-5">

    @if(count($authenticators) > 0)
      {{-- OAuth / SSO provider buttons --}}
      <p class="text-sm text-center text-muted-foreground mb-1">
        {{ Lang::txt('COM_LOGIN_CHOOSE_METHOD') }}
      </p>
      <div class="flex flex-col gap-2">
        @foreach($authenticators as $a)
          @php
            $authClass = 'Plugins\\Authentication\\' . ucfirst($a['name']) . '\\' . ucfirst($a['name']);
            $authUrl   = Route::url(
                'index.php?option=com_login&task=display&authenticator='
                . $a['name'] . $returnQueryString, false
            );
            $rendered  = null;
            if (class_exists($authClass)) {
                $refl = new \ReflectionClass($authClass);
                if ($refl->hasMethod('onRenderOption')) {
                    $html = $refl->getMethod('onRenderOption')->invoke(null, $returnQueryString);
                    $rendered = is_array($html) ? implode("\n", $html) : $html;
                }
            }
          @endphp
          @if($rendered)
            {!! $rendered !!}
          @else
            <a href="{{ $authUrl }}" class="btn btn-outline btn-sm w-full">
              {{ Lang::txt('COM_LOGIN_SIGN_IN_WITH_METHOD', $a['display']) }}
            </a>
          @endif
        @endforeach
      </div>

      @if($basic)
        <div class="divider text-xs my-1">
          {{ Lang::txt('COM_LOGIN_SIGN_IN_WITH_ACCOUNT', $site_display) }}
        </div>
      @endif
    @endif

    @if($basic || count($authenticators) === 0)
      {{-- Username / password form --}}
      <form action="{{ Route::url('index.php', true, true) }}"
            method="post">

        {{-- Stacked inputs with small gap; labels sr-only, placeholders visible --}}
        <div class="login-inputs">
          <label for="mod-login-username">
            <span class="sr-only">{{ Lang::txt('JGLOBAL_USERNAME') }}</span>
            <input type="text"
                   name="username"
                   id="mod-login-username"
                   autocomplete="username"
                   placeholder="{{ Lang::txt('JGLOBAL_USERNAME') }}"
                   class="input input-bordered w-full" />
          </label>
          <label for="mod-login-password">
            <span class="sr-only">{{ Lang::txt('JGLOBAL_PASSWORD') }}</span>
            <input type="password"
                   name="passwd"
                   id="mod-login-password"
                   autocomplete="current-password"
                   placeholder="{{ Lang::txt('JGLOBAL_PASSWORD') }}"
                   class="input input-bordered w-full" />
          </label>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-primary w-full">
            {{ Lang::txt('MOD_ADMINLOGIN_LOGIN') }}
          </button>
        </div>

        <input type="hidden" name="option"        value="com_login" />
        <input type="hidden" name="authenticator" value="hubzero" />
        <input type="hidden" name="task"          value="login" />
        <input type="hidden" name="return"        value="{{ $return }}" />
        <input type="hidden" name="freturn"       value="{{ $freturn }}" />
        {!! Html::input('token') !!}
      </form>
    @endif

  </div>
</div>
