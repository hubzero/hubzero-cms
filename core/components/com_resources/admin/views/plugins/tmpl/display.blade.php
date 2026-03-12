{{--
  Resource Plugins — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo     = \Components\Resources\Helpers\Permissions::getActions('plugin');
  $listOrder = $filters['sort'] ?? 'ordering';
  $listDirn  = $filters['sort_Dir'] ?? 'asc';
  $canOrder  = User::authorise('core.edit.state', 'com_plugins');
  $saveOrder = ($listOrder == 'ordering');
  $folders   = $items->fieldsByKey('folder');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_PLUGINS') }}"
    icon="plugin"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $listOrder }}"
    sortDir="{{ $listDirn }}"
>
  <x-admin-filters>
      @slot('search')
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-60"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

        <select name="filter_state" class="select select-bordered select-sm" data-submit-on-change
                aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}">
          <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
          {!! Html::select(
              'options',
              \Components\Plugins\Helpers\Plugins::stateOptions(),
              'value',
              'text',
              $filters['state'] ?? '',
              true
          ) !!}
        </select>

        <select name="filter_access" class="select select-bordered select-sm" data-submit-on-change
                aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}">
          <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
          {!! Html::select(
              'options',
              Html::access('assetgroups'),
              'value',
              'text',
              $filters['access'] ?? ''
          ) !!}
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
            {!! Html::grid('sort', 'Plug-in Name', 'name', $listDirn, $listOrder) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JSTATUS', 'enabled', $listDirn, $listOrder) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder) !!}
          </th>
          <th class="priority-3">
            {{ Lang::txt('Manage') }}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'Element', 'element', $listDirn, $listOrder) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'extension_id', $listDirn, $listOrder) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $items->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($items as $i => $item)
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
          @endphp
          <tr>
            <td class="column-check">
              {!! Html::grid('id', $i, $item->extension_id) !!}
            </td>
            <td>
              @if($item->checked_out)
                {!! Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, '', $canCheckin) !!}
              @endif
              @if($canEdit)
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ Lang::txt($item->name) }}
                </a>
              @else
                {{ Lang::txt($item->name) }}
              @endif
            </td>
            <td>
              {!! Html::grid('published', $item->enabled, $i, '', $canChange) !!}
            </td>
            <td class="priority-2">
              @if($canChange)
                @if($saveOrder)
                  @php
                    $prevFolder = ($folders[$i - 1] ?? null) == $item->folder;
                    $nextFolder = ($folders[$i + 1] ?? null) == $item->folder;
                    $pTotal     = $items->pagination->total;
                  @endphp
                  @if($listDirn == 'asc')
                    <span>{!! $items->pagination->orderUpIcon($i, $prevFolder, 'orderup', 'JLIB_HTML_MOVE_UP', true) !!}</span>
                    <span>{!! $items->pagination->orderDownIcon($i, $pTotal, $nextFolder, 'orderdown', 'JLIB_HTML_MOVE_DOWN', true) !!}</span>
                  @elseif($listDirn == 'desc')
                    <span>{!! $items->pagination->orderUpIcon($i, $prevFolder, 'orderdown', 'JLIB_HTML_MOVE_UP', true) !!}</span>
                    <span>{!! $items->pagination->orderDownIcon($i, $pTotal, $nextFolder, 'orderup', 'JLIB_HTML_MOVE_DOWN', true) !!}</span>
                  @endif
                @endif
                <input type="text"
                       name="order[]"
                       size="5"
                       value="{{ $item->ordering }}"
                       aria-label="{{ Lang::txt('JGRID_HEADING_ORDERING') }} - {{ Lang::txt($item->name) }}"
                       class="input input-bordered input-xs w-16"
                       @unless($saveOrder) disabled @endunless />
              @else
                {{ $item->ordering }}
              @endif
            </td>
            <td class="priority-3">
              @if(in_array($item->element, $manage))
                @php
                  $manageUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=manage&plugin=' . $item->element, false
                  );
                @endphp
                <a href="{{ $manageUrl }}" class="link link-primary text-sm">
                  {{ Lang::txt('Manage') }}
                </a>
              @endif
            </td>
            <td class="priority-3">
              {{ $item->element }}
            </td>
            <td class="priority-4">
              {{ $item->access_level }}
            </td>
            <td class="priority-5">
              {{ (int) $item->extension_id }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
