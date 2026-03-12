{{--
  com_menus — Admin menu items list

  Hierarchical item list with ordering, publish state toggle, home (default) marker.
  Includes batch-processing partial when user has full create+edit+edit.state perms.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  // Register component HTML helpers (required for Html::menus(), Html::menu())
  Html::addIncludePath(\Hubzero\Facades\Component::path($option) . '/helpers/html');

  $canDo     = \Components\Menus\Helpers\Menus::getActions();
  $sort      = $filters['sort'] ?? 'lft';
  $sortDir   = $filters['sort_Dir'] ?? 'asc';
  // $ordering (2D array from controller) is already in scope; $sortByLft is the local boolean
  $sortByLft = ($sort === 'lft');
  $canOrder  = User::authorise('core.edit.state', $option);

  Toolbar::title(Lang::txt('COM_MENUS_VIEW_ITEMS_TITLE'), 'menumgr');

  if ($canDo->get('core.create'))    Toolbar::addNew('item.add');
  if ($canDo->get('core.edit'))      Toolbar::editList('item.edit');
  if ($canDo->get('core.edit.state')) {
      Toolbar::divider();
      Toolbar::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
      Toolbar::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
  }
  if (User::authorise('core.admin')) {
      Toolbar::divider();
      Toolbar::checkin('items.checkin', 'JTOOLBAR_CHECKIN', true);
  }
  if (($filters['published'] ?? '') == -2 && $canDo->get('core.delete')) {
      Toolbar::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
  } elseif ($canDo->get('core.edit.state')) {
      Toolbar::trash('items.trash');
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::makeDefault('items.setDefault', 'COM_MENUS_TOOLBAR_SET_HOME');
      Toolbar::divider();
  }
  if (User::authorise('core.admin')) {
      Toolbar::custom('items.rebuild', 'refresh.png', 'refresh_f2.png', 'JToolbar_Rebuild', false);
      Toolbar::divider();
  }
  Toolbar::help('items');

  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);

  $canBatch = User::authorise('core.create', 'com_menus')
           && User::authorise('core.edit', 'com_menus')
           && User::authorise('core.edit.state', 'com_menus');
@endphp

{{-- Filters --}}
<form action="{{ $formAction }}" method="post" name="adminForm" id="adminForm">

  <x-admin-filters>
    @slot('search')
      <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER_LABEL') }}</label>
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm w-64"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MENUS_ITEMS_SEARCH_FILTER') }}" />
      <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
      <button type="button" class="btn btn-sm btn-ghost border border-base-300" data-clear-search="filter_search">
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    @endslot

    {{-- Menutype --}}
    <select name="menutype" class="select select-bordered select-sm" aria-label="{{ Lang::txt('COM_MENUS_MENU') }}" data-submit-on-change>
      {!! Html::select('options', Html::menu('menus'), 'value', 'text', $filters['menutype'] ?? '') !!}
    </select>

    {{-- Level --}}
    <select name="filter_level" class="select select-bordered select-sm" aria-label="{{ Lang::txt('COM_MENUS_OPTION_SELECT_LEVEL') }}" data-submit-on-change>
      <option value="">{{ Lang::txt('COM_MENUS_OPTION_SELECT_LEVEL') }}</option>
      {!! Html::select('options', $f_levels, 'value', 'text', $filters['level'] ?? '') !!}
    </select>

    {{-- Published --}}
    <select name="filter_published" class="select select-bordered select-sm" aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}" data-submit-on-change>
      <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
      {!! Html::select('options', Html::grid('publishedOptions', ['archived' => false]), 'value', 'text', $filters['published'] ?? '', true) !!}
    </select>

    {{-- Access --}}
    <select name="filter_access" class="select select-bordered select-sm" aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}" data-submit-on-change>
      <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
      {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $filters['access'] ?? '') !!}
    </select>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto mt-4">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="w-8">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'JGLOBAL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th class="text-center priority-3 w-24">{!! Html::grid('sort', 'JSTATUS', 'published', $sortDir, $sort) !!}</th>
          <th class="text-center w-24">
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'lft', $sortDir, $sort) !!}
          </th>
          <th class="text-center priority-4 w-24">{!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $sortDir, $sort) !!}</th>
          <th class="priority-5">{{ Lang::txt('JGRID_HEADING_MENU_ITEM_TYPE') }}</th>
          <th class="text-center priority-2 w-16">{!! Html::grid('sort', 'COM_MENUS_HEADING_HOME', 'home', $sortDir, $sort) !!}</th>
          <th class="text-center priority-4 w-24">{!! Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $sortDir, $sort) !!}</th>
          <th class="text-center priority-6 w-16">{!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9" class="p-3 text-sm text-muted-foreground">
            {!! $rows->pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $i => $item)
          @php
            // $ordering is the 2D parent_id => [item_ids] array from the controller
            $parentOrder = $ordering[$item->get('parent_id')] ?? [];
            $orderkey    = array_search($item->get('id'), $parentOrder);
            $canEdit     = User::authorise('core.edit', $option);
            $canCheckin  = User::authorise('core.manage', 'com_checkin')
                         || $item->get('checked_out') == User::get('id')
                         || $item->get('checked_out') == 0;
            $canChange   = User::authorise('core.edit.state', $option) && $canCheckin;
            $indent      = max(0, (int)$item->get('level') - 1);
            $hasUp       = isset($parentOrder[$orderkey - 1]);
            $hasDown     = isset($parentOrder[$orderkey + 1]);
          @endphp
          <tr>
            <td class="text-center">
              <input type="checkbox" name="cid[]" value="{{ (int) $item->get('id') }}"
                     id="cb{{ $i }}" class="checkbox checkbox-sm" aria-label="{{ $item->get('title') }}" data-check-item />
            </td>
            <td>
              <div class="flex items-start gap-1">
                @if($indent > 0)
                  <span class="text-faint-foreground text-xs select-none shrink-0 pl-[{{ $indent }}rem]">
                    @for($d = 0; $d < $indent; $d++)&#x251C;&#x2500;@endfor
                  </span>
                @endif
                <div>
                  @if($item->get('checked_out'))
                    {!! Html::grid('checkedout', $i, $item->get('editor'), $item->get('checked_out_time'), 'items.', $canCheckin) !!}
                  @endif
                  @php
                    $title = $item->get('type') === 'separator'
                        ? '<span class="text-faint-foreground select-none">—</span>'
                        : e($item->get('title'));
                  @endphp
                  @if($canEdit)
                    @php $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&cid[]=' . (int)$item->get('id'), false); @endphp
                    <a href="{{ $editUrl }}" class="font-medium link link-hover">{!! $title !!}</a>
                  @else
                    <span class="font-medium">{!! $title !!}</span>
                  @endif
                  <div class="text-xs text-muted-foreground mt-0.5" title="{{ $item->get('path') }}">
                    @if($item->get('type') !== 'separator' && $item->get('alias'))
                      <span>{{ $item->get('alias') }}</span>
                      @if($item->get('note'))
                        <span class="text-warning"> — {{ $item->get('note') }}</span>
                      @endif
                    @elseif($item->get('note'))
                      <span class="text-warning">{{ $item->get('note') }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </td>
            <td class="text-center priority-3">
              @php
                $rawPub = (int)$item->get('apublished'); // raw a.published: 1=pub, 0=unpub, -2=trashed
                $pub    = (int)$item->get('published');  // CASE: encodes type + extension state
                $extOff = in_array($pub, [-1, -2]);      // component extension is off
                if ($rawPub === -2) {
                    $btnCls      = 'btn-error no-ring';
                    $stateTask   = 'items.unpublish';
                    $stateAction = Lang::txt('JTOOLBAR_UNPUBLISH');
                    $stateIcon   = '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />';
                } elseif ($rawPub === 1) {
                    // Published — grey if component extension is off
                    $btnCls      = $extOff ? 'is-dimmed' : 'btn-success';
                    $stateTask   = 'items.unpublish';
                    $stateAction = Lang::txt('JTOOLBAR_UNPUBLISH');
                    $stateIcon   = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />';
                } else {
                    // Unpublished — grey if component extension is off
                    $btnCls      = $extOff ? 'is-dimmed' : 'btn-warning';
                    $stateTask   = 'items.publish';
                    $stateAction = Lang::txt('JTOOLBAR_PUBLISH');
                    $stateIcon   = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />';
                }
              @endphp
              @if($canChange)
                <a href="#" data-list-item-task data-cb="cb{{ $i }}" data-task="{{ $stateTask }}"
                   data-title="{{ $stateAction }}" aria-label="{{ $stateAction }}" class="status-icon {{ $btnCls }}">
                  <svg viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="1.5"
                       stroke-linecap="round" stroke-linejoin="round">
                    {!! $stateIcon !!}
                  </svg>
                </a>
              @else
                <span title="{{ $stateAction }}" class="status-icon {{ $btnCls }}">
                  <svg viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="1.5"
                       stroke-linecap="round" stroke-linejoin="round">
                    {!! $stateIcon !!}
                  </svg>
                </span>
              @endif
            </td>
            <td class="text-center whitespace-nowrap">
              @if($canChange)
                <div class="order-group">
                  @if($hasUp)
                    <button type="button" class="order-btn" data-order-btn
                            data-cb="cb{{ $i }}" data-task="items.orderup"
                            title="{{ Lang::txt('JLIB_HTML_MOVE_UP') }}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 15l7-7 7 7"/>
                      </svg>
                    </button>
                  @else
                    <span class="order-btn is-disabled">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 15l7-7 7 7"/>
                      </svg>
                    </span>
                  @endif

                  <span class="order-num">{{ $orderkey !== false ? $orderkey + 1 : '' }}</span>
                  @if($hasDown)
                    <button type="button" class="order-btn" data-order-btn
                            data-cb="cb{{ $i }}" data-task="items.orderdown"
                            title="{{ Lang::txt('JLIB_HTML_MOVE_DOWN') }}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 9l-7 7-7-7"/>
                      </svg>
                    </button>
                  @else
                    <span class="order-btn is-disabled">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 9l-7 7-7-7"/>
                      </svg>
                    </span>
                  @endif
                </div>
              @else
                <span class="order-num">{{ $orderkey !== false ? $orderkey + 1 : '' }}</span>
              @endif
            </td>
            <td class="text-center priority-4 text-sm">{{ $item->get('access_level') }}</td>
            <td class="priority-5 text-sm">
              <span title="{{ $item->get('item_type_desc') ?? '' }}">
                {{ $item->get('item_type') }}
              </span>
            </td>
            <td class="text-center priority-2">
              @if($item->get('type') == 'component')
                @if($item->get('language') == '*' || $item->get('home') == '0')
                  @php $isDefault = ($item->get('language') != '*' || !$item->get('home')) && $canChange; @endphp
                  @if($item->get('home') == '1')
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="#eab308" stroke="#ca8a04" stroke-width="1"
                         stroke-linecap="round" stroke-linejoin="round"
                         title="{{ Lang::txt('JDEFAULT') }}">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                  @elseif($isDefault)
                    <a href="#" data-list-item-task data-cb="cb{{ $i }}" data-task="items.setDefault"
                       title="{{ Lang::txt('JLIB_HTML_SETDEFAULT_ITEM') }}" aria-label="{{ Lang::txt('JLIB_HTML_SETDEFAULT_ITEM') }}" class="text-muted-foreground hover:text-warning transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                           fill="none" stroke="currentColor" stroke-width="1.5"
                           stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                      </svg>
                    </a>
                  @endif
                @elseif($canChange)
                  @php
                    $unsetUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=unsetDefault&cid[]=' . $item->get('id') . '&' . Session::getFormToken() . '=1', false);
                    $unsetTitle = Lang::txt('COM_MENUS_GRID_UNSET_LANGUAGE', $item->get('language_title'));
                  @endphp
                  <a href="{{ $unsetUrl }}" title="{{ $unsetTitle }}" class="link link-hover text-xs">
                    {{ $item->get('language_title') }}
                  </a>
                @else
                  <span class="text-xs">{{ $item->get('language_title') }}</span>
                @endif
              @endif
            </td>
            <td class="text-center priority-4 text-sm">
              {{ $item->get('language') === '*' ? Lang::txt('JALL') : e($item->get('language_title') ?: $item->get('language')) }}
            </td>
            <td class="text-center priority-6 text-sm">
              <span title="{{ $item->get('lft') }}-{{ $item->get('rgt') }}">
                {{ (int) $item->get('id') }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="p-6 text-center text-muted-foreground text-sm">
              {{ Lang::txt('COM_MENUS_NO_ITEMS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Batch panel --}}
  @if($canBatch)
    @include('com_menus::admin.views.items.tmpl.display_batch')
  @endif

  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />

  {!! Html::input('token') !!}

</form>
