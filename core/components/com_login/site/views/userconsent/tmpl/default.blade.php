{{--
  User consent agreement form.

  Displays the system usage monitoring consent notice and
  Agree / Cancel buttons.

  Variables from controller (userconsentTask):
    $option — string: 'com_login'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $consentUrl = Route::url(
      'index.php?option=' . $option . '&task=consent', false
  );
  $returnVal = base64_encode(Request::current(true));
@endphp

<section class="py-8">
  <div class="login-wrapper">
    <div class="login-card card bg-base-100 shadow-sm">
      <div class="card-body">

        <div class="login-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
        </div>
        <h2 class="login-heading">
          {{ Lang::txt('COM_LOGIN_USERCONSENT') }}
        </h2>

        <p class="text-sm text-base-content/70 mb-6">
          {{ Lang::txt('COM_LOGIN_USERCONSENT_MESSAGE') }}
        </p>

        <form method="post" action="{{ $consentUrl }}">
          <input type="hidden" name="return"
                 value="{{ $returnVal }}" />
          {!! Html::input('token') !!}
          <div class="flex gap-3">
            <a class="btn btn-ghost flex-1" href="/">
              {{ Lang::txt('COM_LOGIN_USERCONSENT_CANCEL') }}
            </a>
            <button class="btn btn-primary flex-1" type="submit">
              {{ Lang::txt('COM_LOGIN_USERCONSENT_AGREE') }}
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</section>
