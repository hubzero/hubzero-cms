{{--
  com_templates — Template styles list

  Variables: $rows (paginated), $filters (search/template/client_id/sort/sort_Dir), $preview (bool)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Templates\Helpers\Utilities::getActions();

  Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_STYLES'), 'thememanager');
  if ($canDo->get('core.edit.state')) {
      Toolbar::makeDefault('setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::custom('duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
      Toolbar::spacer();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_templates');
      Toolbar::spacer();
  }
  Toolbar::help('styles');

  $sortDir = $filters['sort_Dir'];
  $sort    = $filters['sort'];
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
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('COM_TEMPLATES_STYLES_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
      @endslot

      <select name="filter_template"
              id="filter_template"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('COM_TEMPLATES_FILTER_TEMPLATE') }}"
              data-submit-on-change>
        <option value="0">{{ Lang::txt('COM_TEMPLATES_FILTER_TEMPLATE') }}</option>
        {!! Html::select('options',
            \Components\Templates\Helpers\Utilities::getTemplateOptions($filters['client_id']),
            'value', 'text',
            $filters['template']) !!}
      </select>

      <select name="filter_client_id"
              id="filter_client_id"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JGLOBAL_FILTER_CLIENT') }}"
              data-submit-on-change>
        <option value="*">{{ Lang::txt('JGLOBAL_FILTER_CLIENT') }}</option>
        {!! Html::select('options',
            \Components\Templates\Helpers\Utilities::getClientOptions(),
            'value', 'text',
            $filters['client_id']) !!}
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col" class="column-check">&#160;</th>
          <th scope="col" class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_TEMPLATES_HEADING_STYLE', 'title', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-2">
            {!! Html::grid('sort', 'JCLIENT', 'client_id', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'template', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-3">
            {!! Html::grid('sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'home', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-4">{{ Lang::txt('COM_TEMPLATES_HEADING_ASSIGNED') }}</th>
          <th scope="col" class="priority-5">
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">{!! $rows->pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $i => $item)
          @php
            $path      = $item->path();
            $canEdit   = $path ? User::authorise('core.edit', $option) : false;
            $canChange = User::authorise('core.edit.state', $option);
            $editUrl   = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . (int) $item->id, false
            );
            $templateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=templates&id=' . (int) $item->e_id, false
            );
          @endphp
          <tr>
            {{-- Preview icon --}}
            <td class="text-center">
              @if ($path && $preview && $item->client_id == '0')
                @php
                  $previewUrl   = Request::root() . 'index.php?tp=1&templateStyle=' . (int) $item->id;
                  $previewTitle = Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW');
                @endphp
                <a href="{{ $previewUrl }}" rel="noopener" target="_blank"
                   class="link link-hover text-xs" title="{{ $previewTitle }}">
                  {{ $previewTitle }}
                </a>
              @elseif ($path && $item->client_id == '1')
                <span class="text-faint-foreground text-xs"
                      title="{{ Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN') }}">—</span>
              @else
                <span class="text-faint-foreground text-xs">—</span>
              @endif
            </td>

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
              @if ($canEdit)
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $item->title }}
                </a>
              @else
                <span class="font-medium">{{ $item->title }}</span>
              @endif
              @if (!$path)
                <p class="text-xs text-error mt-0.5">
                  {{ Lang::txt('COM_TEMPLATES_ERROR_MISSING_FILES') }}
                </p>
              @endif
            </td>

            {{-- Client --}}
            <td class="priority-2">
              {{ $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR') }}
            </td>

            {{-- Template --}}
            <td>
              <a href="{{ $templateUrl }}" class="link link-hover">
                {{ ucfirst(e($item->template)) }}
              </a>
            </td>

            {{-- Default / home --}}
            <td class="priority-3 text-center">
              @if ($item->home == '0' || $item->home == '1')
                {!! Html::grid('isdefault', $item->home != '0', $i, 'styles.', $canChange && $item->home != '1') !!}
              @elseif ($canChange)
                @php
                  $unsetUrl   = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=unsetDefault&id=' . $item->id
                      . '&' . Session::getFormToken() . '=1', false
                  );
                  $unsetTitle = Lang::txt('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title);
                @endphp
                <a href="{{ $unsetUrl }}" title="{{ $unsetTitle }}">
                  {!! Html::asset('image', 'mod_languages/' . $item->image . '.gif',
                      $item->language_title, ['title' => $unsetTitle], true) !!}
                </a>
              @else
                {!! Html::asset('image', 'mod_languages/' . $item->image . '.gif',
                    $item->language_title, ['title' => $item->language_title], true) !!}
              @endif
            </td>

            {{-- Assigned count --}}
            <td class="priority-4 text-center">
              @if ($item->assigned > 0)
                <span class="badge badge-sm badge-success">
                  {{ Lang::txts('COM_TEMPLATES_ASSIGNED', $item->assigned) }}
                </span>
              @else
                <span class="text-faint-foreground">—</span>
              @endif
            </td>

            {{-- ID --}}
            <td class="priority-5 text-right tabular-nums">{{ (int) $item->id }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_TEMPLATES_NO_STYLES') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</x-admin-form>
