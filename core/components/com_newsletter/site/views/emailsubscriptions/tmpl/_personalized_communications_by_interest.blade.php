{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $userId = $__view->get('userId', 0);
    $hubname = Config::get('sitename');
@endphp

<ul class="list-disc list-inside text-sm text-base-content/70 mb-2">
    <li>Personalized updates based on your usage and impact on {{ $hubname }}</li>
    <li>Updates about resources you previously used</li>
    <li>Specific information based on your field and interests (please review your
        <a href="/members/{{ $userId }}/profile" class="link link-primary">profile</a>)</li>
</ul>
