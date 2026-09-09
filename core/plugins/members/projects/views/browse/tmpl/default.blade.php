{{--
  Member Projects — project browse with filters and invites.

  Variables from plugin (onMembers):
    $user     — member profile object
    $option   — component option
    $total    — total project count
    $projects — all projects
    $owned    — owned projects
    $rows     — current project rows
    $invites  — pending invitations
    $filters  — filters array (filterby, sortby, sortdir, which)
    $newcount — new activity count
    $which    — current view mode (all/updates)
    $config   — component config

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $memberId = $user->get('id');
  $isUser = User::get('id') == $memberId;
  $base = 'index.php?option=com_members&id=' . $memberId . '&active=projects';
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_PROJECTS') }}</h3>

@if (User::authorise('core.create', 'com_projects'))
  <div class="flex justify-end mb-4">
    <a class="btn btn-primary btn-sm" href="{{ Route::url('index.php?option=com_projects&task=start') }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      {{ Lang::txt('PLG_MEMBERS_PROJECTS_ADD') }}
    </a>
  </div>
@endif

@if ($isUser)
  {{-- Sub-menu tabs --}}
  <div role="tablist" class="tabs tabs-border mb-4">
    <a role="tab" class="tab tab-active" href="{{ Route::url($base . '&action=all') }}">
      {{ Lang::txt('PLG_MEMBERS_PROJECTS_LIST') }} ({{ $total }})
    </a>
    <a role="tab" class="tab" href="{{ Route::url($base . '&action=updates') }}">
      {{ Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED') }}
      @if ($newcount)
        <span class="badge badge-primary badge-sm ml-1">{{ $newcount }}</span>
      @endif
    </a>
  </div>
@endif

<div>
  {{-- Filter nav --}}
  @if ($isUser)
    @php
      $filterBy = $filters['filterby'] ?? '';
      $isActiveFilter = (!$filterBy || $filterBy === 'active');
      $isArchivedFilter = ($filterBy === 'archived');
    @endphp
    <nav class="mb-4" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
      <div role="tablist" class="tabs tabs-border">
        <a role="tab" class="tab {{ $isActiveFilter ? 'tab-active' : '' }}"
           href="{{ Route::url($base . '&action=all') }}"
           @if($isActiveFilter) aria-selected="true" @endif>
          {{ Lang::txt('PLG_MEMBERS_PROJECTS_FILTER_STATUS_ACTIVE') }}
        </a>
        <a role="tab" class="tab {{ $isArchivedFilter ? 'tab-active' : '' }}"
           href="{{ Route::url($base . '&action=all&filterby=archived') }}"
           @if($isArchivedFilter) aria-selected="true" @endif>
          {{ Lang::txt('PLG_MEMBERS_PROJECTS_FILTER_STATUS_ARCHIVED') }}
        </a>
      </div>
    </nav>
  @endif

  {{-- Invitations --}}
  @if (count($invites))
    <div class="mb-6">
      <h4 class="text-sm font-medium mb-2">
        {{ Lang::txt('PLG_MEMBERS_PROJECTS_INVITED') }}
        <span class="badge badge-sm">{{ count($invites) }}</span>
      </h4>
      <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
          <tbody>
            @foreach ($invites as $invite)
              @php
                $row = new \Components\Projects\Models\Project($invite->projectid);
                $rowUrl = Route::url($row->link());
                $thumbUrl = Route::url($row->link('thumb'));
                $rowTitle = e($row->get('title'));
                $rowAlias = $row->get('alias');
                $acceptUrl = Route::url(
                    'index.php?option=com_projects&alias=' . $invite->alias
                    . '&confirm=' . $invite->invited_code
                    . '&email=' . $invite->invited_email
                );
              @endphp
              <tr>
                <td class="w-12">
                  <a href="{{ $rowUrl }}" title="{{ $rowTitle }} ({{ $rowAlias }})">
                    <img src="{{ $thumbUrl }}" alt="{{ $rowTitle }}" class="size-10 rounded object-cover" />
                  </a>
                </td>
                <td>
                  @if (!$row->isPublic())
                    <span class="badge badge-ghost badge-xs mr-1">{{ Lang::txt('PLG_MEMBERS_PROJECTS_PRIVATE') }}</span>
                  @endif
                  <a class="link link-hover font-medium" href="{{ $rowUrl }}"
                     title="{{ $rowTitle }} ({{ $rowAlias }})">{{ $rowTitle }}</a>
                </td>
                <td class="text-right">
                  <a class="btn btn-success btn-sm" href="{{ $acceptUrl }}">
                    {{ Lang::txt('PLG_MEMBERS_PROJECTS_ACCEPT') }}
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

  {{-- Owned projects --}}
  @if ($which === 'all' && !empty($owned))
    {!! $__view->view('list')
        ->set('option', $option)
        ->set('rows', $owned)
        ->set('which', 'owned')
        ->set('config', $config)
        ->set('user', $user)
        ->set('filters', $filters)
        ->loadTemplate() !!}
  @endif

  {{-- All/other projects --}}
  {!! $__view->view('list')
      ->set('option', $option)
      ->set('rows', $rows)
      ->set('config', $config)
      ->set('user', $user)
      ->set('which', $filters['which'] ?? 'all')
      ->set('filters', $filters)
      ->loadTemplate() !!}
</div>
