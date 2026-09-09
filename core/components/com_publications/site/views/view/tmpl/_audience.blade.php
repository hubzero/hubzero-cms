{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;

$levels   = [];
$labels   = [];
$selected = [];
$txtlabel = '';

$audienceData = !empty($audience[0]) ? $audience[0] : $audience;
$hideEmpty = $hideEmpty ?? true;

$levelColors = [
    'level0' => 'bg-green-400',
    'level1' => 'bg-yellow-400',
    'level2' => 'bg-orange-400',
    'level3' => 'bg-red-400',
    'level4' => 'bg-red-700',
];

if ($audienceData) {
    for ($i = 0, $n = $numlevels; $i <= $n; $i++) {
        $lb = 'label' . $i;
        $lv = 'level' . $i;
        $ds = 'desc' . $i;
        $levels[$lv] = $audienceData->$lv;
        $labels[$lv]['title'] = $audienceData->$lb;
        $labels[$lv]['desc']  = $audienceData->$ds;
        if ($audienceData->$lv) {
            $selected[] = $lv;
        }
    }

    if (empty($selected) && $hideEmpty) {
        $audienceData = null;
    }

    // Figure out text label
    if (count($selected) == 1) {
        $txtlabel = $labels[$selected[0]]['title'];
    } elseif (count($selected) > 1) {
        $first     = $labels[array_shift($selected)]['title'];
        $firstbits = explode('-', $first);
        $first     = array_shift($firstbits);

        $last     = $labels[end($selected)]['title'];
        $lastbits = explode('-', $last);
        $last     = end($lastbits);

        $txtlabel = $first . '-' . $last;
    } else {
        $txtlabel = Lang::txt('Tool Audience Unrated');
    }
}
@endphp

@if ($audienceData)
    <div class="mt-4">
        <div class="showscale">
            <ul class="flex gap-1 items-center list-none p-0 m-0"
                aria-label="Difficulty level: {{ $txtlabel }}">
                @foreach ($levels as $key => $value)
                    <li class="w-3 h-3 rounded-full {{ $levelColors[$key] ?? 'bg-gray-400' }}{{ !$value ? ' opacity-20' : '' }}"
                        title="{{ $labels[$key]['title'] }}">
                        <span class="sr-only">{{ $labels[$key]['title'] }}</span>
                    </li>
                @endforeach
                <li class="text-sm ml-2">{{ $txtlabel }}</li>
            </ul>
        </div>

        @if ($showtips)
            <details class="mt-2">
                <summary class="cursor-pointer text-sm">
                    {{ Lang::txt('Difficulty Level') }}
                </summary>
                <table class="table table-sm mt-2">
                    <thead>
                        <tr>
                            <td colspan="2">{{ Lang::txt('Difficulty Level') }}</td>
                            <td>{{ Lang::txt('Target Audience') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($labels as $key => $label)
                            <tr>
                                <th>
                                    <ul class="flex gap-1 items-center list-none p-0 m-0">
                                        @foreach ($labels as $ky => $val)
                                            <li class="w-3 h-3 rounded-full {{ $levelColors[$ky] ?? 'bg-gray-400' }}{{ $ky != $key ? ' opacity-20' : '' }}">
                                                <span class="sr-only">{{ $val['title'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </th>
                                <td>{{ $label['title'] }}</td>
                                <td>{{ $label['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($audiencelink)
                    <p>
                        <a href="{{ $audiencelink }}">
                            {{ Lang::txt('Learn more') }} &rsaquo;
                        </a>
                    </p>
                @endif
            </details>
        @endif
    </div>
@endif
