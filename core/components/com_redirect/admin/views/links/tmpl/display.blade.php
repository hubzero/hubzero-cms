{{--
  Redirect Links — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo   = \Components\Redirect\Helpers\Redirect::getActions();
  $sort    = $filters['sort'] ?? 'created_date';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
  $state   = $filters['state'] ?? '';
  $type    = $filters['type'] ?? 'redirect';
  $root    = Request::root();

  $canEdit   = User::authorise('core.edit', $option);
  $canChange = User::authorise('core.edit.state', $option);
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_REDIRECT_MANAGER_LINKS') }}"
    icon="redirect"
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER_LABEL') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_REDIRECT_SEARCH_LINKS') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter_state" class="text-sm">{{ Lang::txt('JSTATUS') }}:</label>
      <select name="state" id="filter_state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected($state === '')>{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        <option value="1" @selected($state == '1')>{{ Lang::txt('JENABLED') }}</option>
        <option value="0" @selected($state === '0')>{{ Lang::txt('JDISABLED') }}</option>
        <option value="2" @selected($state == '2')>{{ Lang::txt('JARCHIVED') }}</option>
        <option value="-2" @selected($state == '-2')>{{ Lang::txt('JTRASHED') }}</option>
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
            {!! Html::grid('sort', 'COM_REDIRECT_HEADING_OLD_URL', 'old_url', $sortDir, $sort) !!}
          </th>
          @if($type == 'redirect')
            <th>
              {!! Html::grid('sort', 'COM_REDIRECT_HEADING_NEW_URL', 'new_url', $sortDir, $sort) !!}
            </th>
          @else
            <th class="priority-4">
              {!! Html::grid('sort', 'COM_REDIRECT_HEADING_REFERRER', 'referer', $sortDir, $sort) !!}
            </th>
          @endif
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_REDIRECT_HEADING_CREATED_DATE', 'created_date', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'JSTATUS', 'published', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_REDIRECT_HEADING_HITS', 'hits', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
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
        <tr>
          <td colspan="7">
            @if($enabled)
              <div role="alert" class="alert alert-success alert-sm">
                {{ Lang::txt('COM_REDIRECT_PLUGIN_ENABLED') }}
              </div>
            @else
              <div role="alert" class="alert alert-warning alert-sm">
                {{ Lang::txt('COM_REDIRECT_PLUGIN_DISABLED') }}
              </div>
            @endif
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $item)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option . '&task=edit&id=' . $item->id,
                false, false
            );
            $old = str_replace($root, '', $item->old_url);

            $stateMap = [
                1  => ['badge-success', Lang::txt('JENABLED')],
                0  => ['badge-ghost',   Lang::txt('JDISABLED')],
                2  => ['badge-info',    Lang::txt('JARCHIVED')],
                -2 => ['badge-error',   Lang::txt('JTRASHED')],
            ];
            $stateBadge = $stateMap[$item->published] ?? ['badge-ghost', '?'];
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $item->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td class="break-all">
              @if($canEdit)
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary"
                   title="{{ $item->old_url }}">
                  <span class="text-xs text-muted-foreground">{{ Lang::txt('COM_REDIRECT_ROOT') }}</span>/{{ ltrim($old, '/') }}
                </a>
              @else
                {{ $old }}
              @endif
            </td>
            @if($type == 'redirect')
              <td class="break-all">
                @if(substr($item->new_url, 0, 4) != 'http')
                  <span class="text-xs text-muted-foreground">{{ Lang::txt('COM_REDIRECT_ROOT') }}</span>/
                @endif
                {{ ltrim($item->new_url, '/') }}
              </td>
            @else
              <td class="priority-4 break-all">
                {{ $item->referer }}
              </td>
            @endif
            <td class="priority-5">
              @if($item->created_date)
                <time datetime="{{ $item->created_date }}">
                  {{ Date::of($item->created_date)->format('M d, Y') }}
                </time>
              @endif
            </td>
            <td class="priority-2">
              <span class="badge badge-sm whitespace-nowrap {{ $stateBadge[0] }}">{{ $stateBadge[1] }}</span>
            </td>
            <td class="priority-3">
              {{ (int) $item->hits }}
            </td>
            <td class="priority-5">
              {{ (int) $item->id }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_REDIRECT_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($rows->count())
    @include('com_redirect::admin.views.links.tmpl._addform')
  @endif
</x-admin-form>
