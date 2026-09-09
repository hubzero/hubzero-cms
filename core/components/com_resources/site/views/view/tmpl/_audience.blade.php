{{--
  Resource audience/skill level indicator.

  Variables:
    $audience     — Audience model instance
    $showtips     — bool, show explanation table
    $numlevels    — int, number of levels (typically 4)
    $audiencelink — string, URL to learn more about audience levels

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $levels   = [];
  $labels   = [];
  $selected = [];
  $txtlabel = '';
@endphp

@if($audience && !$audience->isNew())
  @php
    for ($i = 0; $i <= $numlevels; $i++) {
        $lv = 'level' . $i;
        $level = $audience->{$lv}();
        $levels[$lv] = $audience->get($lv);
        $labels[$lv] = [
            'title' => $level->title,
            'desc'  => $level->description,
        ];
        if ($audience->get($lv)) {
            $selected[] = $lv;
        }
    }

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
  @endphp

  <div class="mb-4">
    {{-- Level indicator dots --}}
    <div class="flex items-center gap-1 mb-1"
         role="img"
         aria-label="{{ Lang::txt('Difficulty Level') }}: {{ $txtlabel }}">
      @foreach($levels as $key => $value)
        <span class="w-3 h-3 rounded-full {{ $value ? 'bg-primary' : 'bg-base-300' }}"
              aria-hidden="true"></span>
      @endforeach
      <span class="text-sm ml-1">{{ $txtlabel }}</span>
    </div>

    {{-- Explanation table --}}
    @if($showtips)
      <details class="text-sm text-base-content/70 mt-2">
        <summary class="cursor-pointer">{{ Lang::txt('Difficulty Level') }}</summary>
        <div class="overflow-x-auto mt-2">
          <table class="table table-zebra table-sm">
            <thead>
              <tr>
                <th>{{ Lang::txt('Difficulty Level') }}</th>
                <th>{{ Lang::txt('Target Audience') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($labels as $key => $label)
                <tr>
                  <td>
                    <div class="flex items-center gap-1">
                      @foreach($labels as $ky => $val)
                        <span class="w-2.5 h-2.5 rounded-full {{ $ky == $key ? 'bg-primary' : 'bg-base-300' }}"
                              aria-hidden="true"></span>
                      @endforeach
                      <span class="ml-1">{{ $label['title'] }}</span>
                    </div>
                  </td>
                  <td>{{ $label['desc'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($audiencelink)
          <p class="mt-2">
            <a class="link link-hover text-primary" href="{{ $audiencelink }}">
              {{ Lang::txt('Learn more') }} &rsaquo;
            </a>
          </p>
        @endif
      </details>
    @endif
  </div>
@endif
