{{--
  Wishlist Lists — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wishlist\Helpers\Permissions::getActions('wishlist');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'title';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}"
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

      <label for="filter-category" class="sr-only">{{ Lang::txt('COM_WISHLIST_CATEGORY') }}</label>
      <select name="category"
              id="filter-category"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_WISHLIST_SELECT_CATEGORY') }}</option>
        <option value="general"
                @selected(($filters['category'] ?? '') == 'general')>
          {{ Lang::txt('COM_WISHLIST_CATEGORY_GENERAL') }}
        </option>
        <option value="group"
                @selected(($filters['category'] ?? '') == 'group')>
          {{ Lang::txt('COM_WISHLIST_CATEGORY_GROUP') }}
        </option>
        <option value="resource"
                @selected(($filters['category'] ?? '') == 'resource')>
          {{ Lang::txt('COM_WISHLIST_CATEGORY_RESOURCE') }}
        </option>
      </select>
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_TITLE', 'title', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WISHLIST_STATE', 'state', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_WISHLIST_ACCESS', 'public', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4" colspan="2">
          {!! Html::grid('sort', 'COM_WISHLIST_CATEGORY', 'category', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {{ Lang::txt('COM_WISHLIST_WISHES') }}
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
                  $stateBadge = 'badge-ghost';
                  $stateText  = Lang::txt('JTRASHED');
                  $stateTask  = 'publish';
                  break;
              default:
                  $stateBadge = 'badge-warning';
                  $stateText  = Lang::txt('JUNPUBLISHED');
                  $stateTask  = 'publish';
                  break;
          }

          // Access badge
          if (!$row->public) {
              $accessBadge = 'badge-error';
              $accessText  = Lang::txt('COM_WISHLIST_PRIVATE');
              $accessTask  = 'accessregistered';
          } else {
              $accessBadge = 'badge-info';
              $accessText  = Lang::txt('COM_WISHLIST_PUBLIC');
              $accessTask  = 'accesspublic';
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->id,
              false, false
          );
          $stateUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $stateTask
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
          $wishesUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=wishes&wishlist=' . $row->id,
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
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">
                {{ $row->title }}
              </a>
            @else
              {{ $row->title }}
            @endif
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
          <td class="priority-3">
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $accessUrl }}">
                <span class="badge badge-sm {{ $accessBadge }}">{{ $accessText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $accessBadge }}">{{ $accessText }}</span>
            @endif
          </td>
          <td class="priority-4">
            {{ $row->category }}
          </td>
          <td class="priority-4">
            {{ $row->referenceid }}
          </td>
          <td class="priority-2">
            <a href="{{ $wishesUrl }}">
              {{ $row->wishes()->total() }} {{ Lang::txt('COM_WISHLIST_LIST_WISHES') }}
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

</x-admin-form>
