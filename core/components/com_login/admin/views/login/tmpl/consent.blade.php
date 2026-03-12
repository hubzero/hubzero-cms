{{--
  Admin Login — User consent/agreement gate

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  // Keep component CSS — consent page layout
  $__view->css('userconsent');
@endphp

<div class="flex min-h-[60vh] items-center justify-center p-6">
  <div class="card bg-base-100 border border-base-300 shadow-md w-full max-w-xl">
    <div class="card-body gap-4">

      <h2 class="card-title text-xl">
        {{ Lang::txt('COM_LOGIN_USERCONSENT') }}
      </h2>

      <p class="text-muted-foreground">
        {{ Lang::txt('COM_LOGIN_USERCONSENT_MESSAGE') }}
      </p>

      <form method="POST"
            action="{{ Route::url('index.php?option=com_login&task=grantconsent', false) }}">
        <input type="hidden"
               name="return"
               value="{{ base64_encode(Request::current(true)) }}" />

        <div class="card-actions justify-end mt-2">
          <a href="/" class="btn btn-ghost">
            {{ Lang::txt('JCANCEL') }}
          </a>
          <button type="submit" class="btn btn-primary">
            {{ Lang::txt('COM_LOGIN_USERCONSENT_AGREE') }}
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
