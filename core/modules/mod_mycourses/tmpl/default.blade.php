{{--
  My Courses module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php $total = count($courses); @endphp

<div{!! $moduleclass ? ' class="' . $moduleclass . '"' : '' !!}>
  @if ($params->get('button_show_all', 1))
    <div class="flex gap-2 mb-3">
      <a class="btn btn-sm btn-outline"
         href="{{ Route::url('index.php?option=com_courses&task=browse') }}">
        {{ Lang::txt('MOD_MYCOURSES_ALL_COURSES') }}
      </a>
    </div>
  @endif

  @if ($courses && $total > 0)
    <ul class="list bg-base-100 rounded-box">
      @php $i = 0; @endphp
      @foreach ($courses as $course)
        @if ($i < $limit)
          @php
            $sfx = '';
            if (isset($course->offering_alias)) {
                $sfx .= '&offering=' . $course->offering_alias;
            }
            if (isset($course->section_alias) && !$course->is_default) {
                $sfx .= ':' . $course->section_alias;
            }
            $courseUrl = Route::url('index.php?option=com_courses&gid=' . $course->alias . $sfx);
            $stateLabels = [
                3 => ['MOD_MYCOURSES_COURSE_STATE_DRAFT', 'badge-warning'],
                2 => ['MOD_MYCOURSES_COURSE_STATE_DELETED', 'badge-error'],
                1 => ['MOD_MYCOURSES_COURSE_STATE_PUBLISHED', 'badge-success'],
                0 => ['MOD_MYCOURSES_COURSE_STATE_UNPUBLISHED', 'badge-ghost'],
            ];
            $stateInfo = $stateLabels[$course->state] ?? null;
          @endphp
          <li class="list-row items-center">
            <div class="flex-1 min-w-0">
              <a href="{{ $courseUrl }}" class="link link-hover font-medium">
                {{ stripslashes($course->title) }}
              </a>
              @if ($course->section_title)
                <div class="text-xs text-base-content/60">
                  <strong>{{ Lang::txt('MOD_MYCOURSES_SECTION') }}</strong>
                  {{ $course->section_title }}
                </div>
              @endif
            </div>
            <div class="flex items-center gap-2">
              @if ($stateInfo)
                <span class="badge badge-sm {{ $stateInfo[1] }}">{{ Lang::txt($stateInfo[0]) }}</span>
              @endif
              <span class="badge badge-sm badge-outline">{{ $course->role }}</span>
            </div>
          </li>
          @php $i++; @endphp
        @endif
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYCOURSES_NO_RESULTS') }}</em></p>
  @endif

  @if ($total > $limit)
    @php
      $coursesUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=courses');
    @endphp
    <p class="text-sm mt-2 text-base-content/60">
      {!! Lang::txt('MOD_MYCOURSES_YOU_HAVE_MORE', $limit, $total, $coursesUrl) !!}
    </p>
  @endif
</div>
