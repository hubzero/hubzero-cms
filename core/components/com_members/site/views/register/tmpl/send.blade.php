{{--
 * Confirmation email sent page
 *
 * Variables:
 *   $title                - string  Page title
 *   $email                - string  User's email address
 *   $hubName              - string  Hub site name
 *   $option               - string  Component option
 *   $controller           - string  Controller name
 *   $return               - string  Return URL parameter
 *   $show_correction_faq  - bool    Whether to show email correction link
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;

    $__view->css('register')
           ->js('register');

    $changeUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=change&return=' . $return
    );

    $resendUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=resend&return=' . $return
    );
@endphp

<x-page-container :title="$title">
    @if($__view->getError())
        <div class="alert alert-error">{{ $__view->getError() }}</div>
    @else
        <div class="alert alert-success">
            A confirmation email has been sent to "{{ $email }}". You must click the link in that email to activate
            your account and resume using {{ $hubName }}.
        </div>

        @if($show_correction_faq)
            <h4>Wrong email address?</h4>
            <p>You can correct your email address by <a href="{{ $changeUrl }}">clicking here</a>.</p>
        @endif

        <h4>Never received or cannot find the confirmation email?</h4>
        <p>
            You can have a new confirmation email sent to "{{ $email }}" by
            <a href="{{ $resendUrl }}">clicking here</a>.
        </p>
    @endif
</x-page-container>
