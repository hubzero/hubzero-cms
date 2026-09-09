{{--
  Featured Member module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ Lang::txt('MOD_FEATUREDMEMBER_MISSING_CLASS') }}</span>
  </div>
@elseif ($row)
  @php
    $memberUrl  = Route::url($row->link());
    $memberName = $row->get('name', $row->get('givenName') . ' ' . $row->get('surname'));
  @endphp
  <div class="{{ $cls }}">
    @if ($filters['show'] == 'contributors')
      <h3 class="font-semibold mb-2">{{ Lang::txt('MOD_FEATUREDMEMBER_PROFILE') }}</h3>
    @else
      <h3 class="font-semibold mb-2">{{ Lang::txt('MOD_FEATUREDMEMBER') }}</h3>
    @endif
    <div class="flex items-start gap-3">
      @if (is_file(PATH_APP . $row->picture()))
        <a href="{{ $memberUrl }}" class="shrink-0">
          <img class="size-12 rounded-full object-cover"
               src="{{ $row->picture() }}"
               alt="{{ e(stripslashes($memberName)) }}" />
        </a>
      @endif
      <p>
        <a href="{{ $memberUrl }}" class="link link-hover font-semibold">
          {{ stripslashes($memberName) }}</a>@if ($txt = $row->get('bio')):
          {{ \Hubzero\Utility\Str::truncate(strip_tags($txt), $txt_length) }}
        @endif
      </p>
    </div>
  </div>
@endif
