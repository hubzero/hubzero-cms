{{--
  Featured Resource module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ Lang::txt('MOD_FEATUREDRESOURCE_MISSING_CLASS') }}</span>
  </div>
@elseif ($row)
  @php
    $resourceUrl = Route::url('index.php?option=com_resources&id=' . $id);
  @endphp
  <div class="flex items-start gap-3 {{ $cls }}">
    @if ($thumb && is_file(PATH_APP . $thumb))
      <a href="{{ $resourceUrl }}" class="shrink-0">
        <img class="size-12 rounded object-cover"
             src="{{ $thumb }}" alt="" />
      </a>
    @endif
    <p>
      <a href="{{ $resourceUrl }}" class="link link-hover font-semibold">
        {{ stripslashes($row->title) }}</a>@if ($row->introtext):
        {{ \Hubzero\Utility\Str::truncate(strip_tags($row->introtext), $txt_length) }}
      @endif
    </p>
  </div>
@else
  <div class="{{ $cls }}">
    <p class="text-base-content/60">{{ Lang::txt('MOD_FEATUREDRESOURCE_NO_RESULTS') }}</p>
  </div>
@endif
