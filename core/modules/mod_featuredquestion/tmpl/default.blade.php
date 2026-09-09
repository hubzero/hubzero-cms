{{--
  Featured Question module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ Lang::txt('MOD_FEATUREDQUESTION_MISSING_CLASS') }}</span>
  </div>
@elseif ($row)
  @php
    $name = Lang::txt('JANONYMOUS');
    if (!$row->get('anonymous')) {
        $name = $row->creator->get('name');
    }
    $rcount = $row->responses()->where('state', '<', 2)->count();
    $when = Date::of($row->get('created'))->relative();
    $questionUrl = Route::url($row->link());
  @endphp
  <div class="{{ $cls }}">
    <h3 class="font-semibold mb-2">{{ Lang::txt('MOD_FEATUREDQUESTION') }}</h3>
    <div class="flex items-start gap-3">
      @if (is_file(PATH_APP . $thumb))
        <a href="{{ $questionUrl }}" class="shrink-0">
          <img class="size-12 rounded object-cover"
               src="{{ $thumb }}" alt="" />
        </a>
      @endif
      <div class="min-w-0 grow">
        <p>
          <a href="{{ $questionUrl }}" class="link link-hover font-semibold">
            {{ strip_tags($row->subject) }}</a>@if ($row->get('question')):
            {{ \Hubzero\Utility\Str::truncate(strip_tags($row->question), $txt_length) }}
          @endif
        </p>
        <div class="flex flex-wrap items-center gap-x-2 text-xs text-base-content/60 mt-1">
          <span>{{ Lang::txt('MOD_FEATUREDQUESTION_ASKED_BY', $name) }}</span>
          <span>&mdash;</span>
          <span>{{ Lang::txt('MOD_FEATUREDQUESTION_AGO', $when) }}</span>
          <span>&mdash;</span>
          <span>
            {{ ($rcount == 1)
                ? Lang::txt('MOD_FEATUREDQUESTION_RESPONSE', $rcount)
                : Lang::txt('MOD_FEATUREDQUESTION_RESPONSES', $rcount) }}
          </span>
        </div>
      </div>
    </div>
  </div>
@endif
