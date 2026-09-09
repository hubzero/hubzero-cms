@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = $member->link() . '&active=' . $name;
@endphp

<nav aria-label="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_SUBMENU') }}">
    <ul class="collections-submenu">
        @if ($params->get('access-manage-collection'))
            <li>
                <a class="{{ $active == 'livefeed' ? 'active' : '' }}"
                    href="{{ Route::url($base) }}"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_TITLE') }}">
                    {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED') }}
                </a>
            </li>
        @endif
        <li>
            <a class="{{ $active == 'collections' ? 'active' : '' }}"
                href="{{ Route::url($base . '&task=all') }}">
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_COLLECTIONS', $collections) !!}
            </a>
        </li>
        <li>
            <a class="{{ $active == 'posts' ? 'active' : '' }}"
                href="{{ Route::url($base . '&task=posts') }}">
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_POSTS', $posts) !!}
            </a>
        </li>
        <li>
            <a class="{{ $active == 'followers' ? 'active' : '' }}"
                href="{{ Route::url($base . '&task=followers') }}">
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWERS', $followers) !!}
            </a>
        </li>
        <li>
            <a class="{{ $active == 'following' ? 'active' : '' }}"
                href="{{ Route::url($base . '&task=following') }}">
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWNG', $following) !!}
            </a>
        </li>
    </ul>
</nav>
