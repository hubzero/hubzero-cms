{{--
  Tool Zone Locations — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'zone';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(
    Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES') . ': ' . Lang::txt('COM_TOOLS_LOCATIONS'),
    'tools'
  );
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
  @php
    $addUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller
      . '&task=add&zone=' . ($zone->id ?? 0), false
    );
  @endphp
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th colspan="6" class="bg-base-200 text-left px-4 py-2 text-sm font-semibold">
            {{ Lang::txt('COM_TOOLS_COL_ZONE') }}: {{ $zone->zone ?? '' }}
          </th>
        </tr>
        <tr>
          <th class="column-check">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_IP_FROM', 'ipFROM', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_IP_TO', 'ipTO', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_CONTINENT', 'continent', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_COUNTRY', 'countrySHORT', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            {!! $__view->pagination($total ?? 0, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url(
              'index.php?option=' . $option . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id')
              . '&zone=' . $row->get('zone_id'), false
            );
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->get('id') }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">{{ $row->get('zone') ?? '' }}</a>
            </td>
            <td class="font-mono text-sm">{{ long2ip((int)$row->get('ipFROM')) }}</td>
            <td class="font-mono text-sm">{{ long2ip((int)$row->get('ipTO')) }}</td>
            <td>{{ $row->get('continent') ?? '' }}</td>
            <td>{{ $row->get('countrySHORT') ?? '' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="zone" value="{{ $zone->id ?? 0 }}" />
</x-admin-form>
