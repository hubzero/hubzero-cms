{{--
  Enrollment buttons for course sidebar.

  Handles the complex logic of showing appropriate enrollment/access buttons
  based on user role (guest, student, manager) and offering/section state.

  Variables (passed via $__view->view('_enrollment')->set(...)):
    $course    — Course model instance
    $offerings — Offerings collection
    $isManager — bool

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $buttonCount = 0;
  $found = false;

  if ($offerings->total()) {
      // If a student — show sections they're enrolled in
      if ($course->isStudent()) {
          $studentFilters = [
              'state'     => 1,
              'available' => true,
          ];

          foreach ($offerings as $offering) {
              $s = [];
              $sections = $offering->sections($studentFilters);
              if ($sections->total() > 0) {
                  foreach ($sections as $section) {
                      if (!$section->isMember()) {
                          continue;
                      }
                      $s[] = $section;
                  }
              }

              if (count($s) > 1) {
                  $offering->section($s[0]);
                  $showDropdown = true;
                  $found = true;
                  $buttonCount++;
              } elseif (count($s) == 1) {
                  $offering->section($s[0]);
                  $showDropdown = false;
                  $found = true;
                  $buttonCount++;
              }
          }
      }
  }
@endphp

@if($offerings->total())
  @if($course->isStudent())
    @foreach($offerings as $offering)
      @php
        $s = [];
        $studentFilters = ['state' => 1, 'available' => true];
        $sections = $offering->sections($studentFilters);
        if ($sections->total() > 0) {
            foreach ($sections as $section) {
                if (!$section->isMember()) { continue; }
                $s[] = $section;
            }
        }
      @endphp
      @if(count($s) > 1)
        @php $offering->section($s[0]); @endphp
        {!! $__view->view('_button')
              ->set('course', $course)
              ->set('offering', $offering)
              ->set('section', $s[0])
              ->set('sections', $s)
              ->loadTemplate() !!}
      @elseif(count($s) == 1)
        @php $offering->section($s[0]); @endphp
        <p class="mt-3">
          <a class="btn btn-primary w-full"
             href="{{ Route::url($offering->link('enter'), false) }}">
            {{ Lang::txt('COM_COURSES_ACCESS_COURSE') }}
          </a>
        </p>
      @endif
    @endforeach
  @endif

  @if(!$found)
    @php
      if ($isManager) {
          $secFilters = ['available' => false];
      } else {
          $secFilters = [
              'state'      => 1,
              'available'  => true,
              'enrollment' => [0, 1],
              'started'    => true,
              'ended'      => false,
              'is_default' => 0,
          ];
      }
    @endphp

    @foreach($offerings as $offering)
      @php
        $dflt = $offering->section('!!default!!');
        if (!$dflt->exists()) {
            if (!$offering->sections($secFilters, true)->total()) {
                $offering->makeSection();
            }
            $dflt = $offering->sections()->fetch('first');
            $offering->section($dflt);
        }

        $sections = $offering->sections($secFilters, true);
      @endphp

      @if($isManager && $sections->total() > 0)
        {!! $__view->view('_button')
              ->set('course', $course)
              ->set('offering', $offering)
              ->set('section', $dflt)
              ->set('sections', $sections)
              ->loadTemplate() !!}
        @php $buttonCount++; @endphp
      @else
        @if($dflt->get('enrollment') == 2 && !$dflt->isMember())
          @continue
        @endif
        <p class="mt-3">
          <a class="btn btn-primary w-full"
             href="{{ Route::url($offering->link('enter'), false) }}">
            {{ Lang::txt('COM_COURSES_ACCESS_COURSE') }}
          </a>
        </p>
        @php $buttonCount++; @endphp
        @if(!$isManager)
          @break
        @endif
      @endif
    @endforeach
  @endif
@endif

@if(!$buttonCount)
  <p class="text-sm text-base-content/50 mt-3">
    {{ Lang::txt('COM_COURSES_NO_OFFERINGS_AVAILABLE') }}
  </p>
@endif
