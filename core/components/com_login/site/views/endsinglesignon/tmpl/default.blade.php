{{--
  Single sign-on logout prompt.

  Asks the user whether to end all sessions for the third-party
  auth provider, or leave them untouched.

  Variables from controller (endsinglesignonTask):
    $authenticator — string: provider name (e.g., 'pucas')
    $sitename      — string: site name
    $display_name  — string: provider display name
    $option        — string: 'com_login'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $logoutAllUrl = Route::url(
      'index.php?option=' . $option
      . '&task=logout&sso=all&authenticator=' . $authenticator,
      false
  );
  $logoutNoneUrl = Route::url(
      'index.php?option=' . $option
      . '&task=logout&sso=none&authenticator=' . $authenticator
      . '&return=' . Request::base(),
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
                  d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
          </svg>
        </div>
        <h2 class="login-heading">
          {!! Lang::txt(
              'COM_LOGIN_SSO_LOGOUT_QUESTION',
              e($display_name)
          ) !!}
        </h2>

        <p class="text-sm text-base-content/70 text-center mb-2">
          {!! Lang::txt(
              'COM_LOGIN_SSO_SESSION_ENDED',
              e($sitename)
          ) !!}
        </p>
        <p class="text-sm text-base-content/70 text-center mb-6">
          {!! Lang::txt(
              'COM_LOGIN_SSO_END_ALL_DESC',
              e($display_name)
          ) !!}
        </p>

        <div class="flex flex-col gap-3">
          <a class="btn btn-primary w-full"
             href="{{ $logoutAllUrl }}">
            {!! Lang::txt(
                'COM_LOGIN_SSO_END_ALL_BUTTON',
                e($display_name)
            ) !!}
          </a>
          <a class="btn btn-ghost w-full"
             href="{{ $logoutNoneUrl }}">
            {!! Lang::txt(
                'COM_LOGIN_SSO_LEAVE_UNTOUCHED',
                e($display_name)
            ) !!}
          </a>
        </div>

      </div>
    </div>
  </div>
</section>
