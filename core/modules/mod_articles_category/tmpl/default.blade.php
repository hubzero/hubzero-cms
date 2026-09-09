{{--
  Articles Category module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<ul class="list bg-base-100 rounded-box{{ $moduleclass_sfx }}">
@if ($grouped)
  @foreach ($list as $group_name => $group)
    <li class="list-row">
      <div>
        <h{{ $item_heading }} class="font-semibold">{{ $group_name }}</h{{ $item_heading }}>
        <ul class="mt-1 space-y-1">
          @foreach ($group as $item)
            <li>
              <h{{ $item_heading + 1 }} class="text-sm">
                @if ($params->get('link_titles') == 1)
                  <a class="link link-hover {{ $item->active }}" href="/{{ $item->link }}">
                    {{ $item->title }}
                    @if ($item->displayHits)
                      <span class="badge badge-ghost badge-sm">{{ $item->displayHits }}</span>
                    @endif
                  </a>
                @else
                  {{ $item->title }}
                  @if ($item->displayHits)
                    <span class="badge badge-ghost badge-sm">{{ $item->displayHits }}</span>
                  @endif
                @endif
              </h{{ $item_heading + 1 }}>
              @if ($params->get('show_author'))
                <span class="text-xs text-base-content/60">{{ $item->displayAuthorName }}</span>
              @endif
              @if ($item->displayCategoryTitle)
                <span class="text-xs text-base-content/60">({{ $item->displayCategoryTitle }})</span>
              @endif
              @if ($item->displayDate)
                <span class="text-xs text-base-content/60">{{ $item->displayDate }}</span>
              @endif
              @if ($params->get('show_introtext'))
                <p class="text-sm mt-1">{{ $item->displayIntrotext }}</p>
              @endif
              @if ($params->get('show_readmore'))
                <p class="mt-1">
                  <a class="link link-primary text-sm" href="/{{ $item->link }}">
                    {{ Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE') }}
                  </a>
                </p>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    </li>
  @endforeach
@else
  @foreach ($list as $item)
    <li class="list-row">
      <div>
        <h{{ $item_heading }} class="font-semibold text-sm">
          @if ($params->get('link_titles') == 1)
            <a class="link link-hover {{ $item->active }}" href="/{{ $item->link }}">
              {{ $item->title }}
              @if ($item->displayHits)
                <span class="badge badge-ghost badge-sm">{{ $item->displayHits }}</span>
              @endif
            </a>
          @else
            {{ $item->title }}
            @if ($item->displayHits)
              <span class="badge badge-ghost badge-sm">{{ $item->displayHits }}</span>
            @endif
          @endif
        </h{{ $item_heading }}>
        @if ($params->get('show_author'))
          <span class="text-xs text-base-content/60">{{ $item->displayAuthorName }}</span>
        @endif
        @if ($item->displayCategoryTitle)
          <span class="text-xs text-base-content/60">({{ $item->displayCategoryTitle }})</span>
        @endif
        @if ($item->displayDate)
          <span class="text-xs text-base-content/60">{{ $item->displayDate }}</span>
        @endif
        @if ($params->get('show_introtext'))
          <p class="text-sm mt-1">{{ $item->displayIntrotext }}</p>
        @endif
        @if ($params->get('show_readmore'))
          <p class="mt-1">
            <a class="link link-primary text-sm" href="/{{ $item->link }}">
              {{ Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE') }}
            </a>
          </p>
        @endif
      </div>
    </li>
  @endforeach
@endif
</ul>
