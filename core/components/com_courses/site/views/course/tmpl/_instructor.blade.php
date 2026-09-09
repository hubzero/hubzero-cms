{{--
  Instructor profile card partial.

  Variables (passed via $__view->view('_instructor')->set(...)):
    $instructor — Member model instance
    $biolength  — int, max bio character length

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $name       = e(stripslashes($instructor->get('name')));
  $viewLevels = User::getAuthorisedViewLevels();
  $isVisible  = in_array($instructor->get('access'), $viewLevels);
  $profileUrl = $isVisible ? Route::url($instructor->link(), false) : '';
  $org        = e(stripslashes($instructor->get('organization', '--')));

  $bio = '';
  if ($isVisible) {
      $bio = $instructor->get('bio') ?: $instructor->get('biography');
      if ($bio) {
          $bio = Html::content('prepare', $bio);
      }
  }
@endphp

<div class="flex gap-4 mb-6">
  {{-- Photo --}}
  <div class="shrink-0">
    @if($instructor->get('public') && $profileUrl)
      <a href="{{ $profileUrl }}">
        <img src="{{ $instructor->picture() }}"
             alt="{{ $name }}"
             class="w-16 h-16 rounded-full object-cover"
             loading="lazy" />
      </a>
    @else
      <img src="{{ $instructor->picture() }}"
           alt="{{ $name }}"
           class="w-16 h-16 rounded-full object-cover"
           loading="lazy" />
    @endif
  </div>

  {{-- Info --}}
  <div class="min-w-0">
    <h4 class="font-semibold">
      @if($profileUrl)
        <a class="link link-hover" href="{{ $profileUrl }}">{{ $name }}</a>
      @else
        {{ $name }}
      @endif
    </h4>
    <p class="text-sm text-base-content/70">{{ $org }}</p>

    @if($bio)
      <div class="prose prose-sm max-w-none mt-2">
        {!! $bio !!}
      </div>
    @elseif($isVisible)
      <p class="text-sm text-base-content/50 italic mt-2">
        {{ Lang::txt('COM_COURSES_INSTRUCTOR_NO_BIO') }}
      </p>
    @endif
  </div>
</div>
