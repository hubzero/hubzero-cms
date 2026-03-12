{{--
  User Notes — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'a.review_time';
  $sortDir = $filters['sort_Dir'] ?? 'desc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_NOTES') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    @slot('search')
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm w-60"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_IN_NOTE_TITLE') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    @endslot

    <select name="filter_category_id"
            id="filter_category_id"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}">
      <option value="">{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}</option>
      {!! Html::select('options', Html::category('options', 'com_members'), 'value', 'text', $filters['category_id'] ?? '') !!}
    </select>

    <select name="filter_published"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}">
      <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
      {!! Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $filters['state'] ?? '', true) !!}
    </select>
  </x-admin-filters>

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
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_USER_HEADING', 'u.name', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_SUBJECT_HEADING', 'a.subject', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_MEMBERS_CATEGORY_HEADING', 'c.title', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'JSTATUS', 'a.state', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_REVIEW_HEADING', 'a.review_time', $sortDir, $sort) !!}
          </th>
          <th class="priority-6">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $canChange = User::authorise('core.edit.state', $option);
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );

            $stateClass = match((int) $row->get('state')) {
                1 => 'badge-success',
                0 => 'badge-ghost',
                2 => 'badge-error',
                default => 'badge-ghost',
            };
            $stateText = match((int) $row->get('state')) {
                1 => Lang::txt('JPUBLISHED'),
                0 => Lang::txt('JUNPUBLISHED'),
                2 => Lang::txt('JTRASHED'),
                default => Lang::txt('JUNPUBLISHED'),
            };
          @endphp
          <tr>
            <td class="column-check">
              {!! Html::grid('id', $i, $row->get('id')) !!}
            </td>
            <td>
              @if($row->get('checked_out'))
                {!! Html::grid('checkedout', $i, $row->editor, $row->get('checked_out_time')) !!}
              @endif
              @php
                $memberName = $row->member->get('name') ?: Lang::txt('COM_MEMBERS_UNKNOWN');
              @endphp
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $memberName }}
                </a>
              @else
                {{ $memberName }}
              @endif
            </td>
            <td>
              @if($row->get('subject'))
                {{ $row->get('subject') }}
              @else
                <span class="text-muted-foreground">{{ Lang::txt('COM_MEMBERS_EMPTY_SUBJECT') }}</span>
              @endif
            </td>
            <td class="priority-5">
              {{ $row->category->get('title') }}
            </td>
            <td class="priority-3">
              <span class="badge badge-sm whitespace-nowrap {{ $stateClass }}">{{ $stateText }}</span>
            </td>
            <td>
              @if($row->get('review_time') && $row->get('review_time') != '0000-00-00 00:00:00')
                <time datetime="{{ $row->get('review_time') }}" class="text-sm">
                  {{ $row->get('review_time') }}
                </time>
              @else
                <span class="text-muted-foreground">{{ Lang::txt('COM_MEMBERS_EMPTY_REVIEW') }}</span>
              @endif
            </td>
            <td class="priority-6">{{ (int) $row->get('id') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
