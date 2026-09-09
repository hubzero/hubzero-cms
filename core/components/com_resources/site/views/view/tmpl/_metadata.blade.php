{{--
  Resource metadata sidebar — ranking bar, audience levels, supported tag, plugin metadata.

  Variables:
    $option   — component option string
    $sections — array of plugin sections (each has 'area', 'metadata', 'html')
    $model    — Entry model

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Route;

  // Collect plugin metadata
  $data = '';
  foreach ($sections as $section) {
      if ($section['area'] == 'collect') {
          echo (isset($section['metadata'])) ? $section['metadata'] : '';
          continue;
      }
      $data .= (isset($section['metadata'])) ? $section['metadata'] : '';
  }

  $showRanking   = $model->params->get('show_ranking', 0);
  $showAudience  = $model->params->get('show_audience', 0);
  $supportedtag  = $model->params->get('supportedtag', 0);
  $hasContent    = $showRanking || $showAudience || $supportedtag || $data;
@endphp

@if($hasContent)
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">

      {{-- Ranking --}}
      @if($showRanking)
        @php
          $database = App::get('db');

          if ($model->isTool()) {
              $stats = new \Components\Resources\Helpers\Usage\Tools(
                  $database, $model->id, $model->type, $model->rating
              );
          } else {
              $stats = new \Components\Resources\Helpers\Usage\Andmore(
                  $database, $model->id, $model->type, $model->rating
              );
          }

          $rank = round($model->ranking, 1);
          $r = 10 * $rank;
          $reviewsUrl = Route::url(
              'index.php?option=' . $option . '&id=' . $model->id . '&active=reviews'
          );
        @endphp
        <div class="mb-4">
          <div class="flex items-center gap-2 mb-1">
            <div class="w-full bg-base-200 rounded-full h-2"
                 role="img"
                 aria-label="{{ number_format($rank, 1) }} Ranking">
              <div class="bg-primary h-2 rounded-full"
                   style="width: {{ $r }}%"></div>
            </div>
            <span class="text-sm font-semibold whitespace-nowrap">
              {{ number_format($rank, 1) }}
            </span>
          </div>
          <details class="text-sm text-base-content/70">
            <summary class="cursor-pointer">Ranking</summary>
            <p class="mt-1">
              Ranking is calculated from a formula comprised of
              <a class="link" href="{{ $reviewsUrl }}">user reviews</a>
              and usage statistics.
              <a class="link" href="about/ranking/">Learn more &rsaquo;</a>
            </p>
            <div class="mt-2">
              {!! $stats->display() !!}
            </div>
          </details>
        </div>
      @endif

      {{-- Audience levels --}}
      @if($showAudience)
        @php
          $audience = \Components\Resources\Models\Audience::all()
              ->whereEquals('rid', $model->id)
              ->row();
        @endphp
        @include('view::_audience', [
            'audience'     => $audience,
            'showtips'     => 1,
            'numlevels'    => 4,
            'audiencelink' => $model->params->get('audiencelink'),
        ])
      @endif

      {{-- Supported tag --}}
      @if($supportedtag)
        @php
          $rt = new \Components\Resources\Helpers\Tags($model->id);
          $isSupported = $rt->checkTagUsage($model->params->get('supportedtag'), $model->id);
        @endphp
        @if($isSupported)
          @php
            $tag = \Components\Tags\Models\Tag::oneByTag($model->params->get('supportedtag'));
            $defaultLink = Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'));
            $supportedLink = $model->params->get('supportedlink', $defaultLink);
            $tagLabel = e(stripslashes($tag->get('raw_tag')));
          @endphp
          <p class="mt-2">
            <span class="badge badge-success">
              <a class="link" href="{{ $supportedLink }}">{{ $tagLabel }}</a>
            </span>
          </p>
        @endif
      @endif

      {{-- Plugin metadata --}}
      @if($data)
        {!! $data !!}
      @endif

    </div>
  </div>
@endif
