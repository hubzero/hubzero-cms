{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $projects = $rows;
    $setup_complete = $pconfig->get('confirm_step', 0) ? 3 : 2;
@endphp

<div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!} id="myprojects">
    @if ($params->get('button_show_all', 1) || $params->get('button_show_add', 1))
        <div class="flex gap-2 mb-3">
            @if ($params->get('button_show_all', 1))
                <a class="btn btn-sm btn-outline" href="{{ Route::url('index.php?option=com_projects&task=browse') }}">
                    {{ Lang::txt('MOD_MYPROJECTS_ALL_PROJECTS') }}
                </a>
            @endif
            @if ($params->get('button_show_add', 1))
                <a class="btn btn-sm btn-outline" href="{{ Route::url('index.php?option=com_projects&task=start') }}">
                    {{ Lang::txt('MOD_MYPROJECTS_NEW_PROJECT') }}
                </a>
            @endif
        </div>
    @endif

    @if ($projects && $total > 0)
        <ul class="list bg-base-100 rounded-box">
            @php $i = 0; @endphp
            @foreach ($projects as $row)
                @if ($i >= $limit)
                    @break
                @endif
                @php
                    $goto = 'alias=' . $row->alias;
                    $owned_by = Lang::txt('MOD_MYPROJECTS_BY') . ' ';
                    if ($row->owned_by_group) {
                        $owned_by .= \Hubzero\Utility\Str::truncate($row->groupname, 20);
                    } elseif ($row->created_by_user == User::get('id')) {
                        $owned_by .= Lang::txt('MOD_MYPROJECTS_ME');
                    } else {
                        $owned_by .= $row->authorname;
                    }
                    $role = $row->role == 1
                        ? Lang::txt('MOD_MYPROJECTS_STATUS_MANAGER')
                        : Lang::txt('MOD_MYPROJECTS_STATUS_COLLABORATOR');
                    $setup = ($row->setup_stage < $setup_complete) ? Lang::txt('MOD_MYPROJECTS_STATUS_SETUP') : '';
                    $viewUrl = Route::url('index.php?option=com_projects&task=view&' . $goto);
                    $thumbUrl = Route::url('index.php?option=com_projects&alias=' . $row->alias . '&controller=media&media=$thumb');
                    $i++;
                @endphp
                <li class="list-row">
                    <div class="list-col">
                        <a href="{{ $viewUrl }}">
                            <img src="{{ $thumbUrl }}"
                                alt="{{ $row->title }}"
                                class="w-8 h-8 rounded"
                            />
                        </a>
                    </div>
                    <div class="list-col grow">
                        <a href="{{ $viewUrl }}"
                            $title="{{ $row->title }} ({{ $row->alias }})"
                        >{{ \Hubzero\Utility\Str::truncate($row->title, 30) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ $owned_by }} | {{ $role }}
                            @if ($setup)
                                | {{ $setup }}
                            @elseif ($row->state == 0)
                                | {{ Lang::txt('MOD_MYPROJECTS_STATUS_SUSPENDED') }}
                            @endif
                        </span>
                    </div>
                    @if ($row->newactivity && $row->state == 1 && !$setup)
                        <div class="list-col">
                            <span class="badge badge-sm badge-primary">{{ $row->newactivity }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYPROJECTS_NO_PROJECTS') }}</em></p>
    @endif

    @if ($total > $limit)
        @php
            $moreUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=projects');
        @endphp
        <p class="text-sm mt-2">{!! Lang::txt('MOD_MYPROJECTS_YOU_HAVE_MORE', $limit, $total, $moreUrl) !!}</p>
    @endif
</div>
