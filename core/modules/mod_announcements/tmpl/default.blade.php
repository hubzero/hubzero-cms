{{--
  Announcements module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $morelink = count($content) > 0 ? $content[0]->catpath : 'announcements';
  $morelink = $params->get('show_viewall', '') ? $morelink : '';
  $subscribelink = $params->get('show_subscribe', '')
      && $params->get('subscribe_path', '')
      ? $params->get('subscribe_path', '')
      : '';
@endphp

@if ($morelink || $subscribelink)
  <div class="flex items-center gap-2 mb-3">
    @if ($morelink)
      <a href="{{ $morelink }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_ANNOUNCEMENTS_VIEW_ALL') }}
      </a>
    @endif
    @if ($subscribelink)
      <a href="{{ $subscribelink }}" class="btn btn-sm btn-outline">
        {{ $params->get('subscribe_label', Lang::txt('MOD_ANNOUNCEMENTS_SUBSCRIBE')) }}
      </a>
    @endif
  </div>
@endif

<div id="{{ $container }}">
  @if ($params->get('show_search', ''))
    <form action="{{ Route::url('index.php?option=com_search') }}" method="get" class="mb-3">
      <fieldset>
        <div class="join w-full">
          <input type="text" name="terms" value=""
                 class="input input-bordered join-item grow" />
          <input type="hidden" name="section" value="content:announcements" />
          <button type="submit" class="btn btn-primary join-item">
            {{ Lang::txt('MOD_ANNOUNCEMENTS_SEARCH') }}
          </button>
        </div>
      </fieldset>
    </form>
  @endif

  @if (count($content) > 0)
    <ul class="list bg-base-100 rounded-box">
      @foreach ($content as $item)
        @php
          $url = '/' . $item->catpath . '/' . $item->alias;

          // get associated image
          preg_match('/<img\s+.*?src="(.*?)"/is', $item->introtext, $match);
          $img = count($match) > 1
              ? trim(stripslashes($match[1]))
              : $params->get(
                  'default_image',
                  'modules/mod_announcements/assets/img/default.gif'
              );
        @endphp
        <li class="list-row items-start gap-3 p-3">
          @if ($params->get('show_image', ''))
            <div class="shrink-0">
              <img src="{{ $img }}"
                   alt="{{ stripslashes($item->title) }}"
                   class="size-16 rounded object-cover" />
            </div>
          @endif
          <div class="min-w-0 grow">
            <h4 class="font-semibold">
              <a href="{{ $url }}" class="link link-hover">
                {{ stripslashes($item->title) }}
              </a>
            </h4>
            @if ($params->get('show_date', ''))
              <div class="text-xs text-base-content/60 mt-0.5">
                {{ Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
              </div>
            @endif
            @if ($params->get('show_desc', ''))
              @php
                $desc = stripslashes($item->introtext);
                $desc = strip_tags($desc);
                $desc = str_replace("\n", '', $desc);
                $desc = str_replace("&nbsp;", '', $desc);
              @endphp
              <p class="text-sm mt-1">
                {{ \Hubzero\Utility\Str::truncate($desc, $params->get('word_count', 200)) }}
              </p>
            @endif
            @if ($params->get('show_morelink', ''))
              <a href="{{ $url }}" class="link link-primary text-sm mt-1 inline-block">
                {{ Lang::txt('MOD_ANNOUNCEMENTS_READ_MORE') }}
              </a>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_ANNOUNCEMENTS_NO_RESULTS') }}</p>
  @endif
</div>
