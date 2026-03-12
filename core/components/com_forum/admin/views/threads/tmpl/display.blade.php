{{--
  Forum Threads — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Forum\Helpers\Permissions::getActions('thread');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
  $access  = Html::access('assetgroups');

  // Build scope lookup
  $scopeList = [];
  foreach ($scopes as $result) {
      if (!isset($scopeList[$result->scope])) {
          $scopeList[$result->scope] = [];
      }
      $scopeList[$result->scope][$result->scope_id] = $result;
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_FORUM') }}: {{ Lang::txt('COM_FORUM_THREADS') }}"
    icon="forum"
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
        <label for="scopeinfo" class="text-sm">{{ Lang::txt('COM_FORUM_FILTER_SCOPE') }}:</label>
        <select name="scopeinfo" id="scopeinfo"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="">{{ Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT') }}</option>
          <option value="site:0" @selected($filters['scopeinfo'] == 'site:0')>
            {{ Lang::txt('COM_FORUM_NONE') }}
          </option>
          @foreach($scopeList as $label => $optgroup)
            @if($label !== 'site')
              <optgroup label="{{ $label }}">
                @foreach($optgroup as $result)
                  @php $val = $result->scope . ':' . $result->scope_id; @endphp
                  <option value="{{ $val }}" @selected($filters['scopeinfo'] == $val)>
                    {{ $result->caption }}
                  </option>
                @endforeach
              </optgroup>
            @endif
          @endforeach
        </select>

        @if(count($sections) > 0)
          <label for="field-section_id" class="text-sm">{{ Lang::txt('COM_FORUM_FILTER_SECTION') }}:</label>
          <select name="section_id" id="field-section_id"
                  class="select select-bordered select-sm"
                  data-submit-on-change>
            <option value="-1">{{ Lang::txt('COM_FORUM_FILTER_SECTION_SELECT') }}</option>
            @foreach($sections as $sect)
              <option value="{{ $sect->id }}" @selected($filters['section_id'] == $sect->id)>
                {{ $sect->title }}
              </option>
            @endforeach
          </select>
        @endif

        @if($filters['section_id'] && $filters['section_id'] > 0 && count($categories) > 0)
          <label for="field-category_id" class="text-sm">{{ Lang::txt('COM_FORUM_FILTER_CATEGORY') }}:</label>
          <select name="category_id" id="field-category_id"
                  class="select select-bordered select-sm"
                  data-submit-on-change>
            <option value="-1">{{ Lang::txt('COM_FORUM_FILTER_CATEGORY_SELECT') }}</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected($filters['category_id'] == $cat->id)>
                {{ $cat->title }}
              </option>
            @endforeach
          </select>
        @endif
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
            {!! Html::grid('sort', 'COM_FORUM_COL_STICKY', 'sticky', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_ACCESS', 'access', $sortDir, $sort) !!}
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
          <td colspan="9">
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

            $isSticky = (int) $row->get('sticky', 0);
            $stickyTask = $isSticky ? '0' : '1';
            $stickyText = $isSticky ? Lang::txt('COM_FORUM_STICKY') : Lang::txt('COM_FORUM_NOT_STICKY');
            $stickyClass = $isSticky ? 'badge-info' : 'badge-ghost';
            $stickyTitle = $isSticky ? Lang::txt('COM_FORUM_NOT_STICKY') : Lang::txt('COM_FORUM_STICKY');

            $accessLevel = '';
            foreach ($access as $ac) {
                if ($row->get('access') == $ac->value) {
                    $accessLevel = $ac->text;
                    break;
                }
            }

            $rowScope   = $row->get('scope');
            $rowScopeId = $row->get('scope_id');
            $scopeCaption = isset($scopeList[$rowScope][$rowScopeId])
                ? e($scopeList[$rowScope][$rowScopeId]->caption)
                : e($rowScopeId);

            $catId = is_array($filters['category_id']) ? -1 : $filters['category_id'];

            $threadUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&category_id=' . $catId
                . '&task=thread&thread=' . $row->get('thread'),
                false, false
            );

            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&category_id=' . $catId
                . '&task=' . $stateTask
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1',
                false, false
            );

            $stickyUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&category_id=' . $catId
                . '&task=sticky&sticky=' . $stickyTask
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1',
                false, false
            );
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
              <a href="{{ $threadUrl }}"
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
            <td>
              @if($canDo->get('core.edit.state'))
                <a href="{{ $stickyUrl }}"
                   title="{{ Lang::txt('COM_FORUM_SET_TO', $stickyTitle) }}">
                  <span class="badge badge-sm {{ $stickyClass }}">{{ $stickyText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stickyClass }}">{{ $stickyText }}</span>
              @endif
            </td>
            <td>{{ $accessLevel }}</td>
            <td>{{ $rowScope }} ({{ $scopeCaption }})</td>
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
</x-admin-form>
