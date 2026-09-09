{{--
  mod_billboards -- sliding billboard/banner with slides

  Variables: $collection, $pager, $slides

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="carousel w-full rounded-box" id="{{ $collection }}">
  @foreach ($slides as $i => $slide)
    @php
      $slideId = $collection . '-slide-' . $i;
      $prevId  = $collection . '-slide-' . ($i - 1);
      $nextId  = $collection . '-slide-' . ($i + 1);
      $isFirst = ($i === 0);
      $isLast  = ($loop->last);
    @endphp
    <div class="carousel-item relative w-full" id="{{ $slideId }}">
      <div class="w-full p-8" id="{{ $slide->alias }}">
        <h3 class="text-xl font-bold mb-2">{{ $slide->header }}</h3>
        <div class="prose">{!! $slide->text !!}</div>
        <div class="{{ $slide->learn_more_location }} mt-4">
          <a class="btn btn-sm btn-primary"
             href="{{ $slide->learn_more_target }}">
            {{ $slide->learn_more_text }}
          </a>
        </div>
      </div>
      <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
        @if (!$isFirst)
          <a href="#{{ $prevId }}" class="btn btn-circle btn-sm">&#10094;</a>
        @else
          <span></span>
        @endif
        @if (!$isLast)
          <a href="#{{ $nextId }}" class="btn btn-circle btn-sm">&#10095;</a>
        @else
          <span></span>
        @endif
      </div>
    </div>
  @endforeach
</div>
@if ($pager !== 'null')
  <div class="flex w-full justify-center gap-2 py-2" id="{{ $pager }}">
    @foreach ($slides as $i => $slide)
      <a href="#{{ $collection }}-slide-{{ $i }}"
         class="btn btn-xs">{{ $i + 1 }}</a>
    @endforeach
  </div>
@endif
