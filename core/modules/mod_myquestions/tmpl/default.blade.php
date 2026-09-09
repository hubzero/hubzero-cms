{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($params->get('button_show_all', 1) || $params->get('button_show_add', 1))
    <div class="flex gap-2 mb-3">
        @if ($params->get('button_show_all', 1))
            <a class="btn btn-sm btn-outline" href="{{ Route::url('index.php?option=com_answers') }}">
                {{ Lang::txt('MOD_MYQUESTIONS_ALL_QUESTIONS') }}
            </a>
        @endif
        @if ($params->get('button_show_add', 1))
            <a class="btn btn-sm btn-outline" href="{{ Route::url('index.php?option=com_answers&task=new') }}">
                {{ Lang::txt('MOD_MYQUESTIONS_NEW_QUESTION') }}
            </a>
        @endif
    </div>
@endif

<h4>
    <a href="{{ Route::url('index.php?option=com_answers&task=search&$area=mine&filterby=open') }}">
        {{ Lang::txt('MOD_MYQUESTIONS_OPEN_QUESTIONS') }}
        <span class="text-sm">{{ Lang::txt('MOD_MYQUESTIONS_VIEW_ALL') }}</span>
    </a>
</h4>

@if ($openquestions)
    <ul class="list bg-base-100 rounded-box">
        @for ($i = 0; $i < count($openquestions); $i++)
            @if ($i < $limit_mine)
                @php
                    $rcount = $openquestions[$i]->get('rcount', 0);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ Route::url($openquestions[$i]->link()) }}">
                            {{ \Hubzero\Utility\Str::truncate(strip_tags($openquestions[$i]->subject), 60) }}
                        </a>
                        @if ($rcount > 0 && $banking)
                            <span class="text-xs text-base-content/60">
                                {{ Lang::txt('MOD_MYQUESTIONS_CLOSE_THIS_QUESTION') }}
                                {{ $openquestions[$i]->get('maxaward', 0) }}
                                {{ Lang::txt('MOD_MYQUESTIONS_POINTS') }}
                            </span>
                        @endif
                    </div>
                    <div class="list-col">
                        <span class="badge badge-sm {{ $rcount > 0 ? 'badge-success' : 'badge-ghost' }}">{{ $rcount }}</span>
                    </div>
                </li>
            @endif
        @endfor
    </ul>
@else
    <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYQUESTIONS_NO_QUESTIONS') }}</em></p>
@endif

@if ($show_assigned)
    <h4>
        <a href="{{ Route::url('index.php?option=com_answers&task=search&$area=assigned&filterby=open') }}">
            {{ Lang::txt('MOD_MYQUESTIONS_OPEN_QUESTIONS_ON_CONTRIBUTIONS') }}
            <span class="text-sm">{{ Lang::txt('MOD_MYQUESTIONS_VIEW_ALL') }}</span>
        </a>
    </h4>

    @if ($assigned)
        <p class="text-sm text-base-content/60"><span>{{ strtolower(Lang::txt('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')) }}</span></p>
        <ul class="list bg-base-100 rounded-box">
            @for ($i = 0; $i < count($assigned); $i++)
                @if ($i < $limit_assigned)
                    <li class="list-row">
                        <div class="list-col grow">
                            <a href="{{ Route::url($assigned[$i]->link()) }}">
                                {{ \Hubzero\Utility\Str::truncate(strip_tags($assigned[$i]->subject), 60) }}
                            </a>
                        </div>
                        @if ($banking)
                            <div class="list-col">
                                <span class="badge badge-sm badge-accent">
                                    {{ $assigned[$i]->get('maxaward', 0) }} {{ strtolower(Lang::txt('MOD_MYQUESTIONS_PTS')) }}
                                </span>
                            </div>
                        @endif
                    </li>
                @endif
            @endfor
        </ul>
    @else
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYQUESTIONS_NO_QUESTIONS') }}</em></p>
    @endif
@endif

@if ($show_interests)
    @php
        $interestUrl = Route::url('index.php?option=com_answers&task=search&$area=interest&filterby=open');
        $profileUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile#profile-interests');
    @endphp
    <h4>
        <a href="{{ $interestUrl }}">
            {{ Lang::txt('MOD_MYQUESTIONS_QUESTIONS_TO_ANSWER') }}
            <span class="text-sm">{{ Lang::txt('MOD_MYQUESTIONS_VIEW_ALL') }}</span>
        </a>
    </h4>
    <p class="text-sm text-base-content/60">
        <span>
            [<a href="{{ $profileUrl }}">
                @if ($interests)
                    {{ Lang::txt('JACTION_EDIT') }}
                @else
                    {{ Lang::txt('MOD_MYQUESTIONS_ADD_INTERESTS') }}
                @endif
            </a>]
        </span>
        <span>{{ Lang::txt('MOD_MYQUESTIONS_MY_INTERESTS') }}: {{ $intext }}</span>
    </p>

    @if ($otherquestions)
        <p class="text-sm text-base-content/60"><span>{{ strtolower(Lang::txt('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')) }}</span></p>
        <ul class="list bg-base-100 rounded-box">
            @for ($i = 0; $i < count($otherquestions); $i++)
                @if ($i < $limit_interest)
                    <li class="list-row">
                        <div class="list-col grow">
                            <a href="{{ Route::url($otherquestions[$i]->link()) }}">
                                {{ \Hubzero\Utility\Str::truncate(strip_tags($otherquestions[$i]->subject), 60) }}
                            </a>
                        </div>
                        @if ($banking)
                            <div class="list-col">
                                <span class="badge badge-sm badge-accent">
                                    {{ $otherquestions[$i]->get('maxaward', 0) }} {{ strtolower(Lang::txt('MOD_MYQUESTIONS_PTS')) }}
                                </span>
                            </div>
                        @endif
                    </li>
                @endif
            @endfor
        </ul>
    @else
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYQUESTIONS_NO_QUESTIONS') }}</em></p>
    @endif
@endif
