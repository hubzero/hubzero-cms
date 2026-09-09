{{--
  Quotes module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $base = filter_var(\Hubzero\Utility\Uri::getInstance()->toString(), FILTER_SANITIZE_URL);
@endphp

@if ($params->get('button', 0) == 1)
  <div class="flex justify-end mb-3">
    <a href="{{ Route::url('index.php?option=com_feedback&task=success_story') }}"
       class="btn btn-sm btn-outline">
      {{ Lang::txt('MOD_QUOTES_ADD_YOUR_STORY') }}
    </a>
  </div>
@endif

<div id="$quotes-$container">
  @if (count($quotes) > 0)
    <div class="flex flex-col gap-4"{!! $params->get('cycle', 0) == 1 ? ' id="shuffle"' : '' !!}>
      @foreach ($quotes as $quote)
        @php
          $fullQuote = e(stripslashes($quote->get('quote')));
          $fullQuote = preg_replace("/&lt;br ?\/&gt;|\n/", "<br/>", $fullQuote);
        @endphp
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body p-4">
            @if (isset($filters['id']) && $filters['id'] != '')
              <div class="breadcrumbs text-sm mb-2">
                @php
                  $quotesUrl = rtrim(str_replace('quoteid=' . $filters['id'], '', $base), '?');
                @endphp
                <ul>
                  <li><a href="{{ $quotesUrl }}">{{ Lang::txt('MOD_QUOTES_NOTABLE_QUOTES') }}</a></li>
                  <li>{{ stripslashes($quote->get('fullname')) }}</li>
                </ul>
              </div>
            @endif

            <blockquote class="border-l-4 border-primary pl-4 italic text-base-content/80"
                        cite="{{ stripslashes($quote->get('fullname')) }}">
              @if (isset($filters['id']) && $filters['id'] != '')
                {!! $fullQuote !!}
              @else
                <p>
                  @php
                    if (!trim($quote->get('short_quote'))) {
                        $quote->set('short_quote', \Hubzero\Utility\Str::truncate($quote->get('quote'), 250));
                    }
                    $quote->set('short_quote', html_entity_decode(stripslashes($quote->get('short_quote'))));
                    $quote->set('short_quote', strip_tags($quote->get('short_quote')));
                  @endphp
                  @if ($quote->get('short_quote') != $quote->get('quote'))
                    {{ rtrim($quote->get('short_quote'), '.') }} &#8230;
                    @php
                      $quoteUrl = $base . (strstr($base, '?') ? '&amp;' : '?')
                          . 'quoteid=' . $quote->get('id');
                      $quoteTitle = Lang::txt(
                          'MOD_QUOTES_VIEW_QUOTE_BY',
                          stripslashes($quote->get('fullname'))
                      );
                    @endphp
                    <a href="{{ $quoteUrl }}"
                       $title="{{ $quoteTitle }}"
                       class="link link-primary not-italic text-sm">
                      {{ Lang::txt('MOD_QUOTES_MORE') }}
                    </a>
                  @else
                    {{ $quote->get('short_quote') }}
                  @endif
                </p>
              @endif
            </blockquote>

            <div class="flex items-center gap-3 mt-3">
              @php
                $user = $quote->user;
                $userPicture = $user->picture();
              @endphp
              <img src="{{ $userPicture }}"
                   alt="{{ stripslashes($quote->get('fullname')) }}"
                   class="size-10 rounded-full object-cover" />
              <cite class="not-italic">
                <span class="font-semibold">{{ stripslashes($quote->get('fullname')) }}</span>
                <span class="block text-sm text-base-content/60">{{ stripslashes($quote->get('org')) }}</span>
              </cite>
            </div>

            @php
              $pictures = $quote->files();
            @endphp
            @if (count($pictures) > 0)
              <div class="flex flex-wrap gap-2 mt-3">
                @foreach ($pictures as $picture)
                  @php
                    list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());
                    $num = max($ow / 120, $oh / 120);
                    if ($num > 1) {
                        $mw = round($ow / $num);
                        $mh = round($oh / $num);
                    } else {
                        $mw = $ow;
                        $mh = $oh;
                    }
                    $img = substr($picture->getPathname(), strlen(PATH_ROOT));
                  @endphp
                  <a href="{{ $img }}">
                    <img src="{{ $img }}" height="{{ $mh }}" width="{{ $mw }}"
                         alt="" class="rounded" />
                  </a>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_QUOTES_NO_QUOTES_FOUND') }}</p>
  @endif
</div>
