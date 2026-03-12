{{--
  Wiki Revisions — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wiki\Helpers\Permissions::getActions('page');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_PAGE') }}: {{ Lang::txt('COM_WIKI_REVISIONS') }}"
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
  {{-- Page info header --}}
  <table class="admin-table mb-4">
    <tbody>
      <tr>
        <th>{{ Lang::txt('COM_WIKI_COL_TITLE') }}</th>
        <td>{{ $page->title }}</td>
        <th class="priority-2">{{ Lang::txt('COM_WIKI_COL_SCOPE') }}</th>
        <td class="priority-2">
          {{ $page->get('scope') }}:{{ $page->get('scope_id') }}
        </td>
      </tr>
      <tr>
        <th>({{ Lang::txt('COM_WIKI_COL_ID') }}) {{ Lang::txt('COM_WIKI_COL_PAGENAME') }}</th>
        <td>({{ $page->get('id') }}) {{ $page->get('pagename') }}</td>
        <th class="priority-2">{{ Lang::txt('COM_WIKI_COL_PATH') }}</th>
        <td class="priority-2">{{ $page->get('path') }}</td>
      </tr>
    </tbody>
  </table>

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
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WIKI_COL_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WIKI_COL_REVISION', 'revision', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WIKI_COL_EDIT_SUMMARY', 'summary', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WIKI_COL_APPROVED', 'approved', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WIKI_COL_MINOR_EDIT', 'minor_edit', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_WIKI_COL_CREATED', 'created', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_WIKI_COL_CREATOR', 'created_by', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          switch ($row->get('approved')) {
              case 2:
                  $approveBadge = 'badge-ghost';
                  $approveText  = Lang::txt('COM_WIKI_STATE_TRASHED');
                  $approveTask  = 0;
                  break;
              case 1:
                  $approveBadge = 'badge-success';
                  $approveText  = Lang::txt('COM_WIKI_STATE_APPROVED');
                  $approveTask  = 0;
                  break;
              default:
                  $approveBadge = 'badge-warning';
                  $approveText  = Lang::txt('COM_WIKI_STATE_NOT_APPROVED');
                  $approveTask  = 1;
                  break;
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id')
              . '&pageid=' . $filters['pageid']
              . '&' . Session::getFormToken() . '=1',
              false, false
          );

          $approveUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=approve&id=' . $row->get('id')
              . '&pageid=' . $filters['pageid']
              . '&approve=' . $approveTask
              . '&' . Session::getFormToken() . '=1',
              false, false
          );

          $revNum = Lang::txt(
              'COM_WIKI_REVISION_NUM',
              e($row->get('version'))
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
          <td class="priority-4">{{ $row->get('id') }}</td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $revNum }}</a>
            @else
              {{ $revNum }}
            @endif
          </td>
          <td class="priority-5">
            {{ trim($row->get('summary', ''))
                ? e($row->get('summary'))
                : Lang::txt('COM_WIKI_NONE') }}
          </td>
          <td>
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $approveUrl }}">
                <span class="badge badge-sm {{ $approveBadge }}">{{ $approveText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $approveBadge }}">{{ $approveText }}</span>
            @endif
          </td>
          <td class="priority-4">
            @if ($row->get('minor_edit'))
              <span class="badge badge-sm badge-info">{{ Lang::txt('JYES') }}</span>
            @else
              <span class="badge badge-sm badge-ghost">{{ Lang::txt('JNO') }}</span>
            @endif
          </td>
          <td class="priority-3">
            <time datetime="{{ $row->get('created') }}">
              {{ $row->created('time') . ' ' . $row->created('date') }}
            </time>
          </td>
          <td class="priority-2">
            {{ $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) }}
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="8">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="pageid" value="{{ $filters['pageid'] }}" />
</x-admin-form>
