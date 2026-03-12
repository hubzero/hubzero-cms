{{--
  Tool Handlers — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'tool.title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HANDLERS'), 'tools');
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
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_HANDLERS_TOOLNAME', 'tool.title', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_TOOLS_HANDLERS_PROMPT') }}</th>
          <th>{{ Lang::txt('COM_TOOLS_HANDLERS_RULES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            {!! $rows->pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false);
            $rules = [];
            foreach ($row->rules as $r) {
              $rules[] = $r->extension . ':' . $r->quantity;
            }
          @endphp
          <tr>
            <td>
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item
                     aria-label="{{ $row->get('id') }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">
                {{ $row->tool->title }}
              </a>
            </td>
            <td>{{ $row->prompt }}</td>
            <td>
              @if(count($rules))
                <span class="text-sm font-mono">{{ implode(', ', $rules) }}</span>
              @else
                <span class="text-muted-foreground">—</span>
              @endif
            </td>
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
