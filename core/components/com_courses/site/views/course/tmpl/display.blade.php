{{--
  Course detail page — overview with plugin tabs, offerings, instructors.

  Variables from controller (displayTask):
    $course        — Course model instance
    $active        — Active plugin/tab name
    $plugins       — Collection of plugin objects
    $config        — Component params (Registry)
    $notifications — Array of notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  $field  = strtolower(Request::getWord('field', ''));
  $action = strtolower(Request::getWord('action', ''));

  // Title & pathway
  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append(e($course->get('title')), $course->link());
  Document::setTitle(Lang::txt('COM_COURSES') . ': ' . $course->get('title'));

  $__view->css();
  $__view->js();

  // Offerings
  $isManager = $course->isManager();
  if ($isManager) {
      $offeringFilters = [
          'available' => false,
          'state'     => [0, 1, 3],
          'sort'      => 'publish_up',
          'sort_Dir'  => 'DESC',
      ];
  } else {
      $offeringFilters = [
          'available' => true,
          'state'     => 1,
          'sort'      => 'publish_up',
          'sort_Dir'  => 'DESC',
      ];
  }
  $offerings = $course->offerings($offeringFilters, true);

  $canEdit  = $course->access('edit', 'course');
  $canCreate = $course->access('create', 'course');
  $manager  = $course->manager(User::get('id'));
  $logo     = $course->logo('url');

  // Action URLs
  $browseUrl    = Route::url('index.php?option=' . $option . '&controller=course&task=browse', false);
  $courseUrl    = Route::url($course->link(), false);
  $copyUrl      = Route::url(
      'index.php?option=' . $option . '&controller=course&gid=' . $course->get('alias') . '&task=copy',
      false
  );
  $forkUrl      = Route::url(
      'index.php?option=' . $option . '&controller=course&gid=' . $course->get('alias') . '&task=fork',
      false
  );
  $publishUrl   = Route::url($course->link() . '&task=publish', false);
@endphp

<x-page-container :title="Lang::txt('COM_COURSES')">
  @slot('actions')
    @if($canEdit && $canCreate)
      @if($manager && $manager->get('id'))
        <a class="btn btn-ghost" href="{{ $copyUrl }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
          </svg>
          {{ Lang::txt('COM_COURSES_COPY') }}
        </a>
      @elseif($course->config('allow_forks'))
        <a class="btn btn-ghost" href="{{ $forkUrl }}">
          {{ Lang::txt('COM_COURSES_FORK') }}
        </a>
      @endif
    @endif
    <a class="btn btn-ghost" href="{{ $browseUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_COURSES_CATALOG') }}
    </a>
  @endslot

  @slot('sidebar')
    {{-- Sidebar: Summary --}}
    @if($field === 'summary' && $canEdit)
      {{-- Inline summary edit form --}}
      <x-sidebar-card :title="Lang::txt('COM_COURSES_SUMMARY')">
        <form action="{{ Route::url('index.php?option=' . $option, false) }}" method="post">
          <x-form-field name="course[length]" inputId="field_length"
                        :label="Lang::txt('COM_COURSES_COURSE_LENGTH')">
            <input type="text" name="course[length]" id="field_length"
                   class="input input-bordered w-full input-sm"
                   value="{{ e($course->get('length')) }}"
                   placeholder="{{ Lang::txt('COM_COURSES_COURSE_LENGTH_HINT') }}" />
          </x-form-field>

          <x-form-field name="course[effort]" inputId="field_effort"
                        :label="Lang::txt('COM_COURSES_COURSE_EFFORT')">
            <input type="text" name="course[effort]" id="field_effort"
                   class="input input-bordered w-full input-sm"
                   value="{{ e($course->get('effort')) }}"
                   placeholder="{{ Lang::txt('COM_COURSES_COURSE_EFFORT_HINT') }}" />
          </x-form-field>

          <div class="flex gap-2 mt-3">
            <button class="btn btn-primary btn-sm" type="submit">
              {{ Lang::txt('COM_COURSES_SAVE') }}
            </button>
            <a class="btn btn-ghost btn-sm" href="{{ $courseUrl }}">
              {{ Lang::txt('JCANCEL') }}
            </a>
          </div>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="course" />
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="gid" value="{{ e($course->get('alias')) }}" />
          <input type="hidden" name="course[id]" value="{{ e($course->get('id')) }}" />
          <input type="hidden" name="course[alias]" value="{{ e($course->get('alias')) }}" />
          {!! Html::input('token') !!}
        </form>
      </x-sidebar-card>
    @else
      {{-- Summary table (read-only) --}}
      @if($canEdit)
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-sm font-semibold">{{ Lang::txt('COM_COURSES_SUMMARY') }}</h3>
          <a class="btn btn-ghost btn-xs"
             href="{{ Route::url($course->link() . '&task=edit&field=summary', false) }}">
            {{ Lang::txt('JACTION_EDIT') }}
          </a>
        </div>
      @endif

      <x-sidebar-card>
        <table class="table table-sm">
          <tbody>
            @if($course->config('show_stats'))
              <tr>
                <th class="text-base-content/70">{{ Lang::txt('COM_COURSES_COURSE_ENROLLED') }}</th>
                <td>{{ number_format((int) $course->students(['count' => true])) }}</td>
              </tr>
            @endif
            @if($length = $course->get('length'))
              <tr>
                <th class="text-base-content/70">{{ Lang::txt('COM_COURSES_COURSE_LENGTH') }}</th>
                <td>{{ e($length) }}</td>
              </tr>
            @endif
            @if($effort = $course->get('effort'))
              <tr>
                <th class="text-base-content/70">{{ Lang::txt('COM_COURSES_COURSE_EFFORT') }}</th>
                <td>{{ e($effort) }}</td>
              </tr>
            @endif
            @php
              // Certificate availability
              $cert = false;
              if ($course->certificate()->exists()) {
                  foreach ($offerings as $off) {
                      $sects = $off->sections(['state' => 1, 'available' => true]);
                      foreach ($sects as $sect) {
                          if ($sect->params('certificate') && $sect->get('enrollment') != 2) {
                              $cert = true;
                              break 2;
                          }
                      }
                  }
              }
            @endphp
            @if($cert)
              <tr>
                <th class="text-base-content/70">{{ Lang::txt('COM_COURSES_COURSE_CERTIFICATE') }}</th>
                <td>{{ Lang::txt('COM_COURSES_COURSE_CERTIFICATE_AVAILABLE') }}</td>
              </tr>
            @endif
          </tbody>
        </table>

        {{-- Enrollment buttons --}}
        @php
          $enrollmentCount = 0;
        @endphp
        {!! $__view->view('_enrollment')
              ->set('course', $course)
              ->set('offerings', $offerings)
              ->set('isManager', $isManager)
              ->loadTemplate() !!}
      </x-sidebar-card>

      {{-- Instructors --}}
      @if($canEdit)
        <div class="flex items-center justify-between mb-2 mt-6">
          <h3 class="text-sm font-semibold">{{ Lang::txt('COM_COURSES_MANAGE_INSTRUCTORS') }}</h3>
          <a class="btn btn-ghost btn-xs"
             href="{{ Route::url($course->link() . '&task=instructors', false) }}">
            {{ Lang::txt('COM_COURSES_MANAGE') }}
          </a>
        </div>
      @endif

      @php
        $instructors = $course->instructors();
      @endphp
      @if(count($instructors) > 0)
        <x-sidebar-card>
          <h3 class="text-sm font-semibold mb-3">
            {{ count($instructors) > 1
                ? Lang::txt('COM_COURSES_ABOUT_THE_INSTRUCTORS')
                : Lang::txt('COM_COURSES_ABOUT_THE_INSTRUCTOR') }}
          </h3>
          @foreach($instructors as $i)
            {!! $__view->view('_instructor')
                  ->set('biolength', 200)
                  ->set('instructor', \Components\Members\Models\Member::oneOrNew($i->get('user_id')))
                  ->loadTemplate() !!}
          @endforeach
        </x-sidebar-card>
      @else
        <p class="text-sm text-base-content/50 mt-4">
          {{ Lang::txt('COM_COURSES_NO_INSTRUCTORS_FOUND') }}
        </p>
      @endif

      {{-- Plugin metadata --}}
      @if($plugins)
        @foreach($plugins as $plugin)
          @if($meta = $plugin->get('metadata'))
            {!! $meta !!}
          @endif
        @endforeach
      @endif
    @endif
  @endslot

  {{-- Draft banner --}}
  @if($canEdit && $course->get('state') != 1)
    <div class="alert alert-warning mb-6" role="status">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
      </svg>
      <span>
        <strong>{{ Lang::txt('COM_COURSES_FIELDS_STATE_DRAFT') }}</strong>
      </span>
      <a class="btn btn-sm btn-success" href="{{ $publishUrl }}">
        {{ Lang::txt('COM_COURSES_PUBLISH') }}
      </a>
    </div>
  @endif

  {{-- Course intro section --}}
  <section class="mb-8" aria-label="{{ Lang::txt('COM_COURSES_COURSE_OVERVIEW') }}">
    @if(($field === 'blurb' || $field === 'tags') && $canEdit)
      {{-- Inline blurb/tags edit form --}}
      <form action="{{ Route::url('index.php?option=' . $option, false) }}"
            method="post" class="max-w-2xl">
        <x-form-field name="course[title]" inputId="field_title"
                      :label="Lang::txt('COM_COURSES_FIELD_TITLE')" :required="true">
          <input type="text" name="course[title]" id="field_title"
                 class="input input-bordered w-full"
                 value="{{ e($course->get('title')) }}" required />
        </x-form-field>

        <x-form-field name="course[blurb]" inputId="field_blurb"
                      :label="Lang::txt('COM_COURSES_FIELD_BLURB')">
          <textarea name="course[blurb]" id="field_blurb"
                    class="textarea textarea-bordered w-full"
                    rows="5">{{ e($course->get('blurb')) }}</textarea>
        </x-form-field>

        <x-form-field name="tags" inputId="actags"
                      :label="Lang::txt('COM_COURSES_FIELD_TAGS')"
                      :hint="Lang::txt('COM_COURSES_FIELD_TAGS_HINT')">
          {!! $__view->autocompleter('tags', 'tags', e($course->tags('string')), 'actags') !!}
        </x-form-field>

        <x-form-field name="params[allow_forks]" inputId="params-allow_forks"
                      :label="Lang::txt('COM_COURSES_ALLOW_FORKS')" type="checkbox">
          <input type="checkbox" class="checkbox" name="params[allow_forks]"
                 id="params-allow_forks" value="1"
                 @if($course->config('allow_forks')) checked @endif />
        </x-form-field>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">
            {{ Lang::txt('COM_COURSES_SAVE') }}
          </button>
          <a class="btn btn-ghost" href="{{ $courseUrl }}">
            {{ Lang::txt('JCANCEL') }}
          </a>
        </div>

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="course" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="gid" value="{{ e($course->get('alias')) }}" />
        <input type="hidden" name="course[id]" value="{{ e($course->get('id')) }}" />
        <input type="hidden" name="course[alias]" value="{{ e($course->get('alias')) }}" />
        {!! Html::input('token') !!}
      </form>
    @else
      {{-- Read-only intro --}}
      <div class="flex items-start gap-6">
        <div class="flex-1 min-w-0">
          @if($canEdit)
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-sm font-semibold text-base-content/70">
                {{ Lang::txt('COM_COURSES_FIELDS_TITLE_BLURB') }}
              </h2>
              <a class="btn btn-ghost btn-xs"
                 href="{{ Route::url($course->link() . '&task=edit&field=blurb', false) }}">
                {{ Lang::txt('JACTION_EDIT') }}
              </a>
            </div>
          @endif

          <h2 class="text-2xl font-bold mb-3">
            {{ e($course->get('title')) }}
          </h2>
          <p class="text-base-content/70 mb-4">
            {{ e($course->get('blurb')) }}
          </p>

          {!! $course->tags('cloud') !!}

          @if($course->get('group_id'))
            @php
              $group = \Hubzero\User\Group::getInstance($course->get('group_id'));
            @endphp
            @if($group)
              <div class="flex items-center gap-3 mt-4 p-3 bg-base-200 rounded-box">
                <a href="{{ Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn'), false) }}">
                  <img src="{{ $group->getLogo() }}"
                       alt="{{ e(stripslashes($group->get('description'))) }}"
                       class="w-12 h-12 rounded object-cover" />
                </a>
                <div>
                  <p class="text-sm text-base-content/70">
                    {{ Lang::txt('COM_COURSES_BROUGHT_BY_GROUP') }}
                  </p>
                  <a class="font-semibold link link-hover"
                     href="{{ Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn'), false) }}">
                    {{ e(stripslashes($group->get('description'))) }}
                  </a>
                </div>
              </div>
            @endif
          @endif
        </div>

        {{-- Course logo --}}
        <div class="shrink-0 w-48">
          @if($logo)
            @php
              $size = $course->logo('size');
              $orientation = ($size['width'] >= $size['height']) ? 'landscape' : 'portrait';
            @endphp
            <img src="{{ Route::url($logo, false) }}"
                 class="w-full rounded-box"
                 alt="{{ e($course->get('title')) }}" />
          @else
            <div class="w-full aspect-square rounded-box bg-base-200" aria-hidden="true"></div>
          @endif

          @if($canEdit)
            <div class="mt-2"
                 data-instructions="{{ Lang::txt('COM_COURSES_CLICK_OR_DROP_FILE') }}"
                 data-action="{{ Route::url('index.php?option=' . $option . '&no_html=1&controller=media&task=upload&listdir=' . $course->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
              <p class="text-xs text-base-content/50 text-center">
                {{ Lang::txt('COM_COURSES_CLICK_OR_DROP_FILE') }}
              </p>
            </div>
          @endif
        </div>
      </div>
    @endif
  </section>

  {{-- No offerings help (manager only) --}}
  @if($canEdit && !$offerings->total())
    <div class="alert alert-info mb-6">
      <div>
        <p class="font-semibold">{{ Lang::txt('COM_COURSES_COURSE_NEEDS_AN_OFFERING') }}</p>
        <p class="text-sm">{{ Lang::txt('COM_COURSES_COURSE_NEEDS_AN_OFFERING_EXPLANATION') }}</p>
      </div>
      <a class="btn btn-sm btn-primary"
         href="{{ Route::url($course->link() . '&task=newoffering', false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ Lang::txt('COM_COURSES_CREATE_OFFERING') }}
      </a>
    </div>
  @endif

  {{-- Notifications --}}
  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'error' ? 'error' : 'info' }} mb-4"
         role="status">
      <span>{{ $notification['message'] }}</span>
    </div>
  @endforeach

  {{-- Plugin tabs --}}
  @if($plugins)
    @php
      if ($action === 'addpage') {
          $activeTab = '';
      } else {
          $activeTab = $active;
      }
    @endphp
    <nav class="mb-6" aria-label="{{ Lang::txt('COM_COURSES_COURSE_TABS') }}">
      <div class="tabs tabs-border">
        @foreach($plugins as $i => $plugin)
          @php
            $tabUrl  = Route::url($course->link() . '&active=' . $plugin->get('name'), false);
            $isActive = ($plugin->get('name') === $activeTab);

            if ($isActive) {
                Pathway::append($plugin->get('title'), $tabUrl);
                if ($activeTab !== 'overview') {
                    Document::setTitle(Document::getTitle() . ': ' . $plugin->get('title'));
                }
            }
          @endphp
          <a class="tab {{ $isActive ? 'tab-active' : '' }}"
             href="{{ $tabUrl }}"
             @if($isActive) aria-current="true" @endif>
            {{ e($plugin->get('title')) }}
          </a>
        @endforeach
        @if($canEdit)
          <a class="tab"
             href="{{ Route::url($course->link() . '&action=addpage', false) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ Lang::txt('PLG_COURSES_PAGES_ADD_PAGE') }}
          </a>
        @endif
      </div>
    </nav>

    {{-- Tab content --}}
    @if(($action === 'addpage' || $action === 'editpage') && $canEdit)
      {{-- Add/edit page form --}}
      @php
        $page = $course->page($activeTab);
      @endphp
      <div class="max-w-2xl">
        <form action="{{ Route::url($course->link(), false) }}" method="post">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <x-form-field name="page[title]" inputId="field-title"
                          :label="Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE')"
                          :hint="Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE_HINT')"
                          :required="true">
              <input type="text" name="page[title]" id="field-title"
                     class="input input-bordered w-full"
                     value="{{ e(stripslashes($page->get('title'))) }}" required />
            </x-form-field>

            <x-form-field name="page[url]" inputId="field-url"
                          :label="Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS')"
                          :hint="Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS_HINT')">
              <input type="text" name="page[url]" id="field-url"
                     class="input input-bordered w-full"
                     value="{{ e(stripslashes($page->get('url'))) }}" />
            </x-form-field>
          </div>

          <div class="mb-4">
            {!! $__view->editor(
                'page[content]',
                e(stripslashes($page->get('content'))),
                35, 50, 'field_content',
                ['class' => 'form-control']
            ) !!}
          </div>

          <div class="form-actions">
            <button class="btn btn-primary" type="submit">
              {{ Lang::txt('COM_COURSES_SAVE') }}
            </button>
            <a class="btn btn-ghost" href="{{ $courseUrl }}">
              {{ Lang::txt('JCANCEL') }}
            </a>
          </div>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="course" />
          <input type="hidden" name="task" value="savepage" />
          <input type="hidden" name="gid" value="{{ e($course->get('alias')) }}" />
          <input type="hidden" name="page[id]" value="{{ e($page->get('id')) }}" />
          <input type="hidden" name="page[alias]" value="{{ e($page->get('alias')) }}" />
          <input type="hidden" name="page[course_id]" value="{{ $course->get('id') }}" />
          <input type="hidden" name="page[section_id]" value="0" />
          <input type="hidden" name="page[offering_id]" value="0" />
          {!! Html::input('token') !!}
        </form>
      </div>
    @else
      {{-- Plugin content --}}
      @foreach($plugins as $plugin)
        @if($html = $plugin->get('html'))
          <div id="{{ $plugin->get('name') }}-section">
            @if($canEdit && $plugin->get('isPage'))
              <div class="flex gap-2 mb-4">
                <a class="btn btn-ghost btn-sm"
                   href="{{ Route::url($course->link() . '&active=' . $plugin->get('name') . '&action=editpage', false) }}">
                  {{ Lang::txt('JACTION_EDIT') }}
                </a>
                <a class="btn btn-ghost btn-sm text-error"
                   href="{{ Route::url($course->link() . '&active=' . $plugin->get('name') . '&task=deletepage', false) }}">
                  {{ Lang::txt('COM_COURSES_DELETE') }}
                </a>
              </div>
            @endif
            {!! $html !!}
          </div>
        @endif
      @endforeach
    @endif
  @endif

  {{-- Post-course event hook --}}
  @php
    $after = Event::trigger('courses.onCourseViewAfter', [$course]);
  @endphp
  @if($after && count($after) > 0)
    <section class="mt-8">
      {!! implode("\n", $after) !!}
    </section>
  @endif
</x-page-container>
