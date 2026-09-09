{{--
  mod_feed -- RSS $feed display

  Variables: $feed, $params, $rssrtl, $moduleclass_sfx

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($feed !== false)
  @php
    $iUrl   = feed->image->url ?? null;
    $iTitle = feed->image->title ?? null;
    $words  = $params->def('word_count', 0);

    $actualItems = count($feed->items);
    $setItems    = $params->get('rssitems', 5);
    $totalItems  = min($setItems, $actualItems);
  @endphp

  <div class="feed{{ $moduleclass_sfx }} feed-{{ $rssrtl ? 'rtl' : 'ltr' }}"
       @if ($rssrtl) dir="rtl" @endif>

    @if (!is_null($feed->title) && $params->get('rsstitle', 1))
      <h4>
        <a href="{{ str_replace('&', '&amp;', feed->link) }}" rel="nofollow external">
          {{ feed->title }}
        </a>
      </h4>
    @endif

    @if ($params->get('rssdesc', 1))
      <p>{!! feed->description !!}</p>
    @endif

    @if ($params->get('rssimage', 1) && $iUrl)
      <img src="{{ $iUrl }}" alt="{{ $iTitle }}" />
    @endif

    <ul class="list bg-base-100 rounded-box shadow-sm">
      @for ($j = 0; $j < $totalItems; $j++)
        @php
          $currItem = feed->items[$j];
          $text = '';
          if ($params->get('rssitemdesc', 1)) {
              $text = str_replace('&apos;', "'", $currItem->get_description());
              $text = strip_tags($text);
              if ($words) {
                  $texts = explode(' ', $text);
                  if (count($texts) > $words) {
                      $text = implode(' ', array_slice($texts, 0, $words)) . '...';
                  }
              }
          }
          $headingTag = (!is_null($feed->title) && $params->get('rsstitle', 1)) ? 'h5' : 'h4';
        @endphp
        <li class="list-row">
          <div>
            @if ($currItem->get_link())
              <{{ $headingTag }} class="font-medium">
                <a href="{{ $currItem->get_link() }}" rel="nofollow external"
                   class="link link-hover">
                  {{ $currItem->get_title() }}
                </a>
              </{{ $headingTag }}>
            @endif
            @if ($params->get('rssitemdesc', 1) && $text)
              <p class="text-sm opacity-70">{{ $text }}</p>
            @endif
          </div>
        </li>
      @endfor
    </ul>
  </div>
@endif
