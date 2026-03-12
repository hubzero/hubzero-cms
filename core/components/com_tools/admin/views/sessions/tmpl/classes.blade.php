{{--
  Tool Session Classes — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_TOOLS_SESSION_CLASSES'), 'user');
  Toolbar::addNew();
  Toolbar::editList();
  Toolbar::deleteList('COM_TOOLS_SESSION_CLASSES_CONFIRM_DELETE', 'delete');
  Toolbar::spacer();
  Toolbar::help('sessionclasses');
@endphp

@include('com_tools::admin.views.sessions.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    task="classes"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-3">{{ Lang::txt('COM_TOOLS_SESSION_CLASS_ID') }}</th>
          <th>{{ Lang::txt('COM_TOOLS_SESSION_CLASS_ALIAS') }}</th>
          <th>{{ Lang::txt('COM_TOOLS_SESSION_CLASS_JOBS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->id, false);
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->id }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->id }}" />
            </td>
            <td class="priority-3">
              <a href="{{ $editUrl }}" class="link link-hover">{{ $row->id }}</a>
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">{{ $row->alias }}</a>
            </td>
            <td>{{ $row->jobs }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
