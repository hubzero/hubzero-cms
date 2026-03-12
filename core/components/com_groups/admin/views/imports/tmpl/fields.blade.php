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

$canDo = \Components\Groups\Helpers\Permissions::getActions('component');

$title = $import->get('id')
    ? Lang::txt('COM_GROUPS_IMPORT_TITLE_EDIT')
    : Lang::txt('COM_GROUPS_IMPORT_TITLE_ADD');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $title, 'import');
if ($canDo->get('core.admin')) {
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();


$__view->css('import');
$__view->js('import');
$__view->js();

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
            <p class="warning">{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_MAPPING_REQUIRED') }}</p>

            @php $__view->view('_fieldmap')->set('import', $import)->display(); @endphp
        </div>
        <div>
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
        </div>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="import[id]" value="{{ $import->get('id') }}" />
    {!! Html::input('token') !!}
</form>
