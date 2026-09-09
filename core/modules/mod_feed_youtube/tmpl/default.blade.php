{{--
  mod_feed_youtube -- YouTube $feed with thumbnails

  Variables: $feed, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
@endphp
<div class="youtubefeed{{ $params->get('moduleclass_sfx') }}">
@if ($feed)
  @php
    $iTitle = feed->image->title ?? null;
    $layout = $params->get('layout') ?: 'vertical';

    $youtube_ima = DS . trim($params->get('imagepath'), DS);
    if (!is_file(PATH_APP . $youtube_ima)) {
        $youtube_ima = '';
    }

    $morelink = $params->get('moreurl')
        ? str_replace('&', '&amp;', $params->get('moreurl'))
        : str_replace('&', '&amp;', feed->link);

    $actualItems = count($feed->items);
    $setItems    = $params->get('rssitems', 5);
    $totalItems  = min($setItems, $actualItems);

    $path  = DS . trim($params->get('webpath', '/site/youtube'), DS);
    $isDir = is_dir(PATH_APP . $path);
    if (!$isDir) {
        Filesystem::makeDirectory(PATH_APP . $path);
        $isDir = is_dir(PATH_APP . $path);
    }

    $words = $params->def('word_count', 0);
  @endphp

  @php
    $hasTitleOrParam = !is_null($feed->title) || $params->get('feedtitle', '');
  @endphp
  @if ($hasTitleOrParam && $params->get('rsstitle', 1))
    <h3 class="text-lg font-semibold">
      <a href="{{ $morelink }}" rel="external" class="link link-hover">
        {{ $params->get('feedtitle') ?: feed->title }}
      </a>
      @if ($params->get('rssimage', 1) && $youtube_ima)
        <a href="{{ str_replace('&', '&amp;', feed->link) }}" rel="external">
          <img src="{{ $youtube_ima }}" alt="{{ $iTitle }}" />
        </a>
      @endif
    </h3>
  @endif

  @php
    $hasFeedDesc = !is_null($feed->description) || $params->get('feeddesc', '');
  @endphp
  @if ($hasFeedDesc && $params->get('rssdesc', 0))
    <p>{{ $params->get('feeddesc') ?: feed->description }}</p>
  @endif

  <ul class="list bg-base-100 rounded-box shadow-sm {{ $layout === 'horizontal' ? 'flex flex-$row flex-wrap gap-4' : '' }}">
    @for ($j = 0; $j < $totalItems; $j++)
      @php
        $currItem = feed->items[$j];
        $vid = 0;
        if ($currItem->get_link()) {
            $match = [];
            preg_match("/youtube\.com\/watch\?v=(.*)/", $currItem->get_link(), $match);
            if (count($match) > 1 && strlen($match[1]) > 11) {
                $vid = substr($match[1], 0, 11);
            }

            $thumb = '';
            if ($vid && $isDir) {
                $img_src = 'http://img.youtube.com/vi/' . $vid . '/default.jpg';
                $thumb   = $path . DS . $vid . '.jpg';
                if (!is_file(PATH_APP . $thumb)) {
                    @copy($img_src, PATH_APP . $thumb);
                }
                if (!is_file(PATH_APP . $thumb)) {
                    $vid = 0;
                }
            }
        }
      @endphp
      <li class="list-row items-center gap-3">
        @if ($currItem->get_link())
          @if ($vid)
            <a href="{{ $currItem->get_link() }}" rel="external">
              <img src="{{ $thumb }}" alt=""
                   class="rounded w-20 h-auto" />
            </a>
          @endif
          <div>
            <a href="{{ $currItem->get_link() }}" rel="external"
               class="link link-hover font-medium">
              {{ $currItem->get_title() }}
            </a>
          </div>
        @endif
      </li>
    @endfor
  </ul>

  @if ($params->get('moreurl') && $params->get('showmorelink', 0))
    <p class="mt-3">
      <a href="{{ $morelink }}" rel="external"
         class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_FEED_YOUTUBE_MORE') }}
      </a>
    </p>
  @endif
@endif
</div>
