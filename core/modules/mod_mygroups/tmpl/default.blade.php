{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
    @php
        $hasRecent = count($recentgroups) > 0;
    @endphp
    <div role="tablist" class="tabs tabs-border mb-3">
        <a role="tab" class="tab {{ $hasRecent ? 'tab-active' : '' }}"
            data-target="recentgroups{{ $module->id }}"
        >{{ Lang::txt('MOD_MYGROUPS_RECENT') }}</a>
        <a role="tab" class="tab {{ !$hasRecent ? 'tab-active' : '' }}"
            data-target="allgroups{{ $module->id }}"
        >{{ Lang::txt('MOD_MYGROUPS_ALL') }}</a>
    </div>

    <div id="recentgroups{{ $module->id }}" class="{{ $hasRecent ? '' : 'hidden' }}">
        @if ($hasRecent)
            <ul class="list bg-base-100 rounded-box">
                @foreach ($recentgroups as $group)
                    @if ($group->published)
                        @php $status = $__module->getStatus($group); @endphp
                        @include('modules.mod_mygroups.tmpl._item', ['group' => $group, 'status' => $status])
                    @endif
                @endforeach
            </ul>
        @else
            <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYGROUPS_NO_RECENT_GROUPS') }}</em></p>
        @endif
    </div>

    <div id="allgroups{{ $module->id }}" class="{{ !$hasRecent ? '' : 'hidden' }}">
        @php $total = count($allgroups); @endphp
        @if ($total > 0)
            <ul class="list bg-base-100 rounded-box">
                @php $i = 0; @endphp
                @foreach ($allgroups as $group)
                    @if ($group->published && $i < $limit)
                        @php
                            $status = $__module->getStatus($group);
                            $i++;
                        @endphp
                        @include('modules.mod_mygroups.tmpl._item', ['group' => $group, 'status' => $status])
                    @endif
                @endforeach
            </ul>
        @else
            <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYGROUPS_NO_GROUPS') }}</em></p>
        @endif

        @if ($total > $limit)
            @php
                $groupsUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=groups');
            @endphp
            <p class="text-sm mt-2">{!! Lang::txt('MOD_MYGROUPS_YOU_HAVE_MORE', $limit, $total, $groupsUrl) !!}</p>
        @endif
    </div>

    @if ($params->get('button_show_add', 1))
        <div class="flex gap-2 mb-3 mt-3">
            <a class="btn btn-sm btn-outline"
                href="{{ Route::url('index.php?option=com_groups&task=new') }}"
            >{{ Lang::txt('MOD_MYGROUPS_NEW_GROUP') }}</a>
        </div>
    @endif
</div>
