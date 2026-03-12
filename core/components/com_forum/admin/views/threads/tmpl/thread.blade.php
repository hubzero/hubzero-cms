{{--
  Forum Thread Posts — Admin thread detail view (list of posts in a thread)

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

  $canDo   = \Components\Forum\Helpers\Permissions::getActions('thread');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
  $access  = Html::access('assetgroups');

  Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_POSTS'), 'forum');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('thread');
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
        <label for="field-category_id" class="text-sm">{{ Lang::txt('COM_FORUM_FILTER_CATEGORY') }}:</label>
        <select name="category_id" id="field-category_id"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="-1">{{ Lang::txt('COM_FORUM_FIELD_CATEGORY_SELECT') }}</option>
          @foreach($sections as $group => $sects)
            <optgroup label="{{ $group }}">
              @foreach($sects as $sect)
                <optgroup label="&nbsp; &nbsp; {{ $sect->title }}">
                  @if(isset($sect->categories))
                    @foreach($sect->categories as $cat)
                      <option value="{{ $cat->id }}" @selected($filters['category_id'] == $cat->id)>
                        &nbsp; &nbsp; {{ $cat->title }}
                      </option>
                    @endforeach
                  @endif
                </optgroup>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      @endslot

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_FORUM_FIELD_STATE') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == -1)>{{ Lang::txt('COM_FORUM_ALL_STATES') }}</option>
        <option value="0" @selected($filters['state'] === 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($filters['state'] === 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="2" @selected($filters['state'] === 2)>{{ Lang::txt('JTRASHED') }}</option>
      </select>

      <label for="filter-access" class="text-sm">{{ Lang::txt('JFIELD_ACCESS_LABEL') }}:</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', $access, 'value', 'text', $filters['access']) !!}
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      @if(is_object($thread))
        <caption class="text-left p-3 font-medium">
          {{ Lang::txt('COM_FORUM_THREAD') }}: {{ $thread->get('title') }}
        </caption>
      @endif
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="w-16">
            {!! Html::grid('sort', 'COM_FORUM_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_STATE', 'state', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_CREATOR', 'created_by', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_CREATED', 'created', $sortDir, $sort) !!}
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
            $state = (int) $row->get('state', 0);
            if ($state === 1) {
                $stateClass = 'badge-success';
                $stateText  = Lang::txt('JPUBLISHED');
                $stateTask  = 'unpublish';
            } elseif ($state === 2) {
                $stateClass = 'badge-warning';
                $stateText  = Lang::txt('JTRASHED');
                $stateTask  = 'publish';
            } else {
                $stateClass = 'badge-ghost';
                $stateText  = Lang::txt('JUNPUBLISHED');
                $stateTask  = 'publish';
            }

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id')
                . '&thread=' . $filters['thread'],
                false, false
            );

            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&category_id=' . $filters['category_id']
                . '&task=' . $stateTask
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1',
                false, false
            );

            $scopeId = $row->get('scope_id');
            $scopeSuffix = $scopeId ? '(' . e($scopeId) . ')' : '';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->get('title') }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              <a href="{{ $editUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->get('title') }}
              </a>
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ $stateUrl }}"
                   title="{{ Lang::txt('COM_FORUM_SET_TO', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>{{ $row->get('scope') }} {{ $scopeSuffix }}</td>
            <td>{{ $row->get('created_by') }}</td>
            <td>
              @if($row->get('created'))
                <time datetime="{{ $row->get('created') }}">
                  {{ $row->get('created') }}
                </time>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="thread" value="{{ $filters['thread'] }}" />
</x-admin-form>
