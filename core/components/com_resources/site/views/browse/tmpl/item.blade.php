{{--
  Single resource item row — used by members/contributions plugin.

  Variables (set by Resources::out()):
    $line      — Components\Resources\Models\Entry
    $option    — 'com_resources'
    $config    — component params (Registry)
    $supported — array of supported resource IDs

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $params = $line->params;
  $extras = Event::trigger('resources.onResourcesList', [$line]);
  $isSupported = ($params->get('supportedtag') && isset($supported) && in_array($line->id, $supported));
@endphp

<li>
  {{-- Title --}}
  <p class="font-semibold">
    <a class="link link-hover text-primary" href="{{ Route::url($line->link()) }}">
      {{ e(stripslashes($line->title)) }}
    </a>
    @if ($isSupported)
      <span class="badge badge-success badge-sm ml-1">
        {{ Lang::txt('COM_RESOURCES_SUPPORTED') }}
      </span>
    @endif
  </p>

  {{-- Plugin extras --}}
  @if (!empty($extras))
    {!! implode("\n", $extras) !!}
  @endif

  {{-- Rating --}}
  @if ($params->get('show_ranking'))
    @php
      $ranking = round($line->get('ranking'), 1);
      $pct = 10 * $ranking;
    @endphp
    <div class="flex items-center gap-2 mt-1">
      <div class="w-24 bg-base-200 rounded-full h-2"
           role="img"
           aria-label="{{ Lang::txt('COM_RESOURCES_RANKING') }}: {{ number_format($ranking, 1) }}">
        <div class="bg-primary h-2 rounded-full" style="width: {{ $pct }}%"></div>
      </div>
      <span class="text-xs text-base-content/60">
        {{ number_format($ranking, 1) }} {{ Lang::txt('COM_RESOURCES_RANKING') }}
      </span>
    </div>
  @elseif ($params->get('show_rating'))
    <p class="text-xs text-base-content/60 mt-1"
       role="img"
       aria-label="{{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}">
      {{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}
    </p>
  @endif

  {{-- Details: date, authors --}}
  @php
    $info = [];
    if ($params->get('show_type')) {
        $info[] = '<strong>' . e(stripslashes($line->type->get('type'))) . '</strong>';
    }
    if ($thedate = $line->date) {
        $info[] = e($thedate);
    }
    if ($line->authors->count() && $params->get('show_authors')) {
        $authors = $line->authorsList();
        if (trim($authors)) {
            $info[] = Lang::txt('COM_RESOURCES_CONTRIBUTORS') . ': ' . $authors;
        }
    }
  @endphp
  @if (count($info))
    <p class="text-sm text-base-content/70">
      {!! implode(' | ', $info) !!}
    </p>
  @endif

  {{-- Description --}}
  @php
    $content = '';
    if ($line->get('introtext')) {
        $content = $line->get('introtext');
    } elseif ($line->get('fulltxt')) {
        $content = $line->get('fulltxt');
        $content = preg_replace('#<nb:(.*?)>(.*?)</nb:(.*?)>#s', '', $content);
        $content = trim($content);
    }
    $snippet = \Hubzero\Utility\Str::truncate(
        strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))),
        300
    );
  @endphp
  @if ($snippet)
    <p class="text-sm text-base-content/70 mt-1">{{ $snippet }}</p>
  @endif
</li>
