{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="flex gap-2 mb-3">
    <a class="btn btn-sm btn-outline" href="{{ Route::url('index.php?option=com_resources&task=draft') }}">
        {{ Lang::txt('MOD_MYCONTRIBUTIONS_START_NEW') }}
    </a>
</div>

@if ($show_tools && $tools)
    <h4>
        <a href="{{ Route::url('index.php?option=com_tools&controller=pipeline&task=pipeline') }}">
            {{ Lang::txt('MOD_MYCONTRIBUTIONS_TOOLS') }}
            @if (count($tools) > $limit_tools)
                <span>{{ Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL') }} {{ count($tools) }}</span>
            @endif
        </a>
    </h4>

    <ul class="list bg-base-100 rounded-box">
        @for ($i = 0; $i < count($tools); $i++)
            @if ($i <= $limit_tools)
                @php
                    $class = $tools[$i]->published ? 'published' : 'draft';
                    $toolState = $__module->getState($tools[$i]->state);
                    $urgency = ($toolState == 'installed' || $toolState == 'created')
                        ? ' ' . Lang::txt('MOD_MYCONTRIBUTIONS_ACTION_REQUIRED')
                        : '';
                    $statusUrl = Route::url('index.php?option=com_tools&controller=pipeline&task=status&app=' . $tools[$i]->toolname);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $statusUrl }}">
                            {{ stripslashes($tools[$i]->toolname) }}
                        </a>
                        <span class="text-sm text-base-content/60">
                            {{ Lang::txt('MOD_MYCONTRIBUTIONS_STATUS') }}:
                            <a href="{{ $statusUrl }}"
                                $title="{{ Lang::txt('MOD_MYCONTRIBUTIONS_TOOL_STATUS', $toolState, $urgency) }}"
                            >{{ $toolState }}</a>
                        </span>
                        @if ($tools[$i]->published)
                            <span class="flex gap-2 text-sm">
                                @if ($show_questions)
                                    @php
                                        $qUrl = Route::url('index.php?option=com_resources&id=' . $tools[$i]->rid . '&active=answers');
                                        $qKey = 'MOD_MYCONTRIBUTIONS_NUM_QUESTION' . ($tools[$i]->q > 1 ? 'S' : '');
                                    @endphp
                                    <a href="{{ $qUrl }}"
                                        $title="{{ Lang::txt($qKey, $tools[$i]->q, $tools[$i]->q_new) }}"
                                    >{{ $tools[$i]->q }} Q</a>
                                @endif
                                @if ($show_wishes)
                                    @php
                                        $wUrl = Route::url('index.php?option=com_resources&id=' . $tools[$i]->rid . '&active=wishlist');
                                        $wKey = 'MOD_MYCONTRIBUTIONS_NUM_WISH' . ($tools[$i]->w > 1 ? 'S' : '');
                                    @endphp
                                    <a href="{{ $wUrl }}"
                                        $title="{{ Lang::txt($wKey, $tools[$i]->w, $tools[$i]->w_new) }}"
                                    >{{ $tools[$i]->w }} W</a>
                                @endif
                                @if ($show_tickets)
                                    @php
                                        $sUrl = Route::url('index.php?option=com_support&task=tickets&find=group:' . $tools[$i]->devgroup);
                                        $sKey = 'MOD_MYCONTRIBUTIONS_NUM_TICKET' . ($tools[$i]->s > 1 ? 'S' : '');
                                    @endphp
                                    <a href="{{ $sUrl }}"
                                        $title="{{ Lang::txt($sKey, $tools[$i]->s, $tools[$i]->s_new) }}"
                                    >{{ $tools[$i]->s }} S</a>
                                @endif
                            </span>
                        @endif
                    </div>
                </li>
            @endif
        @endfor
    </ul>

    @php
        $contribUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=contributions');
    @endphp
    <h4>
        <a href="{{ $contribUrl }}">
            {{ Lang::txt('MOD_MYCONTRIBUTIONS_OTHERS_IN_PROGRESS') }}
            @if ($contributions && count($contributions) > $limit_other)
                <span>{{ Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL') }}</span>
            @endif
        </a>
    </h4>
@endif

@if (!$contributions)
    <p class="text-base-content/60">{{ Lang::txt('MOD_MYCONTRIBUTIONS_NONE_FOUND') }}</p>
@else
    <ul class="list bg-base-100 rounded-box">
        @for ($i = 0; $i < count($contributions); $i++)
            @if ($i < $limit_other)
                @php
                    $class = match($contributions[$i]->published) {
                        1 => 'published',
                        2 => 'draft',
                        3 => 'pending',
                        default => '',
                    };
                    $author_login = Lang::txt('MOD_MYCONTRIBUTIONS_UNKNOWN');
                    $author = \Components\Members\Models\Member::oneOrNew($contributions[$i]->created_by);
                    if ($author->get('id')) {
                        $author_login = stripslashes($author->get('name'));
                        if (in_array($author->get('access'), User::getAuthorisedViewLevels())) {
                            $author_login = '<a href="' . Route::url($author->link()) . '">' . $author_login . '</a>';
                        }
                    }
                    $itemUrl = Route::url('index.php?option=com_resources&task=draft&step=1&id=' . $contributions[$i]->id);
                    $itemTitle = \Hubzero\Utility\Str::truncate(stripslashes($contributions[$i]->title), 40);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $itemUrl }}">{{ $itemTitle }}</a>
                        <span class="text-sm text-base-content/60">
                            {{ Lang::txt('MOD_MYCONTRIBUTIONS_TYPE') }}: {{ $contributions[$i]->typetitle }}<br>
                            {!! Lang::txt('MOD_MYCONTRIBUTIONS_SUBMITTED_BY', $author_login) !!}
                        </span>
                    </div>
                </li>
            @endif
        @endfor
    </ul>
@endif
