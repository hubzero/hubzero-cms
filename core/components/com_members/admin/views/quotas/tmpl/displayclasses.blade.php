{{--
  Quota Classes — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');

  if ($canDo->get('core.edit')) {
      Toolbar::addNew('addClass');
      Toolbar::editList('editClass');
      Toolbar::deleteList('COM_MEMBERS_QUOTA_CONFIRM_DELETE', 'deleteClass');
      Toolbar::spacer();
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS_QUOTA_CLASSES') }}"
    icon="user"
/>

@include('com_members::admin.views.quotas.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

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
          <th class="priority-5">{{ Lang::txt('COM_MEMBERS_QUOTA_ID') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_QUOTA_ALIAS') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES') }}</th>
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
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=editClass&id=' . $row->get('id'), false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('alias')) }}" />
            </td>
            <td class="priority-5">
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary">
                  {{ $row->get('id') }}
                </a>
              @else
                {{ $row->get('id') }}
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->get('alias') }}
                </a>
              @else
                {{ $row->get('alias') }}
              @endif
            </td>
            <td class="priority-3">{{ $row->get('soft_blocks') }}</td>
            <td>{{ $row->get('hard_blocks') }}</td>
            <td class="priority-3">{{ $row->get('soft_files') }}</td>
            <td class="priority-2">{{ $row->get('hard_files') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="displayClasses" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
