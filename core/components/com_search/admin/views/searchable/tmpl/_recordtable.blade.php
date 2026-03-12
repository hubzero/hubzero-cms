{{--
  Solr Search — Document record table partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<table class="admin-table">
  <thead>
    <tr>
      <th scope="col">{{ Lang::txt('ID') }}</th>
      <th scope="col">Type</th>
      <th scope="col">Title</th>
      <th scope="col">Access</th>
      <th scope="col">Owner</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($documents as $document)
      @php
        $ownerDisplay = '';
        if (isset($document['owner']) && $document['owner'] == '') {
            if ($document['owner_type'] == 'user') {
                $user = \Hubzero\User\User::one($document['owner'][0]);
                $ownerDisplay = (isset($user) && is_object($user))
                    ? $user->get('name')
                    : Lang::txt('UNKNOWN');
            } elseif ($document['owner_type'] == 'group') {
                $group = \Hubzero\User\Group::getInstance($document['owner'][0]);
                $ownerDisplay = (isset($group) && is_object($group))
                    ? $group->get('description')
                    : Lang::txt('UNKNOWN');
            }
        } else {
            $ownerDisplay = ($document['owner_type'] ?? '') . ' - ' . Lang::txt('UNKNOWN');
        }
      @endphp
      <tr>
        <td>{{ $document['id'] }}</td>
        <td>{{ $document['hubtype'] }}</td>
        <td>{{ $document['title'][0] ?? '' }}</td>
        <td>{{ $document['access_level'] }}</td>
        <td>{{ $ownerDisplay }}</td>
        <td>
          @if (!in_array($document['id'], $blacklist))
            @php
              $blacklistUrl = Route::url(
                  'index.php?option=' . $option
                  . '&task=addToBlackList'
                  . '&id=' . $document['id']
                  . '&facet=' . $facet
                  . '&limit=' . $pagination->limit
                  . '&limitstart=' . $pagination->limitstart,
                  false, false
              );
            @endphp
            <a href="{{ $blacklistUrl }}" class="btn btn-xs btn-error btn-outline">
              {{ Lang::txt('COM_SEARCH_ADD_BLACKLIST') }}
            </a>
          @else
            <span class="badge badge-ghost">
              {{ Lang::txt('COM_SEARCH_MARKED_FOR_REMOVAL') }}
            </span>
          @endif
        </td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="6">
        {!! $pagination->render() !!}
      </td>
    </tr>
  </tfoot>
</table>
