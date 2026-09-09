{{--
  Section dropdown button for course enrollment/access.

  Variables (passed via $__view->view('_button')->set(...)):
    $course   — Course model instance
    $offering — Offering model instance
    $section  — Default/primary section
    $sections — Collection or array of available sections

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Route;

  $isManager   = $course->isManager();
  $isStudent   = $course->isStudent();
  $primaryUrl  = Route::url($offering->link('enter'), false);
  $primaryText = $isManager
      ? e(stripslashes($offering->get('title')))
      : e(stripslashes($section->get('title')));
@endphp

<div class="dropdown dropdown-end">
  <a href="{{ $primaryUrl }}" class="btn btn-primary">
    {{ $primaryText }}
  </a>
  <button type="button"
          tabindex="0"
          class="btn btn-primary btn-square"
          aria-label="{{ \Hubzero\Facades\Lang::txt('COM_COURSES_MORE_SECTIONS') }}"
          aria-haspopup="true">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
         stroke-width="2" stroke="currentColor" class="size-4" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
    </svg>
  </button>
  <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg z-10 w-52 p-2">
    @foreach($sections as $key => $sec)
      @if($key == 0 && $isStudent)
        @continue
      @endif
      @php
        $offering->section($sec);
        $secUrl = Route::url($offering->link(), false);
      @endphp
      <li>
        <a href="{{ $secUrl }}">
          {{ e(stripslashes($sec->get('title'))) }}
        </a>
      </li>
    @endforeach
  </ul>
</div>
