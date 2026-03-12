{{--
  com_languages — Content languages list

  Variables: $items (paginated), $filters (search/published/access/sort/sort_Dir)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo    = \Components\Languages\Helpers\Utilities::getActions();
  $sort     = $filters['sort'] ?? 'a.ordering';
  $sortDir  = $filters['sort_Dir'] ?? 'asc';
  $canEdit  = User::authorise('core.edit', $option);
  $canChange = User::authorise('core.edit.state', $option);
  $saveOrder = ($sort === 'a.ordering');
  $pagination = $items->pagination;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_LANGUAGES_VIEW_LANGUAGES_TITLE') }}"
    icon="langmanager"
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_LANGUAGES_SEARCH_IN_TITLE') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <select name="filter_published"
              class="select select-sm select-bordered"
              aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        {!! Html::select('options',
            \Components\Languages\Helpers\Utilities::publishedOptions(),
            'value', 'text',
            $filters['published'] ?? '',
            true) !!}
      </select>

      <select name="filter_access"
              class="select select-sm select-bordered"
              aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', Html::access('assetgroups'),
            'value', 'text', $filters['access'] ?? '') !!}
      </select>
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
          <th>
            {!! Html::grid('sort', 'JGLOBAL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_LANGUAGES_HEADING_TITLE_NATIVE', 'title_native', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_LANGUAGES_FIELD_LANG_TAG_LABEL', 'lang_code', $sortDir, $sort) !!}
          </th>
          <th class="priority-6">
            {!! Html::grid('sort', 'COM_LANGUAGES_FIELD_LANG_CODE_LABEL', 'sef', $sortDir, $sort) !!}
          </th>
          <th class="priority-6">
            {!! Html::grid('sort', 'COM_LANGUAGES_HEADING_LANG_IMAGE', 'image', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JSTATUS', 'published', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $sortDir, $sort) !!}
            @if ($canChange && $saveOrder)
              {!! Html::grid('order', $items, 'filesave', 'saveorder') !!}
            @endif
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_LANGUAGES_HOMEPAGE', '', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'lang_id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="11">
            {!! $pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($items as $i => $item)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&lang_id=' . (int) $item->lang_id, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     class="checkbox checkbox-sm"
                     data-check-item="{{ $i }}"
                     name="cid[]"
                     value="{{ $item->lang_id }}"
                     aria-label="{{ $item->title }}" />
            </td>
            <td>
              @if ($canEdit)
                <a href="{{ $editUrl }}">{{ $item->title }}</a>
              @else
                {{ $item->title }}
              @endif
            </td>
            <td class="priority-4">{{ $item->title_native }}</td>
            <td>{{ $item->lang_code }}</td>
            <td class="priority-6">{{ $item->sef }}</td>
            <td class="priority-6">{{ $item->image }}</td>
            <td>
              {!! Html::grid('published', $item->published, $i, '', $canChange) !!}
            </td>
            <td>
              @if ($canChange)
                @if ($saveOrder)
                  @if ($sortDir === 'asc')
                    {!! $pagination->orderUpIcon($i, true, 'orderup', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $pagination->orderDownIcon($i, $pagination->total, true, 'orderdown', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @else
                    {!! $pagination->orderUpIcon($i, true, 'orderdown', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $pagination->orderDownIcon($i, $pagination->total, true, 'orderup', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @endif
                @endif
                <input type="text"
                       name="order[]"
                       size="5"
                       value="{{ $item->ordering }}"
                       aria-label="{{ Lang::txt('JGRID_HEADING_ORDERING') }}"
                       {{ $saveOrder ? '' : 'disabled' }}
                       class="input input-bordered input-xs w-14 text-center" />
              @else
                {{ $item->ordering }}
              @endif
            </td>
            <td class="priority-3">{{ $item->access_level }}</td>
            <td class="priority-4">
              @if ($item->home == '1')
                <span class="badge badge-success badge-sm">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-ghost badge-sm">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
            <td class="priority-5">{{ $item->lang_id }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="11">
              <x-empty-state
                  icon="search"
                  :message="Lang::txt('COM_LANGUAGES_NO_LANGUAGES')" />
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</x-admin-form>
