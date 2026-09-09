{{--
  Group Wiki Dashboard — recent wiki activity table.

  Variables from plugin:
    $option — component option
    $rows   — array/collection of wiki page version rows
              (pagename, scope, title, version, created_by, created)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
@endphp

@if ($rows)
  <div class="overflow-x-auto">
    <table class="table table-zebra w-full" id="wiki-list">
      <thead>
        <tr>
          <th class="w-24">{{ Lang::txt('Action') }}</th>
          <th>{{ Lang::txt('Page') }}</th>
          <th>{{ Lang::txt('Author') }}</th>
          <th class="w-40">{{ Lang::txt('Date') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $row)
          @php
            $author = Lang::txt('WIKI_AUTHOR_UNKNOWN');
            $user   = User::getInstance($row->created_by);
            if (is_object($user) && $user->get('name')) {
                $author = $user->get('name');
            }

            $isCreated = ($row->version <= 1);
            $actionText  = $isCreated
                ? Lang::txt('WIKI_CREATED')
                : Lang::txt('WIKI_EDITED');
            $badgeClass  = $isCreated ? 'badge-info' : 'badge-ghost';

            $pageUrl   = Route::url(
                'index.php?option=' . $option
                . '&pagename=' . $row->pagename
                . '&scope=' . $row->scope
            );
            $authorUrl = Route::url(
                'index.php?option=com_members&id=' . $row->created_by
            );
          @endphp
          <tr>
            <td>
              <span class="badge badge-sm {{ $badgeClass }}">{{ $actionText }}</span>
            </td>
            <td>
              <a class="link link-hover" href="{{ $pageUrl }}">
                {{ stripslashes($row->title) }}
              </a>
            </td>
            <td>
              <a class="link link-hover" href="{{ $authorUrl }}">
                {{ $author }}
              </a>
            </td>
            <td class="text-sm text-base-content/60">
              {{ Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@else
  <p class="text-base-content/60 py-4">{{ Lang::txt('PLG_GROUPS_WIKI_NO_RESULTS_FOUND') }}</p>
@endif
