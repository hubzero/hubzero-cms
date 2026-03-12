{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title($group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_MODULES'), 'groups');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
@endphp

@php require_once dirname(__DIR__, 2) . '/pages/tmpl/menu.php'; @endphp

@php
$formUrl = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&gid=' . $group->cn, false
);
@endphp
<form action="{{ $formUrl }}" name="adminForm" id="item-form" method="post">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_MODULES_DETAILS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-title">{{ Lang::txt('COM_GROUPS_MODULES_TITLE') }}:</label>
                    <input
                        type="text"
                        name="module[title]"
                        id="field-title"
                        class="input input-bordered w-full"
                        value="{{ $module->get('title') }}"
                        size="50" />
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-position">{{ Lang::txt('COM_GROUPS_MODULES_POSITION') }}:</label>
                    <input
                        type="text"
                        name="module[position]"
                        id="field-position"
                        class="input input-bordered w-full"
                        value="{{ $module->get('position') }}"
                        size="50" />
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-state">{{ Lang::txt('COM_GROUPS_MODULES_STATUS') }}:</label>
                    <select name="module[state]" id="field-state" class="select select-bordered w-full">
                        @php
                            $states = [
                                1 => Lang::txt('COM_GROUPS_MODULES_STATUS_PUBLISHED'),
                                0 => Lang::txt('COM_GROUPS_MODULES_STATUS_UNPUBLISHED'),
                                2 => Lang::txt('COM_GROUPS_MODULES_STATUS_DELETED'),
                            ];
                            foreach ($states as $k => $v) {
                                $sel = ($module->get('state') == $k) ? 'selected="selected"' : '';
                                echo '<option ' . $sel . ' value="' . $k . '">' . $v . '</option>';
                            }
                        @endphp
                    </select>
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-ordering">{{ Lang::txt('COM_GROUPS_MODULES_ORDERING') }}:</label>
                    <select name="module[ordering]" id="field-ordering" class="select select-bordered w-full">
                        @foreach ($order as $k => $ord)
                            <option
                                value="{{ $k + 1 }}"
                                @if ($ord->get('title') == $module->get('title')) selected="selected" @endif>
                                {{ ($k + 1) . '. ' . $ord->get('title') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </x-admin-fieldset>

            @php
                $menus      = $module->menu('list');
                $activeMenu = (!$module->get('id')) ? [0] : [];
                foreach ($menus as $menu) {
                    $activeMenu[] = $menu->get('pageid');
                }
            @endphp
            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_MODULES_MENU_ASSIGNMENT') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-assignment">{{ Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT') }}:</label>
                    <select name="menu[assignment]" id="field-assignment" class="select select-bordered w-full">
                        <option value="0">{{ Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_ALL') }}</option>
                        <option
                            value=""
                            @if (!in_array(0, $activeMenu)) selected="selected" @endif>
                            {{ Lang::txt('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_SELECTED') }}
                        </option>
                    </select>
                </div>

                <fieldset class="adminform">
                    <legend>{{ Lang::txt('COM_GROUPS_MODULES_MENU_SELECTION') }}</legend>

                    @foreach ($pages as $i => $pg)
                        <div class="input-wrap">
                            <label for="assigned{{ $i }}">
                                <input
                                    type="checkbox"
                                    class="option"
                                    @if (in_array($pg->get('id'), $activeMenu) || in_array(0, $activeMenu)) checked="checked" @endif
                                    name="menu[assigned][]"
                                    id="assigned{{ $i }}"
                                    value="{{ $pg->get('id') }}" /> {{ $pg->get('title') }}
                            </label>
                        </div>
                    @endforeach
                </fieldset>
            </x-admin-fieldset>
        </div>
        <div>
            @if ($module->get('id'))
                <table class="meta">
                    <tbody>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_OWNER') }}</th>
                            <td>{{ $group->get('description') }}</td>
                        </tr>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_ID') }}</th>
                            <td>{{ $module->get('id') }}</td>
                        </tr>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_CREATED') }}</th>
                            <td>{{ Date::of($module->get('created'))->toLocal('F j, Y @ g:ia') }}</td>
                        </tr>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_CREATED_BY') }}</th>
                            <td>
                                @php
                                    $profile = User::getInstance($module->get('created_by'));
                                    echo (is_object($profile))
                                        ? $profile->get('name') . ' (' . $profile->get('id') . ')'
                                        : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_LAST_MODIFIED') }}</th>
                            <td>
                                @php
                                    $modified = '--';
                                    if ($module->get('modified_by') != null) {
                                        $modified = Date::of($module->get('modified'))->toLocal('F j, Y @ g:ia');
                                    }
                                    echo $modified;
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Lang::txt('COM_GROUPS_MODULES_LAST_MODIFIED_BY') }}</th>
                            <td>
                                @php
                                    $modifiedBy = '--';
                                    if ($module->get('modified_by') != null) {
                                        $mbProfile = User::getInstance($module->get('modified_by'));
                                        $modifiedBy = is_object($mbProfile)
                                            ? $mbProfile->get('name') . ' (' . $mbProfile->get('id') . ')'
                                            : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                    }
                                    echo $modifiedBy;
                                @endphp
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_MODULES_MODULE_CONTENT') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-content">{{ Lang::txt('COM_GROUPS_MODULES_CONTENT') }}:</label>
                    <textarea
                        name="module[content]"
                        id="field-content"
                        class="textarea textarea-bordered w-full"
                        rows="20">{{ $module->get('content') }}</textarea>
                </div>
            </x-admin-fieldset>
        </div>
    </div>

    <input type="hidden" name="module[id]" value="{{ $module->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    {!! Html::input('token') !!}
</form>
