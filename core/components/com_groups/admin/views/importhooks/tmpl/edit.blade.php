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

$title = $hook->get('id')
    ? Lang::txt('COM_GROUPS_IMPORTHOOK_TITLE_EDIT')
    : Lang::txt('COM_GROUPS_IMPORTHOOK_TITLE_ADD');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt($title), 'import');
Toolbar::save();
Toolbar::cancel();


$__view->js();

$formAction = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller, false
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
            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELDSET_DETAILS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-event">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_TYPE') }}
                    </label>
                    <select name="hook[event]" id="field-event" class="select select-bordered w-full">
                        <option value="postparse"{{ $hook->get('event') == 'postparse' || !$hook->get('event') ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTPARSE') }}
                        </option>
                        <option value="postmap"{{ $hook->get('event') == 'postmap' ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTMAP') }}
                        </option>
                        <option value="postconvert"{{ $hook->get('event') == 'postconvert' ? ' selected="selected"' : '' }}>
                            {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTCONVERT') }}
                        </option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-name">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_NAME') }}
                    </label>
                    <input
                        type="text"
                        name="hook[name]"
                        id="field-name"
                        class="input input-bordered w-full"
                        value="{{ $hook->get('name') }}" />
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-notes">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_NOTES') }}
                    </label>
                    <textarea
                        name="hook[notes]"
                        id="field-notes"
                        class="textarea textarea-bordered w-full"
                        rows="5">{{ $hook->get('notes') }}</textarea>
                </div>
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELDSET_FILE') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-script">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_SCRIPT') }}
                    </label>
                    @if ($hook->get('file'))
                        @php
                        $scriptCurrent = Lang::txt(
                            'COM_GROUPS_IMPORTHOOK_EDIT_FIELD_SCRIPT_CURRENT',
                            $hook->get('file')
                        );
                        $rawUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=raw&id=' . $hook->get('id'), false
                        );
                        $viewRaw = Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_SCRIPT_VIEWRAW');
                        @endphp
                        {!! $scriptCurrent !!}
                        &mdash;
                        <a rel="noopener noreferrer" target="_blank" href="{{ $rawUrl }}">
                            {{ $viewRaw }}
                        </a><br />
                    @endif
                    <input type="file" name="file" id="field-script" class="file-input file-input-bordered" />
                </div>
            </x-admin-fieldset>
        </div>
        <div>
            @if ($hook->get('id'))
                <table class="meta">
                    <tbody>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_ID') }}</th>
                            <td>{{ $hook->get('id') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_CREATEDBY') }}</th>
                            <td>
                                @php $createdBy = User::getInstance($hook->get('created_by')); @endphp
                                @if ($createdBy)
                                    {{ $createdBy->get('name') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_IMPORTHOOK_EDIT_FIELD_CREATEDON') }}</th>
                            <td>{{ Date::of($hook->get('created_at'))->toLocal('m/d/Y @ g:i a') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="hook[id]" value="{{ $hook->get('id') }}" />
    <input type="hidden" name="hook[type]" value="{{ $hook->get('type') }}" />
    {!! Html::input('token') !!}
</form>
