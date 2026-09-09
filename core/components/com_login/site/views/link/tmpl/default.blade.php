{{--
  Account linking wizard (3-step).

  Guides users through linking a third-party auth provider account
  to an existing hub account.

  Variables from controller (linkTask):
    $hzal         — Hubzero\Auth\Link instance
    $hzad         — Hubzero\Auth\Domain instance
    $plugins      — array: authentication plugins
    $display_name — string: auth provider display name
    $conflict     — array: email conflicts
    $sitename     — string: site name
    $user         — object: current user
    $option       — string: 'com_login'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $step = (int) Request::getInt('step', 1);

  $step2Url = Route::url(
      'index.php?option=' . $option . '&task=link&step=2', false
  );
  $step3Url = Route::url(
      'index.php?option=' . $option . '&task=link&step=3', false
  );
  $registerUrl = Route::url(
      'index.php?option=com_members&controller=register&task=update',
      false
  );

  // Step 3 OK button: log out, then redirect to re-auth with the
  // third-party provider so the accounts can be linked
  $linkReturnUrl = Route::url(
      'index.php?option=' . $option . '&reset=1&return='
      . base64_encode(
          Route::url(
              'index.php?option=' . $option
              . '&authenticator=' . $hzad->authenticator,
              false
          )
      ),
      false
  );
  $logoutLinkUrl = Route::url(
      'index.php?option=' . $option . '&task=logout&return='
      . base64_encode($linkReturnUrl),
      false
  );
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
                  d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m9.86-2.54a4.5 4.5 0 0 0-1.242-7.244l4.5-4.5a4.5 4.5 0 0 1 6.364 6.364l-1.757 1.757" />
          </svg>
        </div>
        <h2 class="login-heading">
          {{ Lang::txt('COM_LOGIN_LINK_ACCOUNT_SETUP') }}
        </h2>

        {{-- Step 1: Have you logged in before? --}}
        <div class="{{ $step === 1 ? '' : 'hidden' }}">
          <p class="text-center mb-6">
            {!! Lang::txt(
                'COM_LOGIN_LINK_LOGGED_IN_BEFORE',
                e($sitename)
            ) !!}
          </p>
          <div class="flex gap-3">
            <a class="btn btn-primary flex-1"
               href="{{ $step2Url }}">
              {{ Lang::txt('JYES') }}
            </a>
            <a class="btn btn-ghost flex-1"
               href="{{ $registerUrl }}">
              {{ Lang::txt('JNO') }}
            </a>
          </div>
        </div>

        {{-- Step 2: Link or create new? --}}
        <div class="{{ $step === 2 ? '' : 'hidden' }}">
          <p class="text-center mb-6">
            {!! Lang::txt(
                'COM_LOGIN_LINK_PROMPT',
                e($display_name)
            ) !!}
          </p>
          <div class="flex gap-3">
            <a class="btn btn-primary flex-1"
               href="{{ $step3Url }}">
              {{ Lang::txt('COM_LOGIN_LINK_BUTTON') }}
            </a>
            <a class="btn btn-ghost flex-1"
               href="{{ $registerUrl }}">
              {{ Lang::txt('COM_LOGIN_LINK_CREATE_NEW') }}
            </a>
          </div>
        </div>

        {{-- Step 3: Confirm and log out to re-auth --}}
        <div class="{{ $step === 3 ? '' : 'hidden' }}">
          <p class="text-center mb-6">
            {{ Lang::txt('COM_LOGIN_LINK_CONFIRM') }}
          </p>
          <div class="flex gap-3">
            <a class="btn btn-primary flex-1"
               href="{{ $logoutLinkUrl }}">
              {{ Lang::txt('COM_LOGIN_LINK_OK') }}
            </a>
            <a class="btn btn-ghost flex-1"
               href="{{ $step2Url }}">
              {{ Lang::txt('COM_LOGIN_LINK_GO_BACK') }}
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
