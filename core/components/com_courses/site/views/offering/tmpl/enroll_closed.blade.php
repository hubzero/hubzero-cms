{{--
  Enrollment closed message page.

  Variables from controller (enrollTask):
    $course — Course model instance (with offering/section set)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  $courseTitle = e(stripslashes($course->get('title')));

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append($courseTitle, $course->link());
  Document::setTitle($courseTitle);

  $__view->css();

  $courseOverviewUrl = Route::url($course->link(), false);
  $supportUrl       = Route::url('index.php?option=com_support', false);
  $browseUrl        = Route::url('index.php?option=' . $option . '&controller=courses&task=browse', false);
@endphp

<x-page-container :title="$courseTitle">
  <div class="max-w-lg mx-auto py-12">
    <div class="alert alert-warning mb-8">
      {{ Lang::txt('COM_COURSES_ENROLLMENT_CLOSED') }}
    </div>

    <div class="space-y-6">
      <div>
        <p class="font-semibold mb-1">{{ Lang::txt('COM_COURSES_I_SHOULD_HAVE_ACCESS') }}</p>
        <a class="link link-hover" href="{{ $supportUrl }}">
          {{ $supportUrl }}
        </a>
      </div>

      <div>
        <p class="font-semibold mb-1">{{ Lang::txt('COM_COURSES_WHERE_CAN_I_FIND_OTHER_COURSES') }}</p>
        <a class="link link-hover" href="{{ $browseUrl }}">
          {{ Lang::txt('COM_COURSES_BROWSE_CATALOG') }}
        </a>
      </div>
    </div>

    <div class="mt-8">
      <a class="btn btn-ghost" href="{{ $courseOverviewUrl }}">
        {{ Lang::txt('COM_COURSES_COURSE_OVERVIEW') }}
      </a>
    </div>
  </div>
</x-page-container>
