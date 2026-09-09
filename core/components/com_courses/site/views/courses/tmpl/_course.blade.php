{{--
  Course card for grid display on intro/landing page.

  Variables (passed via $__view->view('_course')->set(...)):
    $course — Course model instance

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $courseUrl = Route::url($course->link(), false);
  $logo     = $course->logo('url');
  $logoUrl  = $logo ? Route::url($logo, false) : '';
  $blurb    = \Hubzero\Utility\Str::truncate(e($course->get('blurb')), 130);
@endphp

<div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow">
  <a href="{{ $courseUrl }}" class="block">
    <div class="p-4">
      <div class="flex items-start gap-3 mb-3">
        @if($logoUrl)
          <img src="{{ $logoUrl }}"
               alt=""
               class="w-12 h-12 rounded object-cover shrink-0"
               loading="lazy" />
        @else
          <div class="w-12 h-12 rounded bg-base-200 shrink-0" aria-hidden="true"></div>
        @endif

        <div class="min-w-0">
          @if($course->get('rating', 0) > 4)
            <span class="badge badge-warning badge-sm mb-1">
              {{ Lang::txt('COM_COURSES_TOP_RATED_COURSE') }}
            </span>
          @elseif($course->get('popularity', 0) > 7)
            <span class="badge badge-info badge-sm mb-1">
              {{ Lang::txt('COM_COURSES_POPULAR_COURSE') }}
            </span>
          @endif
        </div>
      </div>

      <h3 class="font-semibold text-base mb-2 line-clamp-2">
        {{ $course->get('title') }}
      </h3>

      @if($course->get('blurb'))
        <p class="text-sm text-base-content/70 line-clamp-3">
          {{ $blurb }}
        </p>
      @endif
    </div>
  </a>
</div>
