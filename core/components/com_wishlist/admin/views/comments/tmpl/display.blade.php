{{--
  Wishlist Comments — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wishlist\Helpers\Permissions::getActions('component');
  $sortDir = $filters['sort_Dir'] ?? 'desc';
  $sort    = $filters['sort'] ?? 'id';
  $wishId  = (int) ($filters['wish'] ?? 0);
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}: {{ Lang::txt('COM_WISHLIST_COMMENTS') }}"
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
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      @if ($wishId > 0)
        <tr>
          @php
            $wishlistUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=wishes&wishlist=' . $wishlist->id,
                false, false
            );
          @endphp
          <th colspan="7">
            <a href="{{ $wishlistUrl }}">
              ({{ $wishlist->category }})
              {{ $wishlist->title }}
              &rsaquo;
            </a>
            {{ $wish->subject }}
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
          {!! Html::grid('sort', 'COM_WISHLIST_COMMENT_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_COMMENT', 'comment', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WISHLIST_ADDED_BY', 'added_by', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_WISHLIST_ADDED', 'added', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_STATE', 'status', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'JANONYMOUS', 'anonymous', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $token = Session::getFormToken();

          // State badge
          switch ($row->state) {
              case 1:
                  $stateBadge = 'badge-success';
                  $stateText  = Lang::txt('JPUBLISHED');
                  $stateTask  = 'unpublish';
                  break;
              case 2:
              case 4:
                  $stateBadge = 'badge-ghost';
                  $stateText  = Lang::txt('JTRASHED');
                  $stateTask  = 'publish';
                  break;
              case 3:
                  $stateBadge = 'badge-secondary';
                  $stateText  = Lang::txt('JFLAGGED') ?: 'Flagged';
                  $stateTask  = 'publish';
                  break;
              default:
                  $stateBadge = 'badge-warning';
                  $stateText  = Lang::txt('JUNPUBLISHED');
                  $stateTask  = 'publish';
                  break;
          }

          // Anonymous badge
          if ($row->anonymous) {
              $anonBadge = 'badge-success';
              $anonText  = Lang::txt('JANONYMOUS');
              $anonTask  = 'publicize';
          } else {
              $anonBadge = 'badge-ghost';
              $anonText  = Lang::txt('COM_WISHLIST_NOT_ANONYMOUS');
              $anonTask  = 'anonymize';
          }

          $comment = substr(strip_tags($row->content ?? ''), 0, 50);
          if (strlen($row->content ?? '') >= 50) {
              $comment .= '...';
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->id
              . '&wish=' . ($row->wish ?? $wishId),
              false, false
          );
          $stateUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $stateTask
              . '&id=' . $row->id
              . '&wish=' . $wishId
              . '&' . $token . '=1',
              false, false
          );
          $anonUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $anonTask
              . '&id=' . $row->id
              . '&wish=' . ($row->wish ?? $wishId)
              . '&' . $token . '=1',
              false, false
          );
          $creatorName = $row->creator->get('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
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
            {!! $row->prfx !!}
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $comment }}</a>
            @else
              {{ $comment }}
            @endif
          </td>
          <td class="priority-4">{{ $creatorName }}</td>
          <td class="priority-3">
            <time datetime="{{ $row->get('created') }}">
              {{ $row->get('created') }}
            </time>
          </td>
          <td>
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $stateUrl }}">
                <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
            @endif
          </td>
          <td class="priority-2">
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $anonUrl }}">
                <span class="badge badge-sm {{ $anonBadge }}">{{ $anonText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $anonBadge }}">{{ $anonText }}</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">
          {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
        </td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="wish" value="{{ $wishId }}" />
</x-admin-form>
