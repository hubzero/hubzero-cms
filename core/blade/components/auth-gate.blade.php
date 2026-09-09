@props([
    'returnUrl' => '',
    'message' => '',
])

@php
    $encoded = $returnUrl ? base64_encode($returnUrl) : '';
    $loginUrl = \Hubzero\Facades\Route::url(
        'index.php?option=com_users&view=login'
        . ($encoded ? '&return=' . $encoded : ''),
        false
    );
    $loginLink = '<a href="' . e($loginUrl) . '">'
        . \Hubzero\Facades\Lang::txt('JLOGIN', 'Log in') . '</a>';
@endphp

<p class="login-to-comment">
    @if($message)
        {!! str_replace(':login', $loginLink, $message) !!}
    @else
        {!! \Hubzero\Facades\Lang::txt(
            'JGLOBAL_YOU_MUST_LOGIN',
            $loginLink
        ) !!}
    @endif
</p>
