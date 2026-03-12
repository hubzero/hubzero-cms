{{--
  Who's Online — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS_WHOSONLINE'), 'user');

  $siteUserCount  = 0;
  $adminUserCount = 0;
  foreach ($rows as $row) {
      if ($row->client_id == 0) {
          $siteUserCount++;
      } else {
          $adminUserCount++;
      }
  }

  $editAuthorized = User::authorise('core.manage', 'com_members');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-base-100 rounded-box border border-base-300 p-4 text-center">
    <div class="text-3xl font-bold text-primary">{{ $siteUserCount }}</div>
    <div class="text-sm text-muted-foreground mt-1">{{ Lang::txt('COM_MEMBERS_WHOSONLINE_SITE') }}</div>
  </div>
  <div class="bg-base-100 rounded-box border border-base-300 p-4 text-center">
    <div class="text-3xl font-bold text-secondary">{{ $adminUserCount }}</div>
    <div class="text-sm text-muted-foreground mt-1">{{ Lang::txt('COM_MEMBERS_WHOSONLINE_ADMIN') }}</div>
  </div>
</div>

<div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
  <table class="admin-table">
    <thead>
      <tr>
        <th>{{ Lang::txt('COM_MEMBERS_WHOSONLINE_COL_USER') }}</th>
        <th>{{ Lang::txt('COM_MEMBERS_WHOSONLINE_COL_LOCATION') }}</th>
        <th>{{ Lang::txt('COM_MEMBERS_WHOSONLINE_COL_ACTIVITY') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $row)
        @php
          $user = User::getInstance($row->username);
          $clientInfo = \Hubzero\Base\ClientManager::client($row->client_id);
        @endphp
        <tr>
          <td>
            @if($editAuthorized)
              @php
                $editLink = Route::url(
                    'index.php?option=com_members&controller=members&task=edit&id=' . $row->userid, false
                );
              @endphp
              <a href="{!! $editLink !!}" class="link link-hover text-primary font-medium">
                {{ $user->get('name') }}
                <span class="text-muted-foreground">[{{ $user->get('username') }}]</span>
              </a>
            @else
              {{ $user->get('name') }}
              <span class="text-muted-foreground">[{{ $user->get('username') }}]</span>
            @endif
          </td>
          <td>{{ ucfirst($clientInfo->name) }}</td>
          <td>
            {{ Lang::txt('COM_MEMBERS_WHOSONLINE_AGO', (time() - $row->time) / 3600.0) }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="text-center text-muted-foreground py-8">
            {{ Lang::txt('COM_MEMBERS_WHOSONLINE_NO_RESULTS') }}
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
