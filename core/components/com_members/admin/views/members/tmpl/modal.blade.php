{{--
  Members — User picker modal (tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $field     = Request::getCmd('field');
  $function  = 'jSelectUser_' . $field;
  $listOrder = $filters['sort'] ?? 'name';
  $listDirn  = $filters['sort_Dir'] ?? 'asc';

  $formUrl = Route::url(
      'index.php?option=com_members&controller=members&task=modal'
      . '&tmpl=component'
      . '&groups=' . Request::getString('groups', '')
      . '&excluded=' . Request::getString('excluded', ''), false
  );
@endphp

<h2 class="modal-title">{{ Lang::txt('Users') }}</h2>

<form action="{!! $formUrl !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <fieldset id="filter-bar" class="admin-filters flex flex-wrap items-center gap-2 w-full">
    <div class="flex items-center gap-2">
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_IN_NAME') }}" />
      <button type="submit" class="btn btn-sm">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm filter-clear">
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
      <button type="button"
              class="btn btn-sm"
              data-parent-callback="{{ $function }}"
              data-callback-args="{{ json_encode(['', Lang::txt('JLIB_FORM_SELECT_USER')]) }}">
        {{ Lang::txt('JOPTION_NO_USER') }}
      </button>
    </div>
    <div class="flex items-center gap-2 ml-auto">
      {!! Html::access(
          'usergroup',
          'filter_group_id',
          $filters['group_id'] ?? '',
          'class="select select-bordered select-sm" data-submit-on-change'
      ) !!}
    </div>
  </fieldset>

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col" class="text-left">
          {!! Html::grid('sort', 'COM_MEMBERS_HEADING_NAME', 'a.name', $listDirn, $listOrder) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_MEMBERS_HEADING_GROUPS', 'group_names', $listDirn, $listOrder) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $groups = [];
          foreach ($row->accessgroups as $agroup) {
              $groups[] = $accessgroups->seek($agroup->get('group_id'))
                  ->get('title');
          }
          $groupNames = implode('<br />', $groups);
          
        @endphp
        <tr>
          <td>
            <a class="pointer cursor-pointer"
               data-parent-callback="{{ $function }}"
               data-callback-args="{{ json_encode([$row->get('id'), $row->get('name')]) }}">
              {{ $row->get('name') }}
            </a>
          </td>
          <td class="text-center">
            {{ $row->get('username') }}
          </td>
          <td>
            {!! $groupNames !!}
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="task" value="" />
  <input type="hidden" name="field" value="{{ $field }}" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $listOrder }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $listDirn }}" />
  {!! Html::input('token') !!}
</form>
