{{--
  Groups intro/landing page with group sections.

  Variables from controller:
    $title             — string: page title
    $option            — string: component option
    $config            — Registry: component config
    $notifications     — array: queued notification messages
    $mygroups          — array: user's groups (members, invitees, applicants)
    $interestinggroups — array: groups matching user interests
    $populargroups     — array: popular groups
    $featuredgroups    — array: featured groups

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('introduction.css', 'system')
         ->css()
         ->js();

  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
  $newUrl    = Route::url('index.php?option=' . $option . '&task=new');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    @if(User::authorise('core.create', $option))
      <a class="btn btn-sm btn-primary" href="{{ $newUrl }}">
        {{ Lang::txt('COM_GROUPS_NEW') }}
      </a>
    @endif
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  {{-- Introduction / search --}}
  <section class="mb-8">
    <div class="flex flex-col md:flex-row gap-6 items-start">
      <div class="flex-1">
        <x-search-bar :action="$browseUrl"
                      name="search"
                      query=""
                      :placeholder="Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER')"
                      :label="Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP')" />
        <p class="text-base-content/70 mt-3">
          {{ Lang::txt('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_DESC') }}
        </p>
      </div>
      <div class="shrink-0">
        <a class="btn btn-outline" href="{{ $browseUrl }}">
          {{ Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_BUTTON_TEXT') }}
        </a>
      </div>
    </div>
  </section>

  @if(!User::isGuest())
    {{-- Invitations --}}
    @if($config->get('intro_mygroups', 1))
      @if(isset($mygroups['invitees']) && count($mygroups['invitees']) > 0)
        <section class="mb-8">
          <h3 class="text-lg font-semibold mb-2">
            {{ Lang::txt('COM_GROUPS_INTRO_GROUP_INVITES') }}
          </h3>
          <p class="text-sm text-base-content/70 mb-3">
            {{ Lang::txt('COM_GROUPS_INTRO_GROUP_INVITES_DESC') }}
          </p>
          <ul class="space-y-2">
            @foreach($mygroups['invitees'] as $invite)
              <li class="flex items-center gap-2">
                <span>{{ $invite->description }}</span>
                <a class="btn btn-success btn-sm"
                   href="{{ Route::url('index.php?option=com_groups&cn=' . $invite->cn . '&task=accept') }}">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_ACCEPT') }}
                </a>
              </li>
            @endforeach
          </ul>
        </section>
      @endif

      {{-- Pending requests --}}
      @if(isset($mygroups['applicants']) && count($mygroups['applicants']) > 0)
        <section class="mb-8">
          <h3 class="text-lg font-semibold mb-2">
            {{ Lang::txt('COM_GROUPS_INTRO_GROUP_REQUESTS') }}
          </h3>
          <p class="text-sm text-base-content/70 mb-3">
            {{ Lang::txt('COM_GROUPS_INTRO_GROUP_REQUESTS_DESC') }}
          </p>
          <ul class="space-y-2">
            @foreach($mygroups['applicants'] as $applicant)
              <li class="flex items-center gap-2">
                <span>{{ $applicant->description }}</span>
                <a class="btn btn-ghost btn-sm"
                   href="{{ Route::url('index.php?option=com_groups&cn=' . $applicant->cn . '&task=cancel') }}">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') }}
                </a>
              </li>
            @endforeach
          </ul>
        </section>
      @endif

      {{-- My groups --}}
      @php $myMembers = $mygroups['members'] ?? []; @endphp
      <section class="mb-8">
        <h3 class="text-lg font-semibold mb-4">
          {{ Lang::txt('COM_GROUPS_INTRO_MY_GROUPS_TITLE') }}
        </h3>
        @if(!count($myMembers))
          <x-empty-state :title="Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS')" />
        @else
          <div class="space-y-3">
            @foreach($myMembers as $g)
              {!! $__view->view('_group')->set('group', $g)->display() !!}
            @endforeach
          </div>
        @endif
      </section>
    @endif

    {{-- Interesting groups --}}
    @if($config->get('intro_interestinggroups', 1))
      <section class="mb-8">
        <h3 class="text-lg font-semibold mb-4">
          {{ Lang::txt('COM_GROUPS_INTRO_INTERESTING_GROUPS_TITLE') }}
        </h3>
        @if(!count($interestinggroups))
          <x-empty-state :title="Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS')" />
        @else
          <div class="space-y-3">
            @foreach($interestinggroups as $g)
              {!! $__view->view('_group')->set('group', $g)->display() !!}
            @endforeach
          </div>
        @endif
      </section>
    @endif
  @endif

  {{-- Popular groups --}}
  @if($config->get('intro_populargroups', 1))
    <section class="mb-8">
      <h3 class="text-lg font-semibold mb-4">
        {{ Lang::txt('COM_GROUPS_INTRO_POPULAR_GROUPS_TITLE') }}
      </h3>
      @if(!count($populargroups))
        <x-empty-state :title="Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS')" />
      @else
        <div class="space-y-3">
          @foreach($populargroups as $g)
            {!! $__view->view('_group')->set('group', $g)->display() !!}
          @endforeach
        </div>
      @endif
    </section>
  @endif

  {{-- Featured groups --}}
  @if($config->get('intro_featuredgroups', 1) && count($featuredgroups) > 0)
    <section class="mb-8">
      <h3 class="text-lg font-semibold mb-4">
        {{ Lang::txt('COM_GROUPS_INTRO_FEATURED_GROUPS_TITLE') }}
      </h3>
      <div class="space-y-3">
        @foreach($featuredgroups as $g)
          {!! $__view->view('_group')->set('group', $g)->display() !!}
        @endforeach
      </div>
    </section>
  @endif
</x-page-container>
