{{--
  Wiki Comments — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wiki\Helpers\Permissions::getActions('comment');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_PAGE') }}: {{ Lang::txt('COM_WIKI_COMMENTS') }}"
    icon="wiki"
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
               class="input input-bordered input-sm"
               placeholder="{{ Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}"
               data-submit-on-change />
      @endslot
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th colspan="7">
          ({{ $page->get('pagename', '') }})
          {{ $page->get('title', '') }}
        </th>
      </tr>
      <tr>
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WIKI_COL_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WIKI_COL_COMMENT', 'content', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_WIKI_COL_CREATOR', 'created_by', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WIKI_COL_ANONYMOUS', 'state', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_WIKI_COL_STATE', 'status', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WIKI_COL_CREATED', 'created', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          // Anonymous toggle
          if (!$row->get('anonymous')) {
              $anonText  = Lang::txt('JOFF');
              $anonBadge = 'badge-ghost';
              $anonState = 1;
          } else {
              $anonText  = Lang::txt('JON');
              $anonBadge = 'badge-info';
              $anonState = 0;
          }

          // State badge
          switch ($row->get('state')) {
              case 2:
                  $stateBadge = 'badge-ghost';
                  $stateText  = Lang::txt('JTRASHED');
                  $stateTask  = 'publish';
                  break;
              case 1:
                  $stateBadge = 'badge-error';
                  $stateText  = Lang::txt('COM_WIKI_STATE_ABUSIVE');
                  $stateTask  = 'publish';
                  break;
              default:
                  $stateBadge = 'badge-success';
                  $stateText  = Lang::txt('JPUBLISHED');
                  $stateTask  = 'unpublish';
                  break;
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id')
              . '&page_id=' . $filters['page_id']
              . '&' . Session::getFormToken() . '=1',
              false, false
          );

          $anonUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=anonymous&state=' . $anonState
              . '&id=' . $row->get('id')
              . '&pageid=' . $filters['page_id']
              . '&' . Session::getFormToken() . '=1',
              false, false
          );

          $stateUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $stateTask
              . '&id=' . $row->get('id')
              . '&pageid=' . $filters['page_id']
              . '&' . Session::getFormToken() . '=1',
              false, false
          );

          $ctextTrunc = \Hubzero\Utility\Str::truncate(
              e($row->get('ctext')),
              90
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox"
                   name="id[]"
                   id="cb{{ $i }}"
                   value="{{ $row->get('id') }}"
                   class="checkbox checkbox-sm"
                   aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                   data-check-item />
          </td>
          <td class="priority-5">{{ $row->get('id') }}</td>
          <td>
            {!! $row->get('treename') !!}
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $ctextTrunc }}</a>
            @else
              {{ $ctextTrunc }}
            @endif
          </td>
          <td class="priority-3">
            {{ $row->creator->get('name') }}
          </td>
          <td class="priority-5">
            <a href="{{ $anonUrl }}">
              <span class="badge badge-sm {{ $anonBadge }}">{{ $anonText }}</span>
            </a>
          </td>
          <td class="priority-2">
            <a href="{{ $stateUrl }}">
              <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
            </a>
          </td>
          <td class="priority-4">
            <time datetime="{{ $row->created() }}">
              {{ $row->created() }}
            </time>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">
          {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
        </td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="page_id" value="{{ $filters['page_id'] }}" />
</x-admin-form>
