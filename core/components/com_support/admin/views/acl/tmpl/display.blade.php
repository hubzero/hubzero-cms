{{--
  Support — ACL management

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

@php
  \Hubzero\Facades\Toolbar::title(
      Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_ACL'),
      'support'
  );
  \Hubzero\Facades\Toolbar::deleteList();
  \Hubzero\Facades\Toolbar::spacer();
  \Hubzero\Facades\Toolbar::help('acl');
@endphp

@php $__view->js('edit.blade.js'); @endphp

<form
    action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <div class="overflow-x-auto">
    <table class="table table-sm w-full">
        <thead>
            <tr class="bg-base-200">
                <th class="w-8"></th>
                <th class="w-12"></th>
                <th></th>
                <th></th>
                <th colspan="3" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_TICKETS') }}
                </th>
                <th colspan="2" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_COMMENTS') }}
                </th>
                <th colspan="2" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_PRIVATE_COMMENTS') }}
                </th>
                <th class="w-20"></th>
            </tr>
            <tr class="bg-base-200">
                <th class="text-center">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox checkbox-sm checkbox-toggle toggle-all"
                        data-check-all="id[]"
                    />
                    <label for="checkall-toggle" class="sr-only">
                        {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
                    </label>
                </th>
                <th scope="col">{{ Lang::txt('COM_SUPPORT_COL_ID') }}</th>
                <th scope="col">{{ Lang::txt('COM_SUPPORT_COL_OBJECT') }}</th>
                <th scope="col">{{ Lang::txt('COM_SUPPORT_COL_MODEL') }}</th>
                <th scope="col" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_READ') }}
                </th>
                <th scope="col" class="text-center">{{ Lang::txt('COM_SUPPORT_COL_UPDATE') }}</th>
                <th scope="col" class="text-center">{{ Lang::txt('COM_SUPPORT_COL_DELETE') }}</th>
                <th scope="col" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_CREATE') }}
                </th>
                <th scope="col" class="text-center">{{ Lang::txt('COM_SUPPORT_COL_READ') }}</th>
                <th scope="col" class="text-center border-l border-base-300">
                    {{ Lang::txt('COM_SUPPORT_COL_CREATE') }}
                </th>
                <th scope="col" class="text-center">{{ Lang::txt('COM_SUPPORT_COL_READ') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-base-50">
                <td></td>
                <td>
                    <input type="hidden" name="aro[id]" id="aro_id" value="" />
                </td>
                <td>
                    <label for="aro_foreign_key" class="label-text text-xs text-base-content">
                        {{ Lang::txt('COM_SUPPORT_ACL_ALIAS_ID') }}:
                    </label>
                    <input
                        type="text"
                        name="aro[foreign_key]"
                        id="aro_foreign_key"
                        class="input input-bordered input-sm w-36 text-base-content"
                        value=""
                    />
                </td>
                <td>
                    <div class="flex items-center gap-2">
                        <label for="aro_model" class="sr-only">{{ Lang::txt('COM_SUPPORT_ACL_MODEL') }}</label>
                        <select name="aro[model]" id="aro_model" class="select select-bordered select-sm">
                            <option value="user">{{ Lang::txt('COM_SUPPORT_ACL_USER') }}</option>
                            <option value="group">{{ Lang::txt('COM_SUPPORT_ACL_GROUP') }}</option>
                        </select>
                        <label class="flex items-center gap-1 text-xs cursor-pointer text-base-content">
                            <input type="checkbox" name="toggleOpt" id="toggleOpt" value=""
                                   class="checkbox checkbox-sm" />
                            <abbr title="{{ Lang::txt('COM_SUPPORT_CHECK_ALL') }}">
                                {{ Lang::txt('COM_SUPPORT_COL_ALL') }}
                            </abbr>
                        </label>
                    </div>
                </td>
                <td class="text-center border-l border-base-300">
                    <input type="hidden" name="map[tickets][id]" value="0" />
                    <input type="hidden" name="map[tickets][aro_id]" value="0" />
                    <input type="hidden" name="map[tickets][aco_id]" value="1" />
                    <input type="hidden" name="map[tickets][action_create]" value="1" />
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[tickets][action_read]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_TICKETS') }} {{ Lang::txt('COM_SUPPORT_COL_READ') }}" />
                </td>
                <td class="text-center">
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[tickets][action_update]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_TICKETS') }} {{ Lang::txt('COM_SUPPORT_COL_UPDATE') }}" />
                </td>
                <td class="text-center">
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[tickets][action_delete]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_TICKETS') }} {{ Lang::txt('COM_SUPPORT_COL_DELETE') }}" />
                </td>
                <td class="text-center border-l border-base-300">
                    <input type="hidden" name="map[comments][id]" value="0" />
                    <input type="hidden" name="map[comments][aro_id]" value="0" />
                    <input type="hidden" name="map[comments][aco_id]" value="2" />
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[comments][action_create]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_COMMENTS') }} {{ Lang::txt('COM_SUPPORT_COL_CREATE') }}" />
                </td>
                <td class="text-center">
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[comments][action_read]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_COMMENTS') }} {{ Lang::txt('COM_SUPPORT_COL_READ') }}" />
                    <input type="hidden" name="map[comments][action_update]" value="0" />
                    <input type="hidden" name="map[comments][action_delete]" value="0" />
                </td>
                <td class="text-center border-l border-base-300">
                    <input type="hidden" name="map[private_comments][id]" value="0" />
                    <input type="hidden" name="map[private_comments][aro_id]" value="0" />
                    <input type="hidden" name="map[private_comments][aco_id]" value="3" />
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[private_comments][action_create]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_PRIVATE_COMMENTS') }} {{ Lang::txt('COM_SUPPORT_COL_CREATE') }}" />
                </td>
                <td class="text-center">
                    <input type="checkbox" class="checkbox checkbox-sm chk"
                           name="map[private_comments][action_read]" value="1"
                           aria-label="{{ Lang::txt('COM_SUPPORT_COL_PRIVATE_COMMENTS') }} {{ Lang::txt('COM_SUPPORT_COL_READ') }}" />
                    <input type="hidden" name="map[private_comments][action_update]" value="0" />
                    <input type="hidden" name="map[private_comments][action_delete]" value="0" />
                </td>
                <td>
                    <button type="submit" name="newacl" id="newacl"
                            class="btn btn-sm btn-primary">
                        {{ Lang::txt('Add') }}
                    </button>
                </td>
            </tr>
        </tfoot>
        <tbody>
            @php $i = 0; @endphp
            @foreach ($rows as $row)
                @php
                  $db = App::get('db');
                  $sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key FROM `#__support_acl_aros_acos` AS m LEFT JOIN `#__support_acl_aros` AS r ON m.aro_id=r.id LEFT JOIN `#__support_acl_acos` AS c ON m.aco_id=c.id WHERE r.foreign_key=" . $db->quote($row->foreign_key) . " AND r.model=" . $db->quote($row->model) . " ORDER BY aro_foreign_key, aro_model";
                  $db->setQuery($sql);
                  $lines = $db->loadObjectList();
                  $data = [
                      'tickets'          => ['id' => 0, 'create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                      'comments'         => ['id' => 0, 'create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                      'private_comments' => ['id' => 0, 'create' => 0, 'read' => 0, 'update' => 0, 'delete' => 0],
                  ];
                  foreach ($lines as $line) {
                      $data[$line->aco_model]['id']     = $line->id;
                      $data[$line->aco_model]['create'] = $line->action_create;
                      $data[$line->aco_model]['read']   = $line->action_read;
                      $data[$line->aco_model]['update'] = $line->action_update;
                      $data[$line->aco_model]['delete'] = $line->action_delete;
                  }
                @endphp
                @include('com_support::admin/views/acl/tmpl/_acl_aro_row', ['i' => $i, 'data' => $data, 'row' => $row])
                @php $i++; @endphp
            @endforeach
        </tbody>
    </table>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />

    {!! Html::input('token') !!}
</form>
