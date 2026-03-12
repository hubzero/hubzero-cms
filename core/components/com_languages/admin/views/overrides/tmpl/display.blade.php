{{--
  com_languages — Language overrides list

  Variables:
    $items (assoc array key=>text, paginated), $filters (search/language_client/sort/sort_Dir)
    $languages (select options), $pagination (Pagination object)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo   = \Components\Languages\Helpers\Utilities::getActions();
  $sort    = $filters['sort'] ?? 'key';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $canEdit = User::authorise('core.edit', $option);
  $langLabel  = $filters['language'] ?? '';
  $clientLabel = ($filters['client'] ?? 'site') === 'site'
      ? Lang::txt('JSITE')
      : Lang::txt('JADMINISTRATOR');

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_TITLE') }}"
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
               placeholder="{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <select name="filter_language_client"
              class="select select-sm select-bordered"
              aria-label="{{ Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL') }}"
              data-submit-on-change>
        {!! Html::select('options', $languages, null, 'text', $filters['language_client'] ?? '') !!}
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
            {!! Html::grid('sort', 'COM_LANGUAGES_VIEW_OVERRIDES_KEY', 'key', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_LANGUAGES_VIEW_OVERRIDES_TEXT', 'text', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">{{ Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL') }}</th>
          <th>{{ Lang::txt('JCLIENT') }}</th>
          <th class="priority-6">{{ Lang::txt('COM_LANGUAGES_HEADING_NUM') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            {!! $pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php $i = 0; @endphp
        @forelse ($items as $key => $text)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $key, false
            );
          @endphp
          <tr>
            <td class="column-check">
              {!! Html::grid('id', $i, $key) !!}
            </td>
            <td>
              @if ($canEdit)
                <a id="key[{{ $key }}]" href="{{ $editUrl }}">{{ $key }}</a>
              @else
                {{ $key }}
              @endif
            </td>
            <td class="priority-3">
              <span id="string[{{ $key }}]">{{ $text }}</span>
            </td>
            <td class="priority-4">{{ $langLabel }}</td>
            <td>{{ $clientLabel }}</td>
            <td class="priority-6">{{ $pagination->getRowOffset($i) }}</td>
          </tr>
          @php $i++; @endphp
        @empty
          <tr>
            <td colspan="6">
              <x-empty-state
                  icon="search"
                  :message="Lang::txt('COM_LANGUAGES_NO_OVERRIDES')" />
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="filter_order"     value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />

</x-admin-form>
