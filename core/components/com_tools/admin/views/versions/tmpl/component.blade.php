{{--
  Tool Version Zones — Component (iframe) view for zone management

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;

  $__view->js();

  $addZoneUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=addZone&version=' . $version . '&tmpl=component', false);
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="adminForm">

  <div class="overflow-x-auto">
    <table class="table table-sm w-full">
      <thead>
        <tr>
          <th colspan="4" class="text-right pb-2">
            <a href="{{ $addZoneUrl }}"
               class="btn btn-xs btn-outline"
               rel="{type: 'iframe', size: {x: 570, y: 550}}">
              {{ Lang::txt('COM_TOOLS_ADD_ZONE') }}
            </a>
          </th>
        </tr>
        <tr>
          <th>{{ Lang::txt('COM_TOOLS_COL_ZONE_NAME') }}</th>
          <th>{{ Lang::txt('COM_TOOLS_COL_PUBLISH_UP') }}</th>
          <th>{{ Lang::txt('COM_TOOLS_COL_PUBLISH_DOWN') }}</th>
          <th class="w-8"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $row)
          @php
            $mwdb    = \Components\Tools\Helpers\Utils::getMWDBO();
            $zone    = with(new \Components\Tools\Tables\Zones($mwdb));
            $zone->load($row->zone_id);
            $editUrl   = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=editZone&id=' . $row->id . '&tmpl=component', false);
            $removeUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=removeZone&id=' . $row->id . '&version=' . $version . '&tmpl=component&' . Session::getFormToken() . '=1', false);
          @endphp
          <tr>
            <td>
              <a href="{{ $editUrl }}"
                 class="link link-hover"
                 rel="{handler: 'iframe', size: {x: 570, y: 550}}">
                {{ $zone->title }}
              </a>
            </td>
            <td>{{ $row->publish_up }}</td>
            <td>{{ $row->publish_down }}</td>
            <td>
              <a href="{{ $removeUrl }}" class="text-error hover:text-error/80" title="{{ Lang::txt('JDELETE') }}">✕</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted-foreground py-4">—</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option"     value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task"       id="task" value="edit" />
  {!! Html::input('token') !!}
</form>
