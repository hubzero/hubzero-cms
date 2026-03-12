{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

$tmpl = Request::getCmd('tmpl', '');

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

if ($tmpl != 'component') {
    $text = isset($task) && $task == 'edit'
        ? Lang::txt('COM_GROUPS_EDIT')
        : Lang::txt('COM_GROUPS_NEW');

    Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
    if ($canDo->get('core.edit')) {
        Toolbar::save();
    }
    Toolbar::cancel();
}


$__view->js('membership.blade.js');

$formUrl = Route::url('index.php?option=' . $option, false);
$redirectUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&gid=' . $group->get('cn'), false
);
$invalidMsg = Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION');
@endphp

@if ($__view->getError())
    <p class="error">{!! implode('<br />', $__view->getError()) !!}</p>
@endif

<form
    action="{{ $formUrl }}"
    method="post"
    name="adminForm"
    id="component-form"
    data-redirect="{{ $redirectUrl }}"
    data-invalid-msg="{{ $invalidMsg }}">
    @if ($tmpl == 'component')
        <fieldset>
            <div class="configuration">
                <div class="fltrt configuration-options">
                    <button type="button" id="btn-save" class="btn btn-primary">{{ Lang::txt('COM_GROUPS_MEMBER_SAVE') }}</button>
                    <button type="button" id="btn-cancel" class="btn">{{ Lang::txt('COM_GROUPS_MEMBER_CANCEL') }}</button>
                </div>
                {{ Lang::txt('COM_GROUPS_MEMBER_ADD') }}
            </div>
        </fieldset>
    @endif

    <div>
        <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_DETAILS') }}">

            <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="no_html" value="{{ $tmpl == 'component' ? '1' : '0' }}" />
            <input type="hidden" name="task" value="addusers" />

            <table class="admin-table">
                <tbody>
                    <tr>
                        <th>
                            <label class="label text-base-content" for="field-usernames">{{ Lang::txt('COM_GROUPS_ADD_USERNAME') }}:</label>
                        </th>
                        <td>
                            <input
                                type="text"
                                name="usernames"
                                class="input-username input input-bordered w-full"
                                id="field-usernames"
                                value=""
                                size="50" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label class="label text-base-content" for="field-tbl">{{ Lang::txt('COM_GROUPS_TO') }}:</label>
                        </th>
                        <td>
                            <select name="tbl" id="field-tbl" class="select select-bordered w-full">
                                <option value="invitees">{{ Lang::txt('COM_GROUPS_INVITEES') }}</option>
                                <option value="applicants">{{ Lang::txt('COM_GROUPS_APPLICANTS') }}</option>
                                <option value="members" selected="selected">{{ Lang::txt('COM_GROUPS_MEMBERS') }}</option>
                                <option value="managers">{{ Lang::txt('COM_GROUPS_MANAGERS') }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-admin-fieldset>
    </div>

    {!! Html::input('token') !!}
</form>
