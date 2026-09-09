{{--
  Popular Questions module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div{!! $cssId ? ' id="' . $cssId . '"' : '' !!}{!! $cssClass ? ' class="' . $cssClass . '"' : '' !!}>
  @if (count($rows) > 0)
    <ul class="list bg-base-100 rounded-box">
      @foreach ($rows as $row)
        @php
          $name = Lang::txt('JANONYMOUS');
          if (!$row->get('anonymous')) {
              $name = $row->creator()->get('name');
          }
          $rcount = $row->responses()->where('state', '<', 2)->count();
        @endphp
        <li class="list-row p-3">
          @if ($style == 'compact')
            <a href="{{ Route::url($row->link()) }}" class="link link-hover">
              {{ strip_tags($row->subject) }}
            </a>
          @else
            <div class="min-w-0 grow">
              <h4 class="font-semibold">
                <a href="{{ Route::url($row->link()) }}" class="link link-hover">
                  {{ strip_tags($row->subject) }}
                </a>
              </h4>
              <div class="flex flex-wrap items-center gap-x-2 text-xs text-base-content/60 mt-0.5">
                <span>{{ Lang::txt('MOD_POPULARQUESTIONS_ASKED_BY', e($name)) }}</span>
                <time datetime="{{ $row->created() }}">{{ $row->created('time') }}</time>
                <time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time>
                <span>&bull;</span>
                <a href="{{ Route::url($row->link() . '#answers') }}"
                   class="link link-hover"
                   $title="{{ Lang::txt('MOD_RECENTQUESTIONS_RESPONSES', $rcount) }}">
                  {{ $rcount }} {{ Lang::txt('MOD_RECENTQUESTIONS_RESPONSES', $rcount) }}
                </a>
              </div>
              <div class="flex flex-wrap gap-2 mt-1.5">
                {!! $row->tags('cloud') !!}
              </div>
            </div>
          @endif
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_POPULARQUESTIONS_NO_RESULTS') }}</p>
  @endif
</div>
