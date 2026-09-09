{{--
  Registration — email confirmation status page.

  Three states:
    1. Login mismatch warning (logged in as different user)
    2. Invalid/expired confirmation link error (with sidebar help)
    3. Success — email already confirmed

  Variables from controller:
    $title    — Page title (string)
    $email    — Email address being confirmed (string)
    $login    — Currently logged-in username (string)
    $redirect — Redirect URL (string)
    $sitename — Site name (string)
    $option   — Component option (string)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('register')
         ->js('register');
@endphp

<x-page-container :title="$title">

  @if ($__view->getError() && $__view->getError() == 'login mismatch')

    <div class="alert alert-warning">
      You are currently logged in as <strong>{{ e($login) }}</strong>.
      If you're trying to activate a different account,
      you may do so by <a href="{{ $redirect }}">confirming a different email address</a>.
    </div>

  @elseif ($__view->getError())

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-2">
        <div class="alert alert-error">
          <h4>{{ Lang::txt('Invalid Confirmation') }}</h4>
          <p>
            The email confirmation link you followed is no longer valid.
            Your email address "{{ e($email) }}" has not been confirmed.
          </p>
          @php
            $resendUrl = Route::url('index.php?option=' . $option . '&task=resend');
          @endphp
          <p>
            Please be sure to click the link from the latest confirmation
            email received. Earlier confirmation emails will be invalid.
            If you cannot locate a newer confirmation email, you may
            <a href="{{ $resendUrl }}">resend a new confirmation email</a>.
          </p>
        </div>
      </div>

      @slot('sidebar')
      <aside class="md:col-span-1">
        <h4>Never received or cannot find the confirmation email?</h4>
        @php
          $resendReturnUrl = Route::url(
              'index.php?option=' . $option . '&task=resend&return=' . $redirect
          );
        @endphp
        <p>
          You can have a new confirmation email sent to "{{ e($email) }}"
          by <a href="{{ $resendReturnUrl }}">clicking here</a>.
        </p>
      </aside>
      @endslot
    </div>

  @else

    <div class="alert alert-success">
      Your email address "{{ e($email) }}" has already been confirmed.
      You should be able to use {{ e($sitename) }} now. Thank you.
    </div>

  @endif

</x-page-container>
