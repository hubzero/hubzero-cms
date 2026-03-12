{{--
  Feedback Quotes — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Feedback\Helpers\Permissions::getActions('quote');
  $sort    = $filters['sort'] ?? 'date';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';

  Toolbar::title(Lang::txt('COM_FEEDBACK'), 'feedback.png');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('quotes');
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
               placeholder="{{ Lang::txt('COM_FEEDBACK_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_FEEDBACK_GO') }}
        </button>
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
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="w-16">
            {!! Html::grid('sort', 'COM_FEEDBACK_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FEEDBACK_COL_AUTHOR', 'fullname', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FEEDBACK_COL_ORGANIZATION', 'org', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_FEEDBACK_COL_QUOTE') }}</th>
          <th class="w-24 text-center">{{ Lang::txt('COM_FEEDBACK_COL_QUOTES') }}</th>
          <th class="w-24 text-center">{{ Lang::txt('COM_FEEDBACK_COL_OK_PUBLISH') }}</th>
          <th>
            {!! Html::grid('sort', 'COM_FEEDBACK_COL_SUBMITTED', 'date', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $quoteText = trim($row->get('quote'));
            if (!$quoteText) {
                $quoteText = trim($row->get('short_quote'));
            }
            if (!$quoteText) {
                $quoteText = trim($row->get('miniquote'));
            }
            if (!$quoteText) {
                $quoteText = Lang::txt('COM_FEEDBACK_BLANK');
            }

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );
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
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('fullname') }}
                </a>
              @else
                {{ $row->get('fullname') }}
              @endif
            </td>
            <td>{{ $row->get('org') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover">
                  {{ \Hubzero\Utility\Str::truncate(strip_tags($quoteText), 100) }}
                </a>
              @else
                {{ \Hubzero\Utility\Str::truncate(strip_tags($quoteText), 100) }}
              @endif
            </td>
            <td class="text-center">
              @if($row->get('notable_quote') == 1)
                <span class="badge badge-sm badge-success">
                  {{ Lang::txt('JYES') }}
                </span>
              @endif
            </td>
            <td class="text-center">
              @if($row->get('publish_ok') == 1)
                <span class="badge badge-sm badge-info">
                  {{ Lang::txt('JYES') }}
                </span>
              @endif
            </td>
            <td>
              @if($row->get('date') && $row->get('date') != '0000-00-00 00:00:00')
                <time datetime="{{ $row->get('date') }}">
                  {{ $row->created('date') }}
                </time>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
