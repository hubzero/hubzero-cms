{{--
  Member Projects — project list sub-view with sortable headers.

  Variables (set by parent view):
    $rows    — project rows
    $which   — list type (all/owned/other/group)
    $config  — component config
    $user    — member profile object
    $filters — filters array (sortby, sortdir)
    $option  — component option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $isUser = User::get('id') == $user->get('id');
  $sortbyDir = ($filters['sortdir'] ?? 'ASC') === 'ASC' ? 'DESC' : 'ASC';

  $title = match ($which) {
      'group' => Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_GROUP'),
      'owned' => Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_OWNED'),
      'other' => Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_OTHER'),
      default => Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_ALL'),
  };

  $sortBase = 'index.php?option=com_members&id=' . $user->get('id')
      . '&active=projects&action=all&sortdir=' . $sortbyDir;
@endphp

<div class="overflow-x-auto mb-6">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm font-semibold mb-2">
      {{ $title }} <span class="text-base-content/50">({{ count($rows) }})</span>
    </caption>
    @if (count($rows) > 0)
      <thead>
        <tr>
          <th class="w-12"></th>
          <th>
            <a class="link link-hover {{ ($filters['sortby'] ?? '') === 'title' ? 'font-bold' : '' }}"
               href="{{ Route::url($sortBase . '&sortby=title') }}">
              {{ Lang::txt('PLG_MEMBERS_PROJECTS_TITLE') }}
            </a>
          </th>
          @if ($which === 'owned')
            <th>
              <a class="link link-hover {{ ($filters['sortby'] ?? '') === 'status' ? 'font-bold' : '' }}"
                 href="{{ Route::url($sortBase . '&sortby=status') }}">
                {{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS') }}
              </a>
            </th>
          @endif
          @if ($isUser)
            <th>
              <a class="link link-hover {{ ($filters['sortby'] ?? '') === 'role' ? 'font-bold' : '' }}"
                 href="{{ Route::url($sortBase . '&sortby=role') }}">
                {{ Lang::txt('PLG_MEMBERS_PROJECTS_MY_ROLE') }}
              </a>
            </th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $row)
          @php
            $role = $row->access('manager')
                ? Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_MANAGER')
                : Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_COLLABORATOR');

            if ($row->access('readonly') && !$row->isArchived()) {
                $role = Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_REVIEWER');
            }

            $setup = $row->inSetup();
            $rowUrl = Route::url($row->link());
            $rowTitle = e($row->get('title'));
            $rowAlias = $row->get('alias');
          @endphp
          <tr>
            <td class="w-12">
              <div class="relative">
                <a href="{{ $rowUrl }}" title="{{ $rowTitle }} ({{ $rowAlias }})">
                  <img src="{{ Route::url($row->link('thumb')) }}"
                       alt="{{ $rowTitle }}" class="size-10 rounded object-cover" />
                </a>
                @if ($isUser && $row->get('newactivity') && $row->isActive() && !$setup)
                  <span class="badge badge-primary badge-xs absolute -top-1 -right-1">
                    {{ $row->get('newactivity') }}
                  </span>
                @endif
              </div>
            </td>
            <td>
              <a class="link link-hover font-medium" href="{{ $rowUrl }}"
                 title="{{ $rowTitle }} ({{ $rowAlias }})">{{ $rowTitle }}</a>
              @if ($which !== 'owned')
                @php
                  $ownerDisplay = $row->groupOwner()
                      ? $row->groupOwner('description')
                      : $row->owner('name');
                @endphp
                <span class="block text-xs text-base-content/50">{{ $ownerDisplay }}</span>
              @endif
            </td>
            @if ($which === 'owned')
              <td>
                @if ($row->access('owner'))
                  @if ($row->isActive())
                    <a class="badge badge-success badge-sm" href="{{ $rowUrl }}"
                       title="{{ Lang::txt('PLG_MEMBERS_PROJECTS_GO_TO_PROJECT') }}">
                      {{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_ACTIVE') }}
                    </a>
                  @elseif ($row->inSetup())
                    <a class="badge badge-warning badge-sm" href="{{ Route::url($row->link('setup')) }}"
                       title="{{ Lang::txt('PLG_MEMBERS_PROJECTS_CONTINUE_SETUP') }}">
                      {{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_SETUP') }}
                    </a>
                  @endif
                @endif
                @if ($row->isInactive())
                  <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_SUSPENDED') }}</span>
                @elseif ($row->isPending())
                  <span class="badge badge-warning badge-sm">{{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_PENDING') }}</span>
                @elseif ($row->isArchived())
                  <span class="badge badge-ghost badge-sm">{{ Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_ARCHIVED') }}</span>
                @endif
              </td>
            @endif
            @if ($isUser)
              <td>
                @php
                  $roleBadge = match(true) {
                      $row->access('manager') => 'badge-primary',
                      $row->access('readonly') && !$row->isArchived() => 'badge-accent',
                      default => 'badge-info',
                  };
                @endphp
                <span class="badge {{ $roleBadge }} badge-sm">{{ $role }}</span>
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    @else
      <tbody>
        <tr>
          <td colspan="4" class="text-center text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_PROJECTS_NO_PROJECTS') }}
          </td>
        </tr>
      </tbody>
    @endif
  </table>
</div>
