{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $url = $__view->get('url', '');
    $seconds = $__view->get('time', 10);
    $domain = $__view->get('domain', '');
@endphp

<x-page-container :title="Lang::txt('COM_REDIRECT_LEAVING_SITE')">
    <div class="max-w-lg mx-auto text-center">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-warning mb-2"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>

                <h2 class="card-title text-2xl">
                    {{ Lang::txt('COM_REDIRECT_REDIRECTING') }}
                </h2>

                <p class="text-base-content/70">
                    {{ Lang::txt('COM_REDIRECT_YOU_WILL_BE_REDIRECTED_TO') }}
                    <a href="{{ $url }}"
                       rel="noreferrer nofollow noopener"
                       class="link link-primary font-medium">{{ e($domain) }}</a>
                    {{ Lang::txt('COM_REDIRECT_IN') }}
                </p>

                <div class="text-4xl font-bold text-primary my-4"
                     data-redirect-countdown="{{ $seconds }}"
                     data-redirect-url="{{ $url }}">
                    {{ $seconds }}
                </div>

                <p class="text-sm text-base-content/50">
                    {{ Lang::txt('COM_REDIRECT_IF_NOT_REDIRECTED') }}
                    <a href="{{ $url }}"
                       rel="noreferrer nofollow noopener"
                       class="link link-primary">{{ Lang::txt('COM_REDIRECT_CLICK_HERE') }}</a>.
                </p>
            </div>
        </div>
    </div>
</x-page-container>
