{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$groupProjectPlugins = Event::trigger('groups.onGroupProjects', array($group));

$allUrl = Route::url(
    'index.php?option=com_groups&cn='
    . $group->get('cn')
    . '&active=projects&action=all'
);
$updatesUrl = Route::url(
    'index.php?option=com_groups&cn='
    . $group->get('cn')
    . '&active=projects&action=updates'
);
@endphp

<ul class="menu menu-horizontal bg-base-200 rounded-box mb-4">
    <li>
        <a class="{{ $tab == 'all' ? 'active' : '' }}"
            href="{{ $allUrl }}">
            {{ Lang::txt('PLG_GROUPS_PROJECTS_LIST') }}
            <span class="badge badge-sm">{{ $projectcount }}</span>
        </a>
    </li>
    <li>
        <a class="{{ $tab == 'updates' ? 'active' : '' }}"
            href="{{ $updatesUrl }}">
            {{ Lang::txt('PLG_GROUPS_PROJECTS_UPDATES_FEED') }}
            @if ($newcount)
                <span class="badge badge-sm badge-accent">{{ $newcount }}</span>
            @endif
        </a>
    </li>
    @foreach ($groupProjectPlugins as $plugin)
        <li>
            <a class="{{ $tab == $plugin->name ? 'active' : '' }}"
                href="{{ $plugin->pathRoute }}">
                {{ $plugin->title }}
                @if ($plugin->newcount)
                    <span class="badge badge-sm badge-accent">{{ $plugin->newcount }}</span>
                @endif
            </a>
        </li>
    @endforeach
</ul>
