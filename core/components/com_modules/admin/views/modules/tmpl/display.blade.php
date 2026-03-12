{{--
  com_modules — Module list

  Variables: $items (paginated Module rows), $filters (search/client_id/state/position/
             module/access/language/sort/sort_Dir), $total (int)

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

  $canDo = \Components\Modules\Helpers\Modules::getActions();

  Toolbar::title(Lang::txt('COM_MODULES_MANAGER_MODULES'), 'module');

  if ($canDo->get('core.create')) {
      $selectUrl = Route::url('index.php?option=com_modules&task=select&tmpl=component', false);
      Toolbar::appendButton('Popup', 'new', 'JTOOLBAR_NEW', $selectUrl, 850, 400);
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::custom('duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::spacer();
      Toolbar::publish('publish', 'JTOOLBAR_PUBLISH', true);
      Toolbar::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH', true);
      Toolbar::spacer();
      Toolbar::checkin('checkin');
      Toolbar::spacer();
  }
  if (($filters['state'] ?? '') == -2 && $canDo->get('core.delete')) {
      Toolbar::deleteList('', 'delete', 'JTOOLBAR_EMPTY_TRASH');
      Toolbar::spacer();
  } elseif ($canDo->get('core.edit.state')) {
      Toolbar::trash();
      Toolbar::spacer();
  }
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_modules');
      Toolbar::spacer();
  }
  Toolbar::help('modules');

  $sort       = $filters['sort'];
  $sortDir    = $filters['sort_Dir'];
  $saveOrder  = ($sort == 'ordering');
  $positions  = $saveOrder ? $items->fieldsByKey('position') : [];
  $pagination = $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 20);

  $canBatch = User::authorise('core.create', 'com_modules')
      && User::authorise('core.edit', 'com_modules')
      && User::authorise('core.edit.state', 'com_modules');
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
               placeholder="{{ Lang::txt('COM_MODULES_MODULES_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
      @endslot

      {{-- Client (site/admin) --}}
      <select name="filter_client_id"
              id="filter_client_id"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('Client') }}"
              data-submit-on-change>
        {!! Html::select('options',
            \Components\Modules\Helpers\Modules::getClientOptions(),
            'value', 'text',
            $filters['client_id'] ?? 0) !!}
      </select>

      {{-- State --}}
      <select name="filter_state"
              id="filter_state"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        {!! Html::select('options',
            \Components\Modules\Helpers\Modules::getStateOptions(),
            'value', 'text',
            $filters['state'] ?? '') !!}
      </select>

      {{-- Position --}}
      <select name="filter_position"
              id="filter_position"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('COM_MODULES_OPTION_SELECT_POSITION') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_MODULES_OPTION_SELECT_POSITION') }}</option>
        {!! Html::select('options',
            \Components\Modules\Helpers\Modules::getPositions($filters['client_id'] ?? 0),
            'value', 'text',
            $filters['position'] ?? '') !!}
      </select>

      {{-- Module type --}}
      <select name="filter_module"
              id="filter_module"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('COM_MODULES_OPTION_SELECT_MODULE') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_MODULES_OPTION_SELECT_MODULE') }}</option>
        {!! Html::select('options',
            \Components\Modules\Helpers\Modules::getModules($filters['client_id'] ?? 0),
            'value', 'text',
            $filters['module'] ?? '') !!}
      </select>

      {{-- Access --}}
      <select name="filter_access"
              id="filter_access"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options',
            Html::access('assetgroups'),
            'value', 'text',
            $filters['access'] ?? '') !!}
      </select>

      {{-- Language --}}
      <select name="filter_language"
              id="filter_language"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_LANGUAGE') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_LANGUAGE') }}</option>
        {!! Html::select('options',
            \Hubzero\Html\Builder\ContentLanguage::existing(true, true),
            'value', 'text',
            $filters['language'] ?? '') !!}
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
            {!! Html::grid('sort', 'JGLOBAL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'JSTATUS', 'published', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-2">
            {!! Html::grid('sort', 'COM_MODULES_HEADING_POSITION', 'position', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-3">
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $sortDir, $sort) !!}
            @if ($saveOrder)
              {!! Html::grid('order', $items, 'filesave.png', 'saveorder') !!}
            @endif
          </th>
          <th scope="col" class="priority-3">
            {!! Html::grid('sort', 'COM_MODULES_HEADING_MODULE', 'name', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-4">
            {!! Html::grid('sort', 'COM_MODULES_HEADING_PAGES', 'pages', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-4">
            {!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-6">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="admin-pagination">{!! $pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($items as $i => $item)
          @php
            $path = $item->path();
            if (!$path) {
                $item->published = 0;
            }

            $canEdit    = $path ? User::authorise('core.edit', 'com_modules') : false;
            $canCheckin = User::authorise('core.manage', 'com_checkin')
                || $item->checked_out == User::get('id')
                || $item->checked_out == 0;
            $canChange  = User::authorise('core.edit.state', 'com_modules') && $canCheckin;

            $editUrl = Route::url(
                'index.php?option=com_modules&task=edit&id=' . (int) $item->id
                . '&' . Session::getFormToken() . '=1',
                false, false
            );

            $prevPos = $positions[$i - 1] ?? null;
            $nextPos = $positions[$i + 1] ?? null;

            if (is_null($item->pages)) {
                $pages = Lang::txt('JNONE');
            } elseif ($item->pages < 0) {
                $pages = Lang::txt('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
            } elseif ($item->pages > 0) {
                $pages = Lang::txt('COM_MODULES_ASSIGNED_VARIES_ONLY');
            } else {
                $pages = Lang::txt('JALL');
            }
          @endphp
          <tr>
            {{-- Checkbox --}}
            <td class="column-check">
              @if ($path)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $item->id }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ $item->title }}"
                       data-check-item />
              @endif
            </td>

            {{-- Title --}}
            <td>
              @if ($item->checked_out)
                {!! Html::grid('checkedout', $i, $item->editor,
                    $item->checked_out_time, 'modules.', $canCheckin) !!}
              @endif
              @if ($canEdit)
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $item->title }}
                </a>
              @else
                <span class="font-medium">{{ $item->title }}</span>
              @endif
              @if (!$path)
                <p class="text-xs text-error mt-0.5">
                  {{ Lang::txt('COM_MODULES_ERROR_MISSING_FILES') }}
                </p>
              @endif
              @if (!empty($item->note))
                <p class="text-xs text-muted-foreground mt-0.5">
                  {{ Lang::txt('JGLOBAL_LIST_NOTE', e($item->note)) }}
                </p>
              @endif
            </td>

            {{-- Status --}}
            <td class="text-center">
              {!! \Components\Modules\Helpers\Modules::state($item->published, $i, $canChange, 'cb') !!}
            </td>

            {{-- Position --}}
            <td class="priority-2 text-sm">
              {{ $item->position ?: ':: ' . Lang::txt('JNONE') . ' ::' }}
            </td>

            {{-- Ordering --}}
            <td class="priority-3 text-center">
              @if ($canChange)
                @if ($saveOrder)
                  @if ($sortDir == 'asc')
                    {!! $pagination->orderUpIcon($i, $prevPos == $item->position, 'orderup', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $pagination->orderDownIcon($i, $pagination->total, $nextPos == $item->position, 'orderdown', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @else
                    {!! $pagination->orderUpIcon($i, $prevPos == $item->position, 'orderdown', 'JLIB_HTML_MOVE_UP', true) !!}
                    {!! $pagination->orderDownIcon($i, $pagination->total, $nextPos == $item->position, 'orderup', 'JLIB_HTML_MOVE_DOWN', true) !!}
                  @endif
                @endif
                <input type="text"
                       name="order[]"
                       id="order{{ $i }}"
                       size="5"
                       value="{{ $item->ordering }}"
                       @if(!$saveOrder) disabled @endif
                       class="input input-bordered input-xs w-14 text-center" />
                <label for="order{{ $i }}" class="sr-only">{{ $item->ordering }}</label>
              @else
                {{ $item->ordering }}
              @endif
            </td>

            {{-- Module type --}}
            <td class="priority-3 text-sm">{{ $item->name }}</td>

            {{-- Pages --}}
            <td class="priority-4 text-sm text-center">{{ $pages }}</td>

            {{-- Access --}}
            <td class="priority-4">{{ $item->access_level }}</td>

            {{-- Language --}}
            <td class="priority-5 text-sm">
              @if ($item->language == '')
                {{ Lang::txt('JDEFAULT') }}
              @elseif ($item->language == '*')
                {{ Lang::txt('JALL') }}
              @else
                {{ $item->language_title ? e($item->language_title) : Lang::txt('JUNDEFINED') }}
              @endif
            </td>

            {{-- ID --}}
            <td class="priority-6 text-right tabular-nums">{{ (int) $item->id }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_MODULES_MSG_MANAGE_NO_MODULES') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($canBatch)
    @include('com_modules::admin.views.modules.tmpl._batch')
  @endif

</x-admin-form>
