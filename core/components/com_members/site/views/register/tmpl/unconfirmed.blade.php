{{--
 * Unconfirmed account warning with sidebar
 *
 * Variables:
 *   $title     - string  Page title
 *   $email     - string  User's email address
 *   $sitename  - string  Site name
 *   $return    - string  Return URL parameter
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;

    $__view->css('register')
           ->js('register');

    $resendUrl = Route::url(
        'index.php?option=com_members&controller=register&task=resend&return=' . $return
    );
@endphp

<x-page-container :title="$title">
    @if($__view->getError())
        <div class="alert alert-error">{{ $__view->getError() }}</div>
    @endif

    @slot('sidebar')
        <h4>Never received or cannot find the confirmation email?</h4>
        <p>
            You can have a new confirmation email sent to "{{ $email }}" by
            <a href="{{ $resendUrl }}">clicking here</a>.
        </p>
    @endslot

    <div class="alert alert-error">
        Your email address "{{ $email }}" has not been confirmed.
        Please check your email for a confirmation notice.
        You must click the link in that email to activate your
        account and resume using {{ $sitename }}.
    </div>
</x-page-container>
