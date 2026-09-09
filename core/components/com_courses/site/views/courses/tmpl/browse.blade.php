{{--
  Courses catalog browse — search, sort, tag filter, paginated course list.

  Variables from controller (browseTask):
    $filters  — array with search, sortby, group, tag, index, limit, start, sort, sort_Dir
    $total    — int total matching courses
    $courses  — Collection of Course models
    $model    — Courses model instance (for tag cloud)
    $title    — Page title string
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append(Lang::txt('COM_COURSES_BROWSE'), 'index.php?option=' . $option . '&task=browse');
  Document::setTitle($title);

  $__view->css();

  $browseUrl = Route::url('index.php?option=' . $option . '&controller=courses&task=browse', false);
  $createUrl = Route::url('index.php?option=' . $option . '&controller=course&task=new', false);
  $canCreate = $config->get('access-create-course');

  // Build query string for sort links
  $qs = '';
  if ($filters['search']) { $qs .= '&search=' . e($filters['search']); }
  if ($filters['index'])  { $qs .= '&index=' . e($filters['index']); }
  if ($filters['tag'])    { $qs .= '&tag=' . e($filters['tag']); }
  if ($filters['group'])  { $qs .= '&group=' . e($filters['group']); }

  // Sort tab options for <x-filter-tabs>
  $sortKeys = [
      'title'      => 'COM_COURSES_SORT_TITLE',
      'alias'      => 'COM_COURSES_SORT_ALIAS',
      'popularity' => 'COM_COURSES_SORT_POPULARITY',
  ];
  $sortTabs  = [];
  $activeTab = '';
  foreach ($sortKeys as $sortKey => $sortLang) {
      $url = Route::url('index.php?option=' . $option . '&task=browse&sortby=' . $sortKey . $qs, false);
      $sortTabs[$url] = Lang::txt($sortLang);
      if ($filters['sortby'] === $sortKey) {
          $activeTab = $url;
      }
  }

  // Group info
  $group = null;
  if ($filters['group']) {
      $group = \Hubzero\User\Group::getInstance($filters['group']);
  }

  // Pagination range
  $rangeStart = $filters['start'] + 1;
  $rangeEnd   = min($filters['start'] + $filters['limit'], $total);

  // Results heading text
  if ($filters['search'] && $filters['tag']) {
      $headingText = Lang::txt('COM_COURSES_SEARCH_FOR_IN_WITH', e($filters['search']), e($filters['tag']));
  } elseif ($filters['search']) {
      $headingText = Lang::txt('COM_COURSES_SEARCH_FOR_IN', e($filters['search']));
  } elseif ($filters['tag']) {
      $headingText = Lang::txt('COM_COURSES_COURSES_WITH', e($filters['tag']));
  } else {
      $headingText = Lang::txt('COM_COURSES');
  }

  // Tag cloud for sidebar
  $tagCloud = $model->tags('cloud', [
      'limit'    => 20,
      'start'    => 0,
      'sort'     => 'total',
      'sort_Dir' => '',
      'scope'    => 'courses',
      'scope_id' => 0,
      'base'     => 'index.php?option=' . $option . '&task=browse',
      'filters'  => $filters,
  ]);
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

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_COURSES_FINDING_A_COURSE')" class="mt-10">
      <p class="text-sm text-base-content/70">
        {!! Lang::txt('COM_COURSES_FINDING_A_COURSE_EXPLANATION') !!}
      </p>
    </x-sidebar-card>

    <x-sidebar-card :title="Lang::txt('COM_COURSES_POPULAR_CATEGORIES')">
      @if($tagCloud)
        {!! $tagCloud !!}
      @else
        <p class="text-sm text-base-content/50">
          {{ Lang::txt('COM_COURSES_POPULAR_CATEGORIES_NONE') }}
        </p>
      @endif
    </x-sidebar-card>
  @endslot

  {{-- Search --}}
  <x-search-bar
      :action="$browseUrl"
      :query="$filters['search']"
      :placeholder="Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER')"
      :buttonLabel="Lang::txt('COM_COURSES_SEARCH')"
  >
    <input type="hidden" name="sortby" value="{{ e($filters['sortby']) }}" />
    <input type="hidden" name="index" value="{{ e($filters['index']) }}" />
  </x-search-bar>

  {{-- Applied tag filters --}}
  @if($filters['tag'])
    @php
      $tags = $model->parseTags($filters['tag']);
      $tagBaseUrl = 'index.php?option=' . $option . '&task=browse';
      if ($filters['search']) { $tagBaseUrl .= '&search=' . e($filters['search']); }
      if ($filters['sortby']) { $tagBaseUrl .= '&sortby=' . e($filters['sortby']); }
      if ($filters['index'])  { $tagBaseUrl .= '&index=' . e($filters['index']); }
      if ($filters['group'])  { $tagBaseUrl .= '&group=' . e($filters['group']); }
    @endphp
    <div class="flex flex-wrap gap-2 mb-4" role="list" aria-label="{{ Lang::txt('COM_COURSES_APPLIED_FILTERS') }}">
      @foreach($tags as $tag)
        @php
          $remaining = implode(',', $model->parseTags($filters['tag'], $tag));
          $removeUrl = Route::url($tagBaseUrl . '&tag=' . $remaining, false);
        @endphp
        <a href="{{ $removeUrl }}"
           class="badge badge-lg gap-1"
           role="listitem"
           aria-label="{{ Lang::txt('COM_COURSES_REMOVE_TAG', e(stripslashes($tag))) }}">
          {{ e(stripslashes($tag)) }}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="2" stroke="currentColor" class="size-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </a>
      @endforeach
    </div>
  @endif

  {{-- Group header --}}
  @if($group)
    <div class="flex items-center gap-4 p-4 bg-base-100 rounded-box shadow-sm mb-6">
      <a href="{{ Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn'), false) }}">
        <img src="{{ $group->getLogo() }}"
             alt="{{ e($group->get('description')) }}"
             class="w-12 h-12 rounded object-cover" />
      </a>
      <div>
        <p class="text-sm text-base-content/70">{{ Lang::txt('COM_COURSES_BROUGHT_BY_GROUP') }}</p>
        <h2 class="font-semibold">
          <a class="link link-hover"
             href="{{ Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn'), false) }}">
            {{ e($group->get('description')) }}
          </a>
        </h2>
      </div>
    </div>
  @endif

  {{-- Sort tabs --}}
  <nav class="mb-4" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
    <x-filter-tabs :options="$sortTabs" :active="$activeTab" />
  </nav>

  {{-- Results heading --}}
  <h2 class="text-lg font-semibold mb-4">
    {{ $headingText }}
    @if($courses->total() > 0)
      <span class="text-base-content/50 font-normal text-sm">
        ({{ $rangeStart }}–{{ $rangeEnd }} {{ Lang::txt('COM_COURSES_OF') }} {{ $total }})
      </span>
    @endif
  </h2>

  {{-- Course list --}}
  @if($courses->total() > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm">
      @foreach($courses as $course)
        @php
          $courseUrl = Route::url($course->link(), false);
          $logo     = $course->logo('url');
          $logoUrl  = $logo ? Route::url($logo, false) : '';
          $status   = '';
          if ($course->get('state') == 1 && $course->access('manage')) {
              $status = 'manager';
          } elseif ($course->get('state') != 1) {
              $status = 'draft';
          }

          // Instructors
          $instructors = $course->instructors();
          $viewLevels  = User::getAuthorisedViewLevels();
          $instrNames  = [];
          foreach ($instructors as $i) {
              $instructor = \Components\Members\Models\Member::oneOrNew($i->get('user_id'));
              $iName = e(stripslashes($instructor->get('name')));
              if (in_array($instructor->get('access'), $viewLevels)) {
                  $iLink = Route::url($instructor->link(), false);
                  $instrNames[] = '<a class="link link-hover" href="' . $iLink . '">' . $iName . '</a>';
              } else {
                  $instrNames[] = $iName;
              }
          }
        @endphp
        <li class="list-row">
          <div class="flex gap-4">
            {{-- Logo --}}
            <a href="{{ $courseUrl }}" class="shrink-0" aria-hidden="true" tabindex="-1">
              @if($logoUrl)
                <img src="{{ $logoUrl }}"
                     alt=""
                     class="w-16 h-16 rounded object-cover"
                     loading="lazy" />
              @else
                <div class="w-16 h-16 rounded bg-base-200" aria-hidden="true"></div>
              @endif
            </a>

            {{-- Content --}}
            <div class="min-w-0 flex-1">
              <h3 class="font-semibold">
                <a class="link link-hover" href="{{ $courseUrl }}">
                  {{ e($course->get('title')) }}
                </a>
                @if($status === 'draft')
                  <span class="badge badge-warning badge-sm ml-2">
                    {{ Lang::txt('COM_COURSES_FIELDS_STATE_DRAFT') }}
                  </span>
                @elseif($status === 'manager')
                  <span class="badge badge-info badge-sm ml-2">
                    {{ Lang::txt('COM_COURSES_MANAGER') }}
                  </span>
                @endif
              </h3>

              @if(count($instrNames) > 0)
                <p class="text-sm text-base-content/70">
                  {{ Lang::txt('COM_COURSES_COURSE_INSTRUCTORS') }}:
                  {!! implode(', ', $instrNames) !!}
                </p>
              @endif

              <p class="text-sm text-base-content/70 mt-1 line-clamp-2">
                {{ \Hubzero\Utility\Str::truncate($course->get('blurb'), 200) }}
              </p>
            </div>
          </div>
        </li>
      @endforeach
    </ul>

    {{-- Pagination --}}
    @php
      $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
      $pageNav->setAdditionalUrlParam('index', $filters['index']);
      $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
    @endphp
    {!! $pageNav->render() !!}
  @else
    <x-empty-state
        :title="Lang::txt('COM_COURSES_NO_RESULTS_FOUND')"
    />
  @endif
</x-page-container>
