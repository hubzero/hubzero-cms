{{--
  com_members — Plugin list for member-related plugins

  Variables: $items (paginated), $filters (search/state/access/sort/sort_Dir),
             $manage (array of element names with manage UI), $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Permissions::getActions('component');

  Toolbar::title(Lang::txt('Members') . ': ' . Lang::txt('Plugins'), 'members');
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
  }

  $sort      = e($filters['sort']);
  $sortDir   = e($filters['sort_Dir']);
  $canOrder  = User::authorise('core.edit.state', 'com_plugins');
  $saveOrder = ($sort == 'ordering');
  $folders   = $saveOrder ? $items->fieldsByKey('folder') : [];
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
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
      @endslot

      <select name="filter_state"
              id="filter_state"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}">
        <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        <option value="1" @selected(($filters['state'] ?? '') === '1')>{{ Lang::txt('JENABLED') }}</option>
        <option value="0" @selected(($filters['state'] ?? '') === '0')>{{ Lang::txt('JDISABLED') }}</option>
      </select>

      <select name="filter_access"
              id="filter_access"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}">
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options',
            Html::access('assetgroups'),
            'value', 'text',
            $filters['access'] ?? '') !!}
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col" class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'Plug-in Name', 'name', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'JSTATUS', 'enabled', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-2">
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $sortDir, $sort) !!}
            @if ($canOrder && $saveOrder)
              {!! Html::grid('order', $items, 'filesave.png', 'plugins.saveorder') !!}
            @endif
          </th>
          <th scope="col" class="priority-3">
            {{ Lang::txt('Manage') }}
          </th>
          <th scope="col" class="priority-3">
            {!! Html::grid('sort', 'Element', 'element', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-4">
            {!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'extension_id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">{!! $items->pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($items as $i => $item)
          @php
            $item->loadLanguage(true);

            $canEdit    = User::authorise('core.edit', 'com_plugins');
            $canCheckin = User::authorise('core.manage', 'com_checkin')
                || $item->checked_out == User::get('id')
                || $item->checked_out == 0;
            $canChange  = User::authorise('core.edit.state', 'com_plugins') && $canCheckin;

            $editUrl = Route::url(
                'index.php?option=com_plugins&task=edit&id='
                . (int) $item->extension_id
                . '&' . Session::getFormToken() . '=1', false
            );

            $paginationTotal = $items->pagination->total;
            $prevFolder = $folders[$i - 1] ?? null;
            $nextFolder = $folders[$i + 1] ?? null;
          @endphp
          <tr>
            {{-- Checkbox --}}
            <td class="column-check">
              {!! Html::grid('id', $i, $item->extension_id) !!}
            </td>

            {{-- Plugin Name --}}
            <td>
              @if ($item->checked_out)
                {!! Html::grid('checkedout', $i, $item->editor,
                    $item->checked_out_time, '', $canCheckin) !!}
              @endif
              @if ($canEdit)
                <a href="{!! $editUrl !!}"
                   class="link link-hover text-primary font-medium">
                  {{ Lang::txt($item->name) }}
                </a>
              @else
                <span class="font-medium">{{ Lang::txt($item->name) }}</span>
              @endif
            </td>

            {{-- Status --}}
            <td class="text-center">
              {!! Html::grid('published', $item->enabled, $i, '', $canChange) !!}
            </td>

            {{-- Ordering --}}
            <td class="priority-2 text-center">
              @if ($canChange)
                @if ($saveOrder)
                  @if ($sortDir == 'asc')
                    {!! $items->pagination->orderUpIcon($i, $prevFolder == $item->folder, 'orderup', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $items->pagination->orderDownIcon($i, $paginationTotal, $nextFolder == $item->folder, 'orderdown', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @else
                    {!! $items->pagination->orderUpIcon($i, $prevFolder == $item->folder, 'orderdown', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $items->pagination->orderDownIcon($i, $paginationTotal, $nextFolder == $item->folder, 'orderup', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @endif
                @endif
                <input type="text"
                       name="order[]"
                       id="order{{ $i }}"
                       size="5"
                       value="{{ $item->ordering }}"
                       @if (!$saveOrder) disabled @endif
                       class="input input-bordered input-xs w-14 text-center" />
                <label for="order{{ $i }}" class="sr-only">{{ $item->ordering }}</label>
              @else
                {{ $item->ordering }}
              @endif
            </td>

            {{-- Manage --}}
            <td class="priority-3 text-center">
              @if (in_array($item->element, $manage))
                @php
                  $manageUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=manage&plugin=' . $item->element, false
                  );
                @endphp
                <a href="{!! $manageUrl !!}" class="link link-hover">
                  {{ Lang::txt('Manage') }}
                </a>
              @endif
            </td>

            {{-- Element --}}
            <td class="priority-3 text-sm">{{ $item->element }}</td>

            {{-- Access --}}
            <td class="priority-4">{{ $item->access_level }}</td>

            {{-- ID --}}
            <td class="priority-5 text-right tabular-nums">{{ (int) $item->extension_id }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_PLUGINS_NO_MATCHING_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</x-admin-form>
