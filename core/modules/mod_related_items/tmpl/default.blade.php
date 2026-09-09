{{--
  Related Items module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<ul class="list bg-base-100 rounded-box{{ $moduleclass_sfx }}">
  @foreach ($list as $item)
    <li class="list-row py-1">
      <a href="{{ $item->route }}" class="link link-hover">
        @if ($showDate)
          <time datetime="{{ $item->created }}" class="text-sm text-base-content/60">
            {{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')) }}
          </time>
          &ndash;
        @endif
        {{ $item->title }}
      </a>
    </li>
  @endforeach
</ul>
