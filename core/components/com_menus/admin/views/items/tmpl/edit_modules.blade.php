{{--
  com_menus items — Module assignment partial

  Shows which modules are assigned to this menu item.
  "Hide unassigned" toggle is handled by the checkbox + CSS.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div class="mb-3 flex items-center gap-2">
  <input type="checkbox" id="showmods" class="checkbox checkbox-sm" />
  <label for="showmods" class="text-sm cursor-pointer">
    {{ Lang::txt('COM_MENUS_ITEM_FIELD_HIDE_UNASSIGNED') }}
  </label>
</div>

<div class="overflow-x-auto">
  <table class="admin-table">
    <thead>
      <tr>
        <th>{{ Lang::txt('COM_MENUS_HEADING_ASSIGN_MODULE') }}</th>
        <th class="text-center w-24">{{ Lang::txt('COM_MENUS_HEADING_DISPLAY') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($modules as $i => $module)
        @php
          $isUnassigned = is_null($module->menuid);
          $isExcluded   = $isUnassigned && (!$module->except || $module->menuid < 0);
          $rowClass     = $isExcluded ? 'opacity-40' : '';
        @endphp
        <tr class="{{ $rowClass }}" data-module-row="{{ $isExcluded ? 'unassigned' : 'assigned' }}">
          <td class="text-sm">
            @php
              $link      = Route::url('index.php?option=com_modules&client_id=0&task=edit&id=' . $module->id . '&tmpl=component&view=module&layout=modal', false);
              $modAccess = Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', e($module->title), e($module->access_title), e($module->position));
            @endphp
            <a href="{{ $link }}" class="link link-hover" title="{{ Lang::txt('COM_MENUS_EDIT_MODULE_SETTINGS') }}">
              {!! $modAccess !!}
            </a>
          </td>
          <td class="text-center text-sm">
            @if(is_null($module->menuid))
              @if($module->except)
                <span class="badge badge-success badge-sm">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-ghost badge-sm">{{ Lang::txt('JNO') }}</span>
              @endif
            @elseif($module->menuid > 0)
              <span class="badge badge-success badge-sm">{{ Lang::txt('JYES') }}</span>
            @elseif($module->menuid < 0)
              <span class="badge badge-ghost badge-sm">{{ Lang::txt('JNO') }}</span>
            @else
              <span class="badge badge-info badge-sm">{{ Lang::txt('JALL') }}</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
