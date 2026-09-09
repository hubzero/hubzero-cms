{{--
  Modal/popup event view for AJAX requests
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div id="event" class="p-4">
    @if ($row)
        @php
        $editUrl = \Hubzero\Facades\Route::url(
            'index.php?option=' . $option . '&task=edit&id=' . $row->id
        );
        $deleteUrl = \Hubzero\Facades\Route::url(
            'index.php?option=' . $option . '&task=delete&id=' . $row->id
        );
        $detailsUrl = \Hubzero\Facades\Route::url(
            'index.php?option=' . $option . '&task=details&id=' . $row->id . '&no_html=1'
        );
        @endphp

        <h2 class="text-xl font-bold mb-4">
            {{ e(stripslashes($row->title)) }}
            @if ($authorized || $row->created_by == \Hubzero\Facades\User::get('id'))
                <a href="{{ $editUrl }}"
                    class="btn btn-ghost btn-xs"
                    title="{{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}">
                    {{ strtolower(\Hubzero\Facades\Lang::txt('JACTION_EDIT')) }}
                </a>
                <a href="{{ $deleteUrl }}"
                    class="btn btn-ghost btn-xs text-error"
                    title="{{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}">
                    {{ strtolower(\Hubzero\Facades\Lang::txt('JACTION_DELETE')) }}
                </a>
            @endif
        </h2>

        {{-- Sub-tabs if pages or registration exist --}}
        @php
        $hasRegisterby = $row->registerby && $row->registerby != '0000-00-00 00:00:00';
        @endphp
        @if ($pages || $hasRegisterby)
            <div role="tablist" class="tabs tabs-border mb-4">
                <a role="tab"
                    class="tab @if($page->alias == '') tab-active @endif"
                    href="{{ $detailsUrl }}">
                    {{ \Hubzero\Facades\Lang::txt('EVENTS_OVERVIEW') }}
                </a>
                @if ($pages)
                    @foreach ($pages as $p)
                        @php
                        $pageUrl = \Hubzero\Facades\Route::url(
                            'index.php?option=' . $option . '&task=details&id=' . $row->id
                            . '&no_html=1&page=' . $p->alias
                        );
                        @endphp
                        <a role="tab"
                            class="tab @if($page->alias == $p->alias) tab-active @endif"
                            href="{{ $pageUrl }}">
                            {{ trim(stripslashes($p->title)) }}
                        </a>
                    @endforeach
                @endif
                @if ($hasRegisterby)
                    @php
                    $regUrl = \Hubzero\Facades\Route::url(
                        'index.php?option=' . $option . '&task=details&id=' . $row->id
                        . '&no_html=1&page=register'
                    );
                    @endphp
                    <a role="tab"
                        class="tab @if($page->alias == 'register') tab-active @endif"
                        href="{{ $regUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_REGISTER') }}
                    </a>
                @endif
            </div>
        @endif

        <div class="entry-details">
            @if ($page->alias != '')
                {{-- Custom page content --}}
                @if (trim($page->pagetext))
                    <div class="prose max-w-none">
                        {!! stripslashes($page->pagetext) !!}
                    </div>
                @else
                    <div class="alert">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_NO_INFO_AVAILABLE') }}
                    </div>
                @endif
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Left column: description, custom fields, tags --}}
                    <div>
                        <h3 class="text-base font-semibold mb-2">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}
                        </h3>
                        <p class="mb-4">
                            {!! stripslashes($row->content) !!}
                        </p>

                        @if ($fields)
                            @foreach ($fields as $field)
                                @if (end($field) != null)
                                    <h3 class="text-base font-semibold mb-1">
                                        {{ e(stripslashes($field[1])) }}
                                    </h3>
                                    <p class="mb-3">
                                        @if (end($field) == '1')
                                            {{ \Hubzero\Facades\Lang::txt('YES') }}
                                        @else
                                            {{ end($field) }}
                                        @endif
                                    </p>
                                @endif
                            @endforeach
                        @endif

                        @if ($tags)
                            <h3 class="text-base font-semibold mb-2">
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TAGS') }}
                            </h3>
                            <div class="mb-4">{!! $tags !!}</div>
                        @endif
                    </div>

                    {{-- Right column: category, when, address, extra info, contact, author --}}
                    <div>
                        {{-- Category --}}
                        <h3 class="text-base font-semibold mb-1">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY') }}
                        </h3>
                        <p class="mb-3">
                            {{ e(stripslashes($categories[$row->catid] ?? 'N/A')) }}
                        </p>

                        {{-- When --}}
                        <h3 class="text-base font-semibold mb-1">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_WHEN') }}
                        </h3>
                        <p class="mb-3">
                            @php
                            // Format start time to 12hr
                            $startTime = $row->start_time;
                            $ts = explode(':', $startTime);
                            if (intval($ts[0]) > 12) {
                                $ts[0] = ($ts[0] - 12);
                                $ts[0] = (substr($ts[0], 0, 1) == '0') ? substr($ts[0], 1) : $ts[0];
                                $startTime = implode(':', $ts) . ' <abbr title="Post Meridiem">pm</abbr>';
                            } else {
                                $startTime = (substr($startTime, 0, 1) == '0')
                                    ? substr($startTime, 1) : $startTime;
                                if (intval($ts[0]) == 12) {
                                    $startTime .= ' <small>' . \Hubzero\Facades\Lang::txt('EVENTS_NOON') . '</small>';
                                } else {
                                    $startTime .= ' <abbr title="Ante Meridiem">am</abbr>';
                                }
                            }

                            // Format stop time to 12hr
                            $stopTime = $row->stop_time;
                            $te = explode(':', $stopTime);
                            if (intval($te[0]) > 12) {
                                $te[0] = ($te[0] - 12);
                                $te[0] = (substr($te[0], 0, 1) == '0') ? substr($te[0], 1) : $te[0];
                                $stopTime = implode(':', $te) . ' <abbr title="Post Meridiem">pm</abbr>';
                            } else {
                                $stopTime = (substr($stopTime, 0, 1) == '0')
                                    ? substr($stopTime, 1) : $stopTime;
                                if (intval($te[0]) == 12) {
                                    $stopTime .= ' <small>' . \Hubzero\Facades\Lang::txt('EVENTS_NOON') . '</small>';
                                } else {
                                    $stopTime .= ' <abbr title="Ante Meridiem">am</abbr>';
                                }
                            }
                            @endphp

                            @if ($row->start_date == $row->stop_date)
                                {{ $row->start_date }},<br>
                                {!! $startTime !!} &ndash; {!! $stopTime !!}
                            @else
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_FROM') }}
                                {{ $row->start_date }} &ndash; {!! $startTime !!}<br>
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_TO') }}
                                {{ $row->stop_date }} &ndash; {!! $stopTime !!}
                            @endif
                        </p>

                        {{-- Address --}}
                        @if (trim($row->adresse_info))
                            <h3 class="text-base font-semibold mb-1">
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE') }}
                            </h3>
                            <p class="mb-3">{{ e(stripslashes($row->adresse_info)) }}</p>
                        @endif

                        {{-- Extra Info URL --}}
                        @if (trim($row->extra_info))
                            <h3 class="text-base font-semibold mb-1">
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA') }}
                            </h3>
                            <p class="mb-3">
                                <a href="{{ stripslashes($row->extra_info) }}"
                                    class="link link-primary">
                                    {{ e(stripslashes($row->extra_info)) }}
                                </a>
                            </p>
                        @endif

                        {{-- Contact --}}
                        @if (trim($row->contact_info))
                            <h3 class="text-base font-semibold mb-1">
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CONTACT') }}
                            </h3>
                            <p class="mb-3">{{ e(stripslashes($row->contact_info)) }}</p>
                        @endif

                        {{-- Author --}}
                        @if ($config->getCfg('byview') == 'YES')
                            @php
                            $user = \Hubzero\Facades\User::getInstance($row->created_by);
                            $authorName = is_object($user)
                                ? $user->get('name')
                                : \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_UNKNOWN');
                            @endphp
                            <h3 class="text-base font-semibold mb-1">
                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS') }}
                            </h3>
                            <p class="mb-3">{{ e(stripslashes($authorName)) }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-warning">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_NOEVENTSELECTED') }}
        </div>
    @endif
</div>
