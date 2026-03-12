{{--
  Members — User permissions debug

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $title = Lang::txt(
      'COM_MEMBERS_VIEW_DEBUG_USER_TITLE',
      $user->get('id'),
      $user->get('name')
  );
  Toolbar::title($title, 'user');
  Toolbar::help('JHELP_USERS_DEBUG_USERS');

  $listOrder = $filters['sort'] ?? 'lft';
  $listDirn  = $filters['sort_Dir'] ?? 'asc';

  $formUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=debug&id=' . (int) $user->get('id'), false
  );
@endphp

<form action="{!! $formUrl !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <fieldset id="filter-bar"
            class="admin-filters flex flex-wrap items-center gap-2 w-full">
    <div class="flex items-center gap-2">
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_USERS') }}" />
      <button type="submit" class="btn btn-sm">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm filter-clear">
        {{ Lang::txt('JSEARCH_RESET') }}
      </button>
    </div>
    <div class="flex items-center gap-2 ml-auto">
      <select name="filter_component"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_COMPONENT') }}">
        <option value="">
          {{ Lang::txt('COM_MEMBERS_OPTION_SELECT_COMPONENT') }}
        </option>
        @if (!empty($components))
          {!! Html::select(
              'options',
              $components,
              'value',
              'text',
              $filters['component'] ?? ''
          ) !!}
        @endif
      </select>

      <select name="filter_level_start"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_START') }}">
        <option value="">
          {{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_START') }}
        </option>
        {!! Html::select(
            'options',
            $levels,
            'value',
            'text',
            $filters['level_start'] ?? ''
        ) !!}
      </select>

      <select name="filter_level_end"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_END') }}">
        <option value="">
          {{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_END') }}
        </option>
        {!! Html::select(
            'options',
            $levels,
            'value',
            'text',
            $filters['level_end'] ?? ''
        ) !!}
      </select>
    </div>
  </fieldset>

  <table class="admin-table">
    <caption>
      {{ Lang::txt('COM_MEMBERS_DEBUG_LEGEND') }}
      <span class="swatch">
        {{ Lang::txt('COM_MEMBERS_DEBUG_NO_CHECK', '-') }}
      </span>
      <span class="check-0 swatch">
        {!! Lang::txt(
            'COM_MEMBERS_DEBUG_IMPLICIT_DENY',
            '<span class="state no"><span>-</span></span>'
        ) !!}
      </span>
      <span class="check-a swatch">
        {!! Lang::txt(
            'COM_MEMBERS_DEBUG_EXPLICIT_ALLOW',
            '<span class="state yes"><span>&#10003;</span></span>'
        ) !!}
      </span>
      <span class="check-d swatch">
        {!! Lang::txt(
            'COM_MEMBERS_DEBUG_EXPLICIT_DENY',
            '<span class="state no"><span>&#10007;</span></span>'
        ) !!}
      </span>
    </caption>
    <thead>
      <tr>
        <th scope="col">
          {!! Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_TITLE', 'title', $listDirn, $listOrder) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_NAME', 'name', $listDirn, $listOrder) !!}
        </th>
        @foreach ($actions as $key => $action)
          @php
            $tipTitle = htmlspecialchars(
                Lang::txt($key) . '::' . Lang::txt($action[1]),
                ENT_COMPAT,
                'UTF-8'
            );
          @endphp
          <th scope="col">
            <span title="{{ $tipTitle }}">{{ Lang::txt($key) }}</span>
          </th>
        @endforeach
        <th scope="col">
          {!! Html::grid('sort', 'COM_MEMBERS_HEADING_LFT', 'lft', $listDirn, $listOrder) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($assets as $item)
        @php
          $checks = $item->get('checks');
        @endphp
        <tr>
          <td>{{ $item->get('title') }}</td>
          <td>
            {!! str_repeat('<span class="gi">|&mdash;</span>', $item->get('level')) !!}
            {{ $item->get('name') }}
          </td>
          @foreach ($actions as $action)
            @php
              $name  = $action[0];
              $check = $checks[$name];
              if ($check === true) {
                  $class = 'check-a';
                  $text  = '<span class="state yes"><span>&#10003;</span></span>';
              } elseif ($check === false) {
                  $class = 'check-d';
                  $text  = '<span class="state no"><span>&#10007;</span></span>';
              } elseif ($check === null) {
                  $class = 'check-0';
                  $text  = '<span class="state no"><span>&#10007;</span></span>';
              } else {
                  $class = '';
                  $text  = '&#160;';
              }
            @endphp
            <td class="center {{ $class }}">{!! $text !!}</td>
          @endforeach
          <td class="center">
            {{ (int) $item->get('lft') }} - {{ (int) $item->get('rgt') }}
          </td>
          <td class="center">
            {{ (int) $item->get('id') }}
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="15">{!! $assets->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="id" value="{{ $user->get('id') }}" />
  <input type="hidden" name="task" value="debug" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $listOrder }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $listDirn }}" />
  {!! Html::input('token') !!}
</form>
