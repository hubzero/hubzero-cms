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

$__view->css('import');
$__view->js('import');

$canDo = \Components\Groups\Helpers\Permissions::getActions('component');

$title = $import->get('id')
    ? Lang::txt('COM_GROUPS_IMPORT_TITLE_EDIT')
    : Lang::txt('COM_GROUPS_IMPORT_TITLE_ADD');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $title, 'import');
if ($canDo->get('core.admin')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();


$__view->js();

// Parse hooks
$hooksData = json_decode($import->get('hooks'));
if (!is_object($hooksData)) {
    $hooksData = new stdClass();
}
$hooksData->postparse   = isset($hooksData->postparse)   ? $hooksData->postparse   : [];
$hooksData->postmap     = isset($hooksData->postmap)     ? $hooksData->postmap     : [];
$hooksData->postconvert = isset($hooksData->postconvert) ? $hooksData->postconvert : [];

$formAction = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&task=save', false
);
$invalidMsg = Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED');
@endphp

@foreach ($__view->getErrors() as $error)
    <p class="error">{{ $error }}</p>
@endforeach

<form
    action="{{ $formAction }}"
    method="post"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
    class="editform form-validate"
    data-invalid-msg="{{ $invalidMsg }}">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">
        <div>
            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_DETAILS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-name">
                        {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_NAME') }}
                        <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                    </label>
                    <input
                        type="text"
                        name="import[name]"
                        id="field-name"
                        class="required input input-bordered w-full"
                        value="{{ $import->get('name') }}" />
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-notes">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_NOTES') }}</label>
                    <textarea
                        name="import[notes]"
                        id="field-notes"
                        class="textarea textarea-bordered w-full"
                        rows="5">{{ $import->get('notes') }}</textarea>
                </div>
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_HOOKS') }}">

                @if ($hooks->count())
                    @php $hooksHint = Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_HINT'); @endphp
                    <div class="input-wrap" data-hint="{{ $hooksHint }}">
                        <label class="label text-base-content" for="field-hookpostparse">
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_POSTPARSEHOOK') }}
                        </label>
                        <select name="hooks[postparse][]" id="field-hookpostparse" class="select select-bordered w-full" multiple="multiple">
                            @foreach ($hooksData->postparse as $hookId)
                                @php $importHook = $hooks->fetch('id', $hookId); @endphp
                                <option selected="selected" value="{{ $importHook->get('id') }}">
                                    {{ $importHook->get('name') }}
                                </option>
                            @endforeach
                            @foreach ($hooks as $hook)
                                @if ($hook->get('event') != 'postparse' || in_array($hook->get('id'), $hooksData->postparse))
                                    @continue
                                @endif
                                <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
                            @endforeach
                        </select>
                        <a class="hook-up" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
                        <a class="hook-down" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
                        <span class="hint">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
                    </div>

                    <div class="input-wrap" data-hint="{{ $hooksHint }}">
                        <label class="label text-base-content" for="field-hookpostmap">
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_POSTMAPHOOK') }}
                        </label>
                        <select name="hooks[postmap][]" id="field-hookpostmap" class="select select-bordered w-full" multiple="multiple">
                            @foreach ($hooksData->postmap as $hookId)
                                @php $importHook = $hooks->fetch('id', $hookId); @endphp
                                <option selected="selected" value="{{ $importHook->get('id') }}">
                                    {{ $importHook->get('name') }}
                                </option>
                            @endforeach
                            @foreach ($hooks as $hook)
                                @if ($hook->get('event') != 'postmap' || in_array($hook->get('id'), $hooksData->postmap))
                                    @continue
                                @endif
                                <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
                            @endforeach
                        </select>
                        <a class="hook-up" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
                        <a class="hook-down" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
                        <span class="hint">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
                    </div>

                    <div class="input-wrap" data-hint="{{ $hooksHint }}">
                        <label class="label text-base-content" for="field-hookpostconvert">
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_POSTCONVERTHOOK') }}
                        </label>
                        <select name="hooks[postconvert][]" id="field-hookpostconvert" class="select select-bordered w-full" multiple="multiple">
                            @foreach ($hooksData->postconvert as $hookId)
                                @php $importHook = $hooks->fetch('id', $hookId); @endphp
                                <option selected="selected" value="{{ $importHook->get('id') }}">
                                    {{ $importHook->get('name') }}
                                </option>
                            @endforeach
                            @foreach ($hooks as $hook)
                                @if ($hook->get('event') != 'postconvert' || in_array($hook->get('id'), $hooksData->postconvert))
                                    @continue
                                @endif
                                <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
                            @endforeach
                        </select>
                        <a class="hook-up" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
                        <a class="hook-down" href="#">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
                        <span class="hint">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
                    </div>
                @else
                    <div class="input-wrap">
                        <em>{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_NO_HOOKS_FOUND') }}</em>
                        <input type="hidden" name="hooks[postparse][]" value="" />
                        <input type="hidden" name="hooks[postmap][]" value="" />
                        <input type="hidden" name="hooks[postconvert][]" value="" />
                    </div>
                @endif
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_PARAMS') }}">

                @php $approvedHint = Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_APPROVED_HINT'); @endphp
                <div class="input-wrap" data-hint="{{ $approvedHint }}">
                    <label class="label text-base-content" for="param-approved">
                        {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_APPROVED') }}
                    </label>
                    <select name="params[approved]" id="param-approved" class="select select-bordered w-full">
                        <option value="0"{{ $params->get('approved', 1) == 0 ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('JNO') }}
                        </option>
                        <option value="1"{{ $params->get('approved', 1) == 1 ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('JYES') }}
                        </option>
                    </select>
                    <span class="hint">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_APPROVED_HINT') }}</span>
                </div>
            </x-admin-fieldset>

            @php $__view->view('_fieldmap')->set('import', $import)->display(); @endphp
        </div>
        <div>
            @if ($import->get('id'))
                <table class="meta">
                    <tbody>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_ID') }}</th>
                            <td>{{ $import->get('id') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_CREATEDBY') }}</th>
                            <td>
                                @php $createdBy = User::getInstance($import->get('created_by')); @endphp
                                @if ($createdBy)
                                    {{ $createdBy->get('name') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_CREATEDON') }}</th>
                            <td>{{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_UPLOAD') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-file">
                        {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATAFILEUPLOAD') }}
                    </label>
                    <input type="file" name="file" id="field-file" class="file-input file-input-bordered" />
                </div>
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_DATA') }}">

                @php
                $dataFileHint = Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $import->fileSpacePath());
                @endphp
                <div class="input-wrap" data-hint="{{ $dataFileHint }}">
                    <label class="label text-base-content" for="field-importfile">
                        {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_FILE') }}
                    </label>
                    <select name="import[file]" id="field-importfile" class="select select-bordered w-full">
                        <option value="">
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL') }}
                        </option>
                        @if (isset($files))
                            @foreach ($files as $file)
                                @php $file = ltrim($file, DS); @endphp
                                <option
                                    {{ $import->get('file') == $file ? 'selected="selected"' : '' }}
                                    value="{{ $file }}">{{ $file }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="hint">{{ $dataFileHint }}</span>
                </div>

                @php $dataModeHint = Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); @endphp
                <div class="input-wrap" data-hint="{{ str_replace('<br />', "\n", $dataModeHint) }}">
                    <label class="label text-base-content" for="field-importmode">
                        {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_MODE') }}
                    </label>
                    <select name="import[mode]" id="field-importmode" class="select select-bordered w-full">
                        <option value="UPDATE">
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE') }}
                        </option>
                        <option value="PATCH"{{ $import->get('mode') == 'PATCH' ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_MODE_PATCH') }}
                        </option>
                    </select>
                    <span class="hint">{!! Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELD_DATA_MODE_HINT') !!}</span>
                </div>
            </x-admin-fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="import[id]" value="{{ $import->get('id') }}" />
    {!! Html::input('token') !!}
</form>
