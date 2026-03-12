{{--
  Tool Host Types — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'value';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOST_TYPES'), 'tools');
  Toolbar::spacer();
  Toolbar::addNew();
  Toolbar::deleteList();
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_NAME', 'name', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_BIT', 'value', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_DESCRIPTION', 'description', $sortDir, $sort) !!}</th>
          <th class="priority-2">{{ Lang::txt('COM_TOOLS_COL_REFERENCES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $bit     = ($row->value > 0) ? (int)(log($row->value) / log(2)) : '';
            $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&item=' . $row->name, false);
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->name }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->name }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">{{ $row->name }}</a>
            </td>
            <td class="font-mono text-sm">{{ $bit }}</td>
            <td class="priority-3">{{ $row->description }}</td>
            <td class="priority-2">{{ $row->refs ?? 0 }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
