{{--
  Wishlist Wishes — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wishlist\Helpers\Permissions::getActions('wish');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'subject';
  $hasWishlist = (bool) $wishlist->id;
  $colspan = $hasWishlist ? 8 : 9;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}: {{ Lang::txt('COM_WISHLIST_WISHES') }}"
    icon="wishlist"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm"
               placeholder="{{ Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}"
               data-submit-on-change />
      @endslot

      <label for="filter-status" class="sr-only">{{ Lang::txt('COM_WISHLIST_STATUS') }}</label>
      <select name="status"
              id="filter-status"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="all"
                @selected(($filters['status'] ?? 'all') == 'all')>
          {{ Lang::txt('COM_WISHLIST_STATE_ALL') }}
        </option>
        <option value="granted"
                @selected(($filters['status'] ?? '') == 'granted')>
          {{ Lang::txt('COM_WISHLIST_STATE_GRANTED') }}
        </option>
        <option value="open"
                @selected(($filters['status'] ?? '') == 'open')>
          {{ Lang::txt('COM_WISHLIST_STATE_OPEN') }}
        </option>
        <option value="accepted"
                @selected(($filters['status'] ?? '') == 'accepted')>
          {{ Lang::txt('COM_WISHLIST_STATE_ACCEPTED') }}
        </option>
        <option value="pending"
                @selected(($filters['status'] ?? '') == 'pending')>
          {{ Lang::txt('COM_WISHLIST_STATE_PENDING') }}
        </option>
        <option value="rejected"
                @selected(($filters['status'] ?? '') == 'rejected')>
          {{ Lang::txt('COM_WISHLIST_STATE_REJECTED') }}
        </option>
        <option value="withdrawn"
                @selected(($filters['status'] ?? '') == 'withdrawn')>
          {{ Lang::txt('COM_WISHLIST_STATE_WITHDRAWN') }}
        </option>
        <option value="deleted"
                @selected(($filters['status'] ?? '') == 'deleted')>
          {{ Lang::txt('COM_WISHLIST_STATE_DELETED') }}
        </option>
        <option value="useraccepted"
                @selected(($filters['status'] ?? '') == 'useraccepted')>
          {{ Lang::txt('COM_WISHLIST_STATE_USER_ACCEPTED') }}
        </option>
        <option value="private"
                @selected(($filters['status'] ?? '') == 'private')>
          {{ Lang::txt('COM_WISHLIST_STATE_PRIVATE') }}
        </option>
        <option value="public"
                @selected(($filters['status'] ?? '') == 'public')>
          {{ Lang::txt('COM_WISHLIST_STATE_PUBLIC') }}
        </option>
        <option value="assigned"
                @selected(($filters['status'] ?? '') == 'assigned')>
          {{ Lang::txt('COM_WISHLIST_STATE_ASSIGNED') }}
        </option>
      </select>
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      @if ($hasWishlist)
        <tr>
          <th colspan="{{ $colspan }}">
            ({{ $wishlist->category }})
            {{ $wishlist->title }}
          </th>
        </tr>
      @endif
      <tr>
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WISHLIST_WISH_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_TITLE', 'subject', $sortDir, $sort) !!}
        </th>
        @if (!$hasWishlist)
          <th scope="col">
            {!! Html::grid('sort', 'COM_WISHLIST_WISHLIST_ID', 'wishlist', $sortDir, $sort) !!}
          </th>
        @endif
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WISHLIST_PROPOSED_BY', 'proposed_by', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WISHLIST_PROPOSED', 'proposed', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_STATUS', 'status', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_WISHLIST_ACCESS', 'private', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_WISHLIST_COMMENTS', 'numreplies', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $token = Session::getFormToken();

          // Status badge
          switch ($row->status) {
              case 1:
                  $statusBadge = 'badge-success';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_GRANTED');
                  $statusTask  = 'pending';
                  break;
              case 2:
                  $statusBadge = 'badge-ghost';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_DELETED');
                  $statusTask  = 'grant';
                  break;
              case 3:
                  $statusBadge = 'badge-error';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_REJECTED');
                  $statusTask  = 'pending';
                  break;
              case 4:
                  $statusBadge = 'badge-warning';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
                  $statusTask  = 'pending';
                  break;
              case 6:
                  $statusBadge = 'badge-info';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_ACCEPTED');
                  $statusTask  = 'grant';
                  break;
              case 7:
                  $statusBadge = 'badge-secondary';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_FLAGGED');
                  $statusTask  = 'pending';
                  break;
              default:
                  $statusBadge = 'badge-outline';
                  $statusText  = Lang::txt('COM_WISHLIST_STATUS_PENDING');
                  $statusTask  = 'grant';
                  break;
          }

          // Access badge
          if ($row->private) {
              $accessBadge = 'badge-error';
              $accessText  = Lang::txt('COM_WISHLIST_PRIVATE');
              $accessTask  = 'accesspublic';
          } else {
              $accessBadge = 'badge-info';
              $accessText  = Lang::txt('COM_WISHLIST_PUBLIC');
              $accessTask  = 'accessregistered';
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->id,
              false, false
          );
          $statusUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $statusTask
              . '&id=' . $row->id
              . '&' . $token . '=1',
              false, false
          );
          $accessUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $accessTask
              . '&id=' . $row->id
              . '&' . $token . '=1',
              false, false
          );
          $commentsUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=comments&wish=' . $row->id,
              false, false
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox"
                   name="id[]"
                   id="cb{{ $i }}"
                   value="{{ $row->id }}"
                   class="checkbox checkbox-sm"
                   aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                   data-check-item />
          </td>
          <td class="priority-5">{{ $row->id }}</td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">
                {{ $row->subject }}
              </a>
            @else
              {{ $row->subject }}
            @endif
          </td>
          @if (!$hasWishlist)
            <td>{{ $row->wishlist }}</td>
          @endif
          <td class="priority-4">
            {{ $row->proposer->get('name', Lang::txt('(unknown)')) }}
          </td>
          <td class="priority-5">
            <time datetime="{{ $row->proposed }}">{{ $row->proposed }}</time>
          </td>
          <td>
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $statusUrl }}">
                <span class="badge badge-sm {{ $statusBadge }}">{{ $statusText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $statusBadge }}">{{ $statusText }}</span>
            @endif
          </td>
          <td class="priority-3">
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $accessUrl }}">
                <span class="badge badge-sm {{ $accessBadge }}">{{ $accessText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $accessBadge }}">{{ $accessText }}</span>
            @endif
          </td>
          <td class="priority-2">
            <a href="{{ $commentsUrl }}">
              {{ $row->comments()->total() }}
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="{{ $colspan }}">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="wishlist" value="{{ $filters['wishlist'] ?? 0 }}" />
  <input type="hidden" name="cid" value="{{ $filters['wishlist'] ?? 0 }}" />
</x-admin-form>
