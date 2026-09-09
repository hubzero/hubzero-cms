{{--
  Group view page — bespoke two-pane layout.

  This view does NOT use <x-page-container>. It renders a sidebar (logo,
  toolbar, navigation tabs, group info) alongside a main content area
  (header, notifications, plugin content). Supports $no_html mode for
  AJAX requests (outputs only $content).

  Variables from controller:
    $group         — Group object
    $option        — string: component option
    $content       — string: plugin/page content HTML
    $tab           — string: active tab name
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css()->js();

  $no_html = Request::getInt('no_html', 0);
@endphp

@if(!$no_html)
  {!! \Components\Groups\Helpers\View::displayBeforeSectionsContent($group) !!}

  @php
    $link = Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn'));

    // Join policy
    switch ($group->get('join_policy')) {
        case 3:  $policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING'); break;
        case 2:  $policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING'); break;
        case 1:  $policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING'); break;
        default: $policy = Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING'); break;
    }

    // Discoverability
    switch ($group->get('discoverability')) {
        case 1:  $discoverability = Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING'); break;
        default: $discoverability = Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING'); break;
    }

    // Created date
    $created = Date::of($group->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
  @endphp

  <div class="innerwrap">
  <div id="page_container" class="flex flex-col lg:flex-row gap-6">
  <div id="page_sidebar" class="lg:w-64 shrink-0">
    <div id="page_identity" class="mb-4 text-center">
      <a href="{{ $link }}"
         title="{{ Lang::txt('COM_GROUPS_OVERVIEW_HOME', $group->get('description')) }}">
        <img src="{{ $group->getLogo() }}"
             alt="{{ Lang::txt('COM_GROUPS_OVERVIEW_LOGO', $group->get('description')) }}"
             class="rounded-full w-24 h-24 mx-auto" />
      </a>
    </div>{{-- /#page_identity --}}

    {{-- Membership toolbar --}}
    {!! \Components\Groups\Helpers\View::displayToolbar($group) !!}

    {{-- Plugin tab navigation --}}
    {!! \Components\Groups\Helpers\View::displaySections($group) !!}

    <div id="page_info" class="mt-4">
      <div class="group-info">
        <ul class="space-y-1 text-sm">
          <li class="info-discoverability">
            <span class="label font-semibold">{{ Lang::txt('COM_GROUPS_INFO_DISCOVERABILITY') }}</span>
            <span class="value">{{ $discoverability }}</span>
          </li>
          <li class="info-join-policy">
            <span class="label font-semibold">{{ Lang::txt('COM_GROUPS_INFO_JOIN_POLICY') }}</span>
            <span class="value">{{ $policy }}</span>
          </li>
          @if($created)
            <li class="info-created">
              <span class="label font-semibold">{{ Lang::txt('COM_GROUPS_INFO_CREATED') }}</span>
              <span class="value">{{ $created }}</span>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>{{-- /#page_sidebar --}}

  <div id="page_main" class="flex-1 min-w-0">
  <div id="page_header" class="mb-4">
    <h2 class="text-2xl font-bold inline">
      <a href="{{ $link }}">{{ $group->get('description') }}</a>
    </h2>
    <span class="divider mx-2">&#9658;</span>
    <h3 class="text-lg inline">
      {!! \Components\Groups\Helpers\View::displayTab($group) !!}
    </h3>

    @if($tab === 'overview')
      @php
        $gt = new \Components\Groups\Models\Tags($group->get('gidNumber'));
      @endphp
      {!! $gt->render() !!}
    @endif
  </div>{{-- /#page_header --}}

  <div id="page_notifications">
    @foreach($notifications as $notification)
      <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
           role="alert">
        {!! $notification['message'] !!}
      </div>
    @endforeach
  </div>{{-- /#page_notifications --}}

  <div id="page_content" class="group_{{ $tab }}">
@endif

{!! $content !!}

@if(!$no_html)
  </div>{{-- /#page_content --}}
  </div>{{-- /#page_main --}}
  </div>{{-- /#page_container --}}
  </div>{{-- /.innerwrap --}}
@endif
