{{--
  Offering display — LMS two-pane layout with sidebar nav and plugin content.

  This is a bespoke layout (not page-container) because the offering view
  is an LMS shell with a left sidebar navigation and main content area,
  both driven by plugin content.

  Variables from controller (displayTask):
    $course        — Course model instance (with offering/section set)
    $config        — Component params (Registry)
    $plugins       — Collection of plugin objects
    $notifications — Array of notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $no_html = Request::getInt('no_html', 0);
  $tmpl    = Request::getWord('tmpl', false);

  $sparams = new \Hubzero\Config\Registry($course->offering()->section()->get('params'));
  $activePlugin = Request::getCmd('active');

  // Logo resolution: section > offering > course
  $logoSrc = $course->logo('url');
  if ($sectionLogo = $course->offering()->section()->logo('url')) {
      $logoSrc = $sectionLogo;
  } elseif ($offeringLogo = $course->offering()->logo('url')) {
      $logoSrc = $offeringLogo;
  }

  $__view->css();

  $courseOverviewUrl = Route::url($course->link(), false);
@endphp

@if(!$no_html && $tmpl !== 'component')
  {{-- Full page header --}}
  <header class="page-header">
    <div class="page-header-content">
      <div class="flex items-center gap-4">
        @if($logoSrc)
          <img src="{{ Route::url($logoSrc, false) }}"
               alt=""
               class="w-10 h-10 rounded object-cover"
               aria-hidden="true" />
        @endif
        <div>
          <h1>{{ e(stripslashes($course->get('title'))) }}</h1>
          <p class="text-sm text-base-content/70">
            <a class="link link-hover" href="{{ $courseOverviewUrl }}">
              {{ Lang::txt('COM_COURSES_COURSE_OVERVIEW') }}
            </a>
            <span class="mx-1" aria-hidden="true">/</span>
            <strong>{{ Lang::txt('COM_COURSES_OFFERING') }}:</strong>
            {{ e(stripslashes($course->offering()->get('title'))) }}
            <span class="mx-1" aria-hidden="true">/</span>
            <strong>{{ Lang::txt('COM_COURSES_SECTION') }}:</strong>
            {{ e(stripslashes($course->offering()->section()->get('title'))) }}
          </p>
        </div>
      </div>
    </div>
  </header>
@endif

@php
  $hasAccess = $course->offering()->access('view') || $sparams->get('preview', 0);
  $isExpired = $course->offering()->section()->expired() && !$sparams->get('preview', 0);
@endphp

@if(!$hasAccess)
  {{-- Not enrolled gate --}}
  @php
    $enrollView = new \Hubzero\Plugin\View([
        'folder'  => 'courses',
        'element' => 'outline',
        'name'    => 'shared',
        'layout'  => '_not_enrolled',
    ]);
    $enrollView->set('course', $course)
               ->set('option', 'com_courses')
               ->set('message', Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED'));
  @endphp
  <section class="page-body">
    {!! $enrollView !!}
  </section>

@elseif($isExpired)
  {{-- Expired section --}}
  <section class="page-body">
    <div class="max-w-lg mx-auto text-center py-12">
      <div class="alert alert-warning mb-6">
        {{ Lang::txt('COM_COURSES_SECTION_EXPIRED') }}
      </div>
      <p class="font-semibold mb-2">{{ Lang::txt('COM_COURSES_WHERE_TO_LEARN_MORE') }}</p>
      <a class="btn btn-primary"
         href="{{ Route::url('index.php?option=' . $option . '&controller=courses&task=browse', false) }}">
        {{ Lang::txt('COM_COURSES_BROWSE_CATALOG') }}
      </a>
    </div>
  </section>

@else
  @if(!$no_html && $tmpl !== 'component')
    {{-- Two-pane LMS layout --}}
    <section class="page-body">
      <div class="flex min-h-[60vh]">
        {{-- Sidebar navigation --}}
        <nav class="w-64 shrink-0 border-r border-base-300 bg-base-100"
             aria-label="{{ Lang::txt('COM_COURSES_COURSE_NAVIGATION') }}">
          <ul class="menu p-2">
            @foreach($plugins as $plugin)
              @if(!$plugin->get('display_menu_tab'))
                @continue
              @endif

              @php
                $isNotManager = !$course->offering()->access('manage', 'section');
                $isManagerOnly = $plugin->get('default_access') === 'managers';
              @endphp

              @if($isNotManager && $isManagerOnly)
                @continue
              @endif

              @if(!$hasAccess)
                <li class="disabled" title="{{ Lang::txt('COM_COURSES_RESTRICTED_PAGE') }}">
                  <span class="opacity-50">
                    {{ e($plugin->get('title')) }}
                  </span>
                </li>
              @else
                @php
                  $pluginUrl  = Route::url($course->offering()->link() . '&active=' . $plugin->get('name'), false);
                  $isActive   = ($activePlugin === $plugin->get('name'));
                  $metaCount  = $plugin->get('meta_count');
                @endphp
                <li>
                  <a class="{{ $isActive ? 'active' : '' }}"
                     href="{{ $pluginUrl }}"
                     @if($isActive) aria-current="page" @endif
                     title="{{ e($plugin->get('title')) . ' — ' . e($plugin->get('description')) }}">
                    {{ e($plugin->get('title')) }}
                    @if($metaCount)
                      <span class="badge badge-sm">{{ $metaCount }}</span>
                    @endif
                  </a>
                  @if($plugin->get('meta_alert'))
                    {!! $plugin->get('meta_alert') !!}
                  @endif
                </li>
              @endif
            @endforeach
          </ul>
        </nav>

        {{-- Main content area --}}
        <div class="flex-1 p-6">
          {{-- Notifications --}}
          @foreach($notifications as $notification)
            <div class="alert alert-{{ $notification['type'] === 'error' ? 'error' : 'info' }} mb-4"
                 role="status">
              <span>{{ e($notification['message']) }}</span>
            </div>
          @endforeach

          {{-- Plugin content --}}
          <div id="page_content">
  @endif

            @foreach($plugins as $plugin)
              @if($html = $plugin->get('html'))
                {!! $html !!}
              @endif
            @endforeach

  @if(!$no_html && $tmpl !== 'component')
          </div>
        </div>
      </div>
    </section>
  @endif
@endif
