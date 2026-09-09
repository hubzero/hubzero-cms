{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@if ($group->published == 1)
    @php
    $backUrl = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->cn
        . '&active=calendar&year=' . $year
        . '&month=' . $month
    );
    $addCalUrl = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->cn
        . '&active=calendar&action=addcalendar'
    );
    @endphp
    <ul id="page_options">
        <li>
            <a class="btn btn-ghost gap-2" href="{{ $backUrl }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                {{ Lang::txt('Back to Events Calendar') }}
            </a>
            <a class="btn btn-primary gap-2" href="{{ $addCalUrl }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ Lang::txt('Add Calendar') }}
            </a>
        </li>
    </ul>
@endif

<div class="overflow-x-auto">
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>{{ Lang::txt('Name') }}</th>
                <th>{{ Lang::txt('Color') }}</th>
                <th>{{ Lang::txt('Publish Events?') }}</th>
                <th>{{ Lang::txt('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($calendars) > 0)
                @foreach ($calendars as $cal)
                    @php
                    $colors = ['red','orange','yellow','green','blue','purple','brown'];
                    if (!in_array($cal->get('color'), $colors)) {
                        $cal->set('color', '');
                    }
                    $imgBase = Request::base(true)
                        . '/core/plugins/groups/calendar/assets/img/swatch-';

                    $calId = $cal->get('id');
                    $calBase = 'index.php?option=' . $option
                        . '&cn=' . $group->cn
                        . '&active=calendar';
                    $editUrl = Route::url(
                        $calBase . '&action=editcalendar&calendar_id=' . $calId
                    );
                    $refreshUrl = Route::url(
                        $calBase . '&action=refreshcalendar&calendar_id=' . $calId
                    );
                    $deleteUrl = Route::url(
                        $calBase . '&action=deletecalendar&calendar_id=' . $calId
                    );
                    @endphp
                    <tr>
                        <td>{{ $cal->get('title') }}</td>
                        <td>
                            @if ($cal->get('color'))
                                <img src="{{ $imgBase }}{{ $cal->get('color') }}.png"
                                    alt="{{ $cal->get('color') }}"
                                    class="w-4 h-4 inline" />
                            @else
                                <img src="{{ $imgBase }}gray.png"
                                    alt="gray"
                                    class="w-4 h-4 inline" />
                            @endif
                        </td>
                        <td>
                            @if ($cal->get('published') == 1)
                                <span class="badge badge-success">{{ Lang::txt('Yes') }}</span>
                            @else
                                <span class="badge badge-error">{{ Lang::txt('No') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a class="btn btn-ghost btn-xs" href="{{ $editUrl }}">
                                    {{ Lang::txt('Edit') }}
                                </a>
                                <button type="button"
                                    class="btn btn-ghost btn-xs text-error"
                                    data-action="toggle-delete"
                                    data-target="delete-confirm-{{ $calId }}">
                                    {{ Lang::txt('Delete') }}
                                </button>
                                @if ($cal->get('url'))
                                    <a class="btn btn-ghost btn-xs" href="{{ $refreshUrl }}">
                                        {{ Lang::txt('Refresh') }}
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden" id="delete-confirm-{{ $calId }}">
                        <td colspan="4">
                            <form action="{{ $deleteUrl }}" method="post" class="flex items-center gap-4 p-2 bg-base-200 rounded-lg">
                                <div>
                                    <h4 class="font-semibold">{{ Lang::txt('Delete Calendar') }}</h4>
                                    <p class="text-sm">{{ Lang::txt('What do you want to do with the events associated with this calendar?') }}</p>
                                </div>
                                <select name="events" class="select select-bordered select-sm">
                                    <option value="keep">{{ Lang::txt('Delete Calendar & Set Events as Uncategorized') }}</option>
                                    <option value="delete">{{ Lang::txt('Delete Calendar & Delete Events') }}</option>
                                </select>
                                <button type="submit" class="btn btn-error btn-sm">{{ Lang::txt('Delete') }}</button>
                                <button type="button"
                                    class="btn btn-ghost btn-sm"
                                    data-action="toggle-delete"
                                    data-target="delete-confirm-{{ $calId }}">
                                    {{ Lang::txt('Cancel') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @if ($cal->get('url'))
                        <tr>
                            <td colspan="4" class="text-sm text-base-content/60">
                                <span class="font-medium">{{ Lang::txt('Calendar URL:') }}</span>
                                {{ $cal->get('url') }}
                                <br />
                                <span class="font-medium">{{ Lang::txt('Last Fetched:') }}</span>
                                @if (!$cal->get('last_fetched') || $cal->get('last_fetched') == '0000-00-00 00:00:00')
                                    {{ Lang::txt('Never') }}
                                @else
                                    {{ Date::of($cal->get('last_fetched'))->toLocal('m/d/Y @ g:ia') }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center text-base-content/60">
                        {{ Lang::txt('Currently there are no calendars for this group.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
