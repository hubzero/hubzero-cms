{{--
  Browse groups with search, filtering, and sorting.

  Variables from controller:
    $title         — string: page title
    $option        — string: component option
    $groups        — array: group objects
    $total         — int: total groups count
    $filters       — array: active filter values
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css()->js('browse');

  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
  $newUrl    = Route::url('index.php?option=' . $option . '&task=new');

  // Build filter query strings for sort links
  $sortFltrs  = ($filters['index'] ?? '')     ? '&index=' . e($filters['index'])         : '';
  $sortFltrs .= ($filters['policy'] ?? '')    ? '&policy=' . e($filters['policy'])       : '';
  $sortFltrs .= ($filters['search'] ?? '')    ? '&search=' . e($filters['search'])       : '';
  $sortFltrs .= ($filters['published'] ?? '') ? '&published=' . e($filters['published']) : '';

  $filterFltrs  = ($filters['index'] ?? '')     ? '&index=' . e($filters['index'])         : '';
  $filterFltrs .= ($filters['sortby'] ?? '')    ? '&sortby=' . e($filters['sortby'])       : '';
  $filterFltrs .= ($filters['search'] ?? '')    ? '&search=' . e($filters['search'])       : '';
  $filterFltrs .= ($filters['published'] ?? '') ? '&published=' . e($filters['published']) : '';

  $titleSortUrl = Route::url(
      'index.php?option=' . $option . '&task=browse&sortby=title' . $sortFltrs
  );
  $aliasSortUrl = Route::url(
      'index.php?option=' . $option . '&task=browse&sortby=alias' . $sortFltrs
  );
@endphp

<x-page-container :title="Lang::txt('COM_GROUPS') . ': ' . $title">
  @slot('actions')
    @if(User::authorise('core.create', $option))
      <a class="btn btn-sm btn-primary" href="{{ $newUrl }}">
        {{ Lang::txt('COM_GROUPS_NEW') }}
      </a>
    @endif
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_TITLE')">
      <p class="text-sm text-base-content/70 mb-2">
        {{ Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_ONE') }}
      </p>
      <p class="text-sm text-base-content/70 mb-2">
        {{ Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_TWO') }}
      </p>
      <p class="text-sm text-base-content/70">
        {{ Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_THREE') }}
      </p>
    </x-sidebar-card>

    @if(Component::isEnabled('com_members'))
      <x-sidebar-card :title="Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_TITLE')">
        <p class="text-sm text-base-content/70">
          @php $membersUrl = Route::url('index.php?option=com_members'); @endphp
          {!! Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', $membersUrl) !!}
        </p>
      </x-sidebar-card>
    @endif
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <x-search-bar :action="$browseUrl"
                :query="$filters['search'] ?? ''"
                name="search"
                :placeholder="Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER')"
                :label="Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP')">
    <input type="hidden" name="sortby" value="{{ e($filters['sortby'] ?? '') }}" />
    <input type="hidden" name="policy" value="{{ e($filters['policy'] ?? '') }}" />
    <input type="hidden" name="index" value="{{ e($filters['index'] ?? '') }}" />
    <input type="hidden" name="published" value="{{ e($filters['published'] ?? '') }}" />
  </x-search-bar>

  {{-- Sort + Filter bar --}}
  <div class="flex flex-wrap gap-4 items-center mb-4">
    <div class="flex gap-1 items-center text-sm">
      <span class="text-base-content/60">{{ Lang::txt('COM_GROUPS_BROWSE_SORT') }}:</span>
      <a class="btn btn-xs {{ ($filters['sortby'] ?? '') == 'title' ? 'btn-active' : 'btn-ghost' }}"
         href="{{ $titleSortUrl }}">
        {{ Lang::txt('COM_GROUPS_GROUP_TITLE') }}
      </a>
      <a class="btn btn-xs {{ ($filters['sortby'] ?? '') == 'alias' ? 'btn-active' : 'btn-ghost' }}"
         href="{{ $aliasSortUrl }}">
        {{ Lang::txt('COM_GROUPS_GROUP_ALIAS') }}
      </a>
    </div>

    <form action="{{ $browseUrl }}" method="get" class="flex gap-2 items-center text-sm ml-auto">
      <input type="hidden" name="search" value="{{ e($filters['search'] ?? '') }}" />
      <input type="hidden" name="sortby" value="{{ e($filters['sortby'] ?? '') }}" />
      <input type="hidden" name="index" value="{{ e($filters['index'] ?? '') }}" />

      <label for="filter-published" class="text-base-content/60">
        {{ Lang::txt('COM_GROUPS_BROWSE_STATE') }}:
      </label>
      <select name="published" id="filter-published"
              class="select select-bordered select-xs"
              data-submit-on-change>
        <option value="1" @selected(($filters['published'] ?? '') == 1)>
          {{ Lang::txt('COM_GROUPS_BROWSE_STATE_ACTIVE') }}
        </option>
        <option value="2" @selected(($filters['published'] ?? '') == 2)>
          {{ Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED') }}
        </option>
      </select>

      <label for="filter-policy" class="text-base-content/60">
        {{ Lang::txt('COM_GROUPS_BROWSE_POLICY') }}:
      </label>
      <select name="policy" id="filter-policy"
              class="select select-bordered select-xs"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_GROUPS_BROWSE_POLICY_ALL') }}</option>
        <option value="open" @selected(($filters['policy'] ?? '') == 'open')>
          {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN') }}
        </option>
        <option value="restricted" @selected(($filters['policy'] ?? '') == 'restricted')>
          {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED') }}
        </option>
        <option value="invite" @selected(($filters['policy'] ?? '') == 'invite')>
          {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY') }}
        </option>
        <option value="closed" @selected(($filters['policy'] ?? '') == 'closed')>
          {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED') }}
        </option>
      </select>
    </form>
  </div>

  {{-- Group list --}}
  @if($groups)
    <div class="space-y-3">
      @foreach($groups as $group)
        {!! $__view->view('_group')
            ->set('group', $group)
            ->display() !!}
      @endforeach
    </div>

    @php
      $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
      $pageNav->setAdditionalUrlParam('index', $filters['index'] ?? '');
      $pageNav->setAdditionalUrlParam('sortby', $filters['sortby'] ?? '');
      $pageNav->setAdditionalUrlParam('policy', $filters['policy'] ?? '');
      $pageNav->setAdditionalUrlParam('search', $filters['search'] ?? '');
      $pageNav->setAdditionalUrlParam('published', $filters['published'] ?? '');
    @endphp
    {!! $pageNav->render() !!}
  @else
    <x-empty-state :title="Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS')" />
  @endif
</x-page-container>
