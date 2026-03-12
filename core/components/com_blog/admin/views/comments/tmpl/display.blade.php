{{--
  Blog Comments — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Blog\Admin\Helpers\Permissions::getActions('entry');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  // Custom toolbar — comments list has no publish/unpublish or preferences
  Toolbar::title(Lang::txt('COM_BLOG_TITLE') . ': ' . Lang::txt('COM_BLOG_COL_COMMENTS'), 'blog');
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_BLOG_CONFIRM_DELETE');
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  Toolbar::spacer();
  Toolbar::help('comments');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('COM_BLOG_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_BLOG_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        @if($entry->get('id'))
          <tr>
            <th colspan="6" class="text-sm font-normal text-muted-foreground">
              ({{ $entry->get('scope') }})
              {{ $entry->get('title') }}
            </th>
          </tr>
        @endif
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_COMMENT', 'content', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_CREATOR', 'created_by', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_ANONYMOUS', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @for($i = 0, $n = count($rows); $i < $n; $i++)
          @php
            $row = $rows[$i];

            if (!$row->get('anonymous')) {
                $calt  = Lang::txt('JOFF');
                $ccls  = 'badge-ghost';
                $anonState = 1;
            } else {
                $calt  = Lang::txt('JON');
                $ccls  = 'badge-success';
                $anonState = 0;
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              {!! $row->get('treename') !!}
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), 90) }}
                </a>
              @else
                {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), 90) }}
              @endif
            </td>
            <td>{{ $row->creator->get('name') }}</td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=anonymous&state=' . $anonState . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                <span class="badge badge-sm {{ $ccls }}">{{ $calt }}</span>
              </a>
            </td>
            <td>
              <time datetime="{{ $row->get('created') }}">
                {{ $row->created('date') }}
              </time>
            </td>
          </tr>
        @endfor
      </tbody>
    </table>
  </div>

  <input type="hidden" name="entry_id" value="{{ $filters['entry_id'] }}" />
</x-admin-form>
