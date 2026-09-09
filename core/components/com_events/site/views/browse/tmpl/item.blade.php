{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
// Resolve timezone from the event or fall back to config offset
if (!isset($row->time_zone) || $row->time_zone == '') {
    $timezone = \Hubzero\Facades\Config::get('offset');

    // Handle daylight savings time
    if (date('I', strtotime($row->publish_up))) {
        $publish_up = strtotime($row->publish_up . '+ 1 hour');
    }
    if (date('I', strtotime($row->publish_down))) {
        $publish_down = strtotime($row->publish_down . '+ 1 hour');
    }
} else {
    $timezone = timezone_name_from_abbr(
        '',
        intval($row->time_zone) * 3600,
        -1
    );
}

// If no timezone found, try to match by offset
if ($timezone === false) {
    $timezone = null;
    $offset = intval($row->time_zone) * 3600;
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr) {
        foreach ($abbr as $city) {
            if ($city['offset'] == $offset) {
                $timezone = $city['timezone_id'];
            }
        }
    }
}

// Clean content and parse custom fields
$row->content = stripslashes($row->content);
$row->content = str_replace('<br />', '', $row->content);

if (!empty($fields)) {
    for ($i = 0, $n = count($fields); $i < $n; $i++) {
        // Extract field value from content
        array_push(
            $fields[$i],
            \Components\Events\Site\Controllers\Events::parseTag(
                $row->content,
                $fields[$i][0]
            )
        );
        // Remove the parsed tag from content
        $row->content = str_replace(
            '<ef:' . $fields[$i][0] . '>' . end($fields[$i]) . '</ef:' . $fields[$i][0] . '>',
            '',
            $row->content
        );
    }
    $row->content = trim($row->content);
}

// Format dates
$startDate = \Hubzero\Facades\Date::of($row->publish_up, $timezone)->toLocal();
$stopDate = \Hubzero\Facades\Date::of($row->publish_down, $timezone)->toLocal();
$currentDate = \Hubzero\Facades\Date::of()->toLocal();

$isPast = (strtotime($stopDate) - strtotime($currentDate) < 0);

$upDate = \Hubzero\Facades\Date::of($row->publish_up, $timezone);
$downDate = \Hubzero\Facades\Date::of($row->publish_down, $timezone);

$detailUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=details&id=' . $row->id
);
$titleEsc = e(stripslashes($row->title));

// Build custom field info
$customFields = [];
if (!empty($fields)) {
    foreach ($fields as $field) {
        if ($field[4] == 1 && end($field) != '') {
            $customFields[] = ['label' => $field[1], 'value' => end($field)];
        }
    }
}
@endphp

<li class="py-4 border-b border-base-300 last:border-b-0 @if($isPast) opacity-50 @endif"
    id="event{{ $row->id }}">
    <div class="flex gap-6">
        {{-- Left column: date & time --}}
        <div class="shrink-0 w-32 text-right text-sm">
            @if ($showdate)
                <div class="font-semibold text-base-content">
                    {{ $upDate->toLocal('d M Y') }}
                </div>
            @endif
            <div class="text-base-content/60">
                {{ $upDate->format('g:i A T', true) }}
            </div>
            <div class="text-base-content/60">
                {{ $downDate->format('g:i A T', true) }}
            </div>
        </div>

        {{-- Right column: content --}}
        <div class="flex flex-col gap-1 min-w-0">
            {{-- Title link --}}
            <p class="font-semibold">
                <a href="{{ $detailUrl }}" class="link link-primary link-hover">
                    {{ $titleEsc }}
                </a>
            </p>

            {{-- Category --}}
            @if (isset($categories[$row->catid]))
                <p class="text-sm">
                    <strong>{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY') }}:</strong>
                    {{ stripslashes($categories[$row->catid]) }}
                </p>
            @endif

            {{-- Description --}}
            @if (trim(strip_tags($row->content)))
                <p class="text-sm text-base-content/70">
                    <strong>{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}:</strong>
                    {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), 300) }}
                </p>
            @endif

            {{-- Custom fields --}}
            @foreach ($customFields as $cf)
                <p class="text-sm">
                    <strong>{{ e($cf['label']) }}:</strong> {{ e($cf['value']) }}
                </p>
            @endforeach
        </div>
    </div>
</li>
