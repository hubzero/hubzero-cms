{{--
  Courses landing page — search bar, tagline, and popular courses grid.

  Variables from controller (introTask):
    $popularcourses — Collection of popular Course models
    $more_courses   — bool, true if more courses exist beyond the grid
    $config         — Component params (Registry) with access-* flags
    $title          — Page title string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Document::setTitle($title);

  $__view->css();

  $browseUrl  = Route::url('index.php?option=' . $option . '&controller=courses&task=browse', false);
  $createUrl  = Route::url('index.php?option=' . $option . '&controller=course&task=new', false);
  $canCreate  = $config->get('access-create-course');
@endphp

<x-page-container :title="$title">
  @if($canCreate)
    @slot('actions')
      <a class="btn btn-primary" href="{{ $createUrl }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ Lang::txt('COM_COURSES_CREATE_COURSE') }}
      </a>
    @endslot
  @endif

  {{-- Search + intro --}}
  <div class="max-w-2xl mx-auto text-center mb-8">
    <x-search-bar
        :action="$browseUrl"
        query=""
        :placeholder="Lang::txt('COM_COURSES_SEARCH_INTRO_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_COURSES_SEARCH')"
    />
    <p class="text-base-content/70 mb-4">
      {!! Lang::txt('COM_COURSES_WATCH_LEARN_TEST_EARN') !!}
    </p>
    <div class="flex gap-4 justify-center">
      <a class="btn btn-outline" href="{{ $browseUrl }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
        </svg>
        {{ Lang::txt('COM_COURSES_BROWSE_CATALOG') }}
      </a>
    </div>
  </div>

  {{-- Popular courses --}}
  @if($config->get('intro_popularcourses', 1))
    @if(count($popularcourses) > 0)
      <h2 class="text-lg font-semibold mb-4">
        {{ Lang::txt('COM_COURSES_POPULAR_COURSES') }}
      </h2>
      <x-card-grid cols="3">
        @foreach($popularcourses as $course)
          {!! $__view->view('_course')
                ->set('course', $course)
                ->loadTemplate() !!}
        @endforeach
      </x-card-grid>

      @if($more_courses)
        <div class="text-center mt-6">
          <a class="btn btn-outline" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_COURSES_MORE_COURSES') }}
          </a>
        </div>
      @endif
    @else
      <x-empty-state
          :title="Lang::txt('COM_COURSES_NO_POPULAR_COURSES')"
      />
    @endif
  @endif
</x-page-container>
