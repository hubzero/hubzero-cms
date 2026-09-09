{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;
@endphp

<nav>
    <ul class="menu menu-horizontal bg-base-200 rounded-box mb-4">
        <li>
            <a class="{{ $active == 'collections' ? 'active' : '' }}"
                href="{{ Route::url($base . '&scope=all') }}">
                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_COLLECTIONS', $collections) }}
            </a>
        </li>
        <li>
            <a class="{{ $active == 'posts' ? 'active' : '' }}"
                href="{{ Route::url($base . '&scope=posts') }}">
                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_POSTS', $posts) }}
            </a>
        </li>
        <li>
            <a class="{{ $active == 'followers' ? 'active' : '' }}"
                href="{{ Route::url($base . '&scope=followers') }}">
                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWERS', $followers) }}
            </a>
        </li>
        @if ($params->get('access-can-follow'))
            <li>
                <a class="{{ $active == 'following' ? 'active' : '' }}"
                    href="{{ Route::url($base . '&scope=following') }}">
                    {!! Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWING', '<strong>' . $following . '</strong>') !!}
                </a>
            </li>
        @endif
    </ul>
</nav>
