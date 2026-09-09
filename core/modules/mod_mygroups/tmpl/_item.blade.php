{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<li class="list-row">
    <div class="list-col grow">
        <a href="{{ Route::url('index.php?option=com_groups&cn=' . $group->cn) }}">
            {{ stripslashes($group->description) }}
        </a>
        <span class="badge badge-sm badge-ghost">{{ Lang::txt('MOD_MYGROUPS_STATUS_' . strtoupper($status)) }}</span>
        @if (!$group->approved)
            <span class="badge badge-sm badge-warning">{{ Lang::txt('MOD_MYGROUPS_GROUP_STATUS_PENDING') }}</span>
        @endif
        @if ($group->published == 2)
            <span class="badge badge-sm badge-neutral">{{ Lang::txt('MOD_MYGROUPS_GROUP_STATUS_ARCHIVED') }}</span>
        @endif
    </div>
    @if ($group->regconfirmed && !$group->registered)
        <div class="list-col">
            <div class="flex gap-1">
                <a class="btn btn-xs btn-success"
                    href="{{ Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=accept') }}"
                >{{ Lang::txt('MOD_MYGROUPS_ACTION_ACCEPT') }}</a>
                <a class="btn btn-xs btn-error"
                    href="{{ Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=cancel') }}"
                >{{ Lang::txt('MOD_MYGROUPS_ACTION_DECLINE') }}</a>
            </div>
        </div>
    @endif
</li>
