{{--
  Tool Hosts — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'hostname';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS'), 'tools');
  Toolbar::spacer();
  Toolbar::addNew();
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('hosts');

  $__view->css();
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
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_NAME', 'hostname', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_SERVICE_HOST', 'service_host', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_PROVISIONS', 'provisions', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_TOOLS_COL_STATUS', 'status', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_USES', 'uses', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone_id', $sortDir, $sort) !!}</th>
          <th class="priority-3">{{ Lang::txt('COM_TOOLS_COL_BROKEN_CONTAINERS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl   = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&hostname=' . $row->hostname, false);
            $statusUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=status&hostname=' . $row->hostname, false);
            $stateClass = ($row->status == 'up') ? 'badge-success' : 'badge-error';

            // Build provisions map
            $provList = [];
            foreach ($hosttypes as $ht) {
                $active = (int)$ht->value & (int)$row->provisions;
                $provList[] = ['name' => $ht->name, 'active' => $active];
            }

            // Broken containers count from middleware DB
            $mwdb = \Components\Tools\Helpers\Utils::getMWDBO();
            $mwdb->setQuery("SELECT count(*) FROM `display` WHERE `status`='broken' AND `hostname`=" . $mwdb->quote($row->hostname));
            $brokenCount = (int)$mwdb->loadResult();
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->hostname }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->hostname }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">{{ $row->hostname }}</a>
            </td>
            <td>{{ $row->service_host }}</td>
            <td>
              <div class="flex flex-wrap gap-1">
                @foreach($provList as $prov)
                  @php
                    $toggleUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=toggle&hostname=' . $row->hostname . '&item=' . $prov['name'], false);
                  @endphp
                  <a href="{{ $toggleUrl }}"
                     class="badge badge-xs {{ $prov['active'] ? 'badge-primary' : 'badge-ghost' }}">
                    {{ $prov['name'] }}
                  </a>
                @endforeach
              </div>
            </td>
            <td class="priority-2">
              <a href="{{ $statusUrl }}" class="badge badge-sm {{ $stateClass }}">
                {{ $row->status }}
              </a>
            </td>
            <td class="priority-3">{{ $row->uses }}</td>
            <td class="priority-4">{{ $row->zone ?? '' }}</td>
            <td class="priority-3">
              @if($brokenCount > 0)
                <span class="badge badge-sm badge-error">{{ $brokenCount }}</span>
              @else
                <span class="text-muted-foreground">0</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
