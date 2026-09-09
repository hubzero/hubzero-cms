{{--
  Registration — account update success page.

  Variables from controller:
    $title       — Page title (string)
    $self        — Whether the user updated their own account (bool)
    $updateEmail — Whether the email address was changed (bool)
    $xprofile    — User profile object
    $sitename    — Site name (string)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  $__view->css('register')
         ->js('register');
@endphp

<x-page-container :title="$title">

  @if (!empty($self))
    <div class="alert alert-success">Your account has been updated successfully.</div>

    @if ($updateEmail)
      <p>
        Thank you for updating your account. In order to continue to use this
        account you must verify your new email address.
      </p>

      @if ($__view->getError())
        <div class="alert alert-error">{!! $__view->getError() !!}</div>
      @else
        @php
          $email = $xprofile->get('email');
        @endphp
        <p>
          A confirmation email has been sent to {{ e($email) }}. You must click
          the link in that email to activate your account and begin using
          {{ e($sitename) }}.
        </p>
      @endif
    @endif
  @else
    <div class="alert alert-success">The account has been updated successfully.</div>

    @if ($updateEmail)
      <p>
        The user of this account has been notified of the change. In order to
        continue to use this account they will need to verify the new email
        address.
      </p>

      @if ($__view->getError())
        <div class="alert alert-error">{!! $__view->getError() !!}</div>
      @else
        @php
          $email = $xprofile->get('email');
        @endphp
        <p>
          A confirmation email has been sent to {{ e($email) }}. They must
          click the link in that email to activate the account and begin using
          {{ e($sitename) }}.
        </p>
      @endif
    @endif
  @endif

</x-page-container>
