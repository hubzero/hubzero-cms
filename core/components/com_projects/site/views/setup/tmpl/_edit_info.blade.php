{{--
 * Project info editing form partial
 *
 * Variables:
 *   $model      - Project model object
 *   $option     - Component option string
 *   $config     - Component config Registry
 *   $publishing - Whether publishing is enabled (0/1)
 *   $fields     - Custom description fields (optional)
 *   $data       - Custom field data (optional)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Plugin;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
@endphp

<fieldset>
    <legend class="text-lg font-semibold">{{ ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO')) }}</legend>

    <div class="mb-4">
        <label for="field-name">
            {{ Lang::txt('COM_PROJECTS_ALIAS') }}
            <input
                type="text"
                name="name"
                id="field-name"
                disabled="disabled"
                readonly="readonly"
                class="input input-bordered w-full opacity-50 cursor-not-allowed"
                value="{{ e($model->get('alias')) }}"
            />
        </label>
    </div>

    <div class="mb-4">
        <label for="field-title">
            {{ Lang::txt('COM_PROJECTS_TITLE') }}
            <input
                name="title"
                id="field-title"
                maxlength="250"
                type="text"
                class="input input-bordered w-full"
                value="{{ e($model->get('title')) }}"
            />
        </label>
    </div>

    <div class="mb-4">
        <label for="field-about">
            {{ Lang::txt('COM_PROJECTS_ABOUT') }}
            {!! $__view->editor(
                'about',
                e($model->about('raw')),
                35,
                25,
                'about',
                ['class' => 'form-control minimal no-footer']
            ) !!}
        </label>
    </div>

    @include('setup::_picture', [
        'model'  => $model,
        'option' => $option,
    ])
</fieldset>

@if (isset($fields) && !empty($fields))
    <fieldset>
        <legend class="text-lg font-semibold">{{ ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO_EXTENDED')) }}</legend>

        @php
            $xml = \Components\Projects\Models\Orm\Description\Field::toXml($fields, 'edit');
            \Hubzero\Form\Form::addFieldPath(
                Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description' . DS . 'fields'
            );
            $form = new \Hubzero\Form\Form('description', ['control' => 'description']);
            $form->load($xml);
            $form->bind($data);
        @endphp

        @foreach ($form->getFieldsets() as $fieldset)
            @foreach ($form->getFieldset($fieldset->name) as $field)
                {!! $field->label !!}
                {!! $field->input !!}
                {!! $field->description !!}
            @endforeach
        @endforeach
    </fieldset>
@endif

<fieldset>
    <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_ACCESS') }}</legend>

    <input type="hidden" name="private" value="{{ e($model->get('private')) }}" />

    <div class="mb-4">
        <label for="field-access">
            {{ Lang::txt('COM_PROJECTS_PRIVACY_EDIT') }}
            <select class="select select-bordered w-full" name="access" id="field-access">
                @php
                    $access = $model->get('access');
                    $hasCustomAccess = ($access && !in_array($access, [1, 2, 5]));
                @endphp
                @if ($hasCustomAccess || User::authorise('core.manage', 'com_projects'))
                    {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $access) !!}
                @else
                    <option value="1" @selected($model->get('access') == 1)>
                        {!! Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC') !!}
                    </option>
                    <option value="2" @selected($model->get('access') == 2)>
                        {!! Lang::txt('COM_PROJECTS_PRIVACY_EDIT_REGISTERED') !!}
                    </option>
                    <option value="5" @selected($model->get('access') == 5)>
                        {{ Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE') }}
                    </option>
                @endif
            </select>
        </label>
    </div>

    <fieldset id="access-public" @class(['' => true, 'hidden' => $model->get('access') == 5])>
        <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_OPTIONS_FOR_PUBLIC') }}</legend>

        <div class="mb-4 form-check">
            <label for="params-allow_membershiprequest" class="form-check-label">
                <input type="hidden" name="params[allow_membershiprequest]" value="0" />
                <input
                    type="checkbox"
                    class="checkbox checkbox-primary form-check-input"
                    name="params[allow_membershiprequest]"
                    id="params-allow_membershiprequest"
                    value="1"
                    @checked($model->params->get('allow_membershiprequest'))
                /> {{ Lang::txt('COM_PROJECTS_MEMBERSHIPREQUEST') }}
            </label>
        </div>

        <div class="mb-4 form-check">
            <label for="params-team_public" class="form-check-label">
                <input type="hidden" name="params[team_public]" value="0" />
                <input
                    type="checkbox"
                    class="checkbox checkbox-primary form-check-input"
                    name="params[team_public]"
                    id="params-team_public"
                    value="1"
                    @checked($model->params->get('team_public'))
                /> {!! Lang::txt('COM_PROJECTS_TEAM_PUBLIC') !!}
            </label>
        </div>

        @if ($publishing)
            <div class="mb-4 form-check">
                <label for="params-publications_public" class="form-check-label">
                    <input type="hidden" name="params[publications_public]" value="0" />
                    <input
                        type="checkbox"
                        class="checkbox checkbox-primary form-check-input"
                        name="params[publications_public]"
                        id="params-publications_public"
                        value="1"
                        @checked($model->params->get('publications_public'))
                    /> {!! Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC') !!}
                </label>
            </div>
        @endif

        @php
            $notesParams = Plugin::params('projects', 'notes');
        @endphp
        @if ($notesParams->get('enable_publinks'))
            <div class="mb-4 form-check">
                <label for="params-notes_public" class="form-check-label">
                    <input type="hidden" name="params[notes_public]" value="0" />
                    <input
                        type="checkbox"
                        class="checkbox checkbox-primary form-check-input"
                        name="params[notes_public]"
                        id="params-notes_public"
                        value="1"
                        @checked($model->params->get('notes_public'))
                    /> {!! Lang::txt('COM_PROJECTS_NOTES_PUBLIC') !!}
                </label>
            </div>
        @endif

        @php
            $filesParams = Plugin::params('projects', 'files');
        @endphp
        @if ($filesParams->get('enable_publinks'))
            <div class="mb-4 form-check">
                <label for="params-files_public" class="form-check-label">
                    <input type="hidden" name="params[files_public]" value="0" />
                    <input
                        type="checkbox"
                        class="checkbox checkbox-primary form-check-input"
                        name="params[files_public]"
                        id="params-files_public"
                        value="1"
                        @checked($model->params->get('files_public'))
                    /> {!! Lang::txt('COM_PROJECTS_FILES_PUBLIC') !!}
                </label>
            </div>
        @endif
    </fieldset>
</fieldset>

@if ($config->get('grantinfo', 0))
    @include('setup::_edit_grant_info', [
        'model' => $model,
    ])
@endif

<div class="flex gap-2 mt-6">
    <input type="submit" class="btn btn-success" value="{{ Lang::txt('COM_PROJECTS_SAVE_CHANGES') }}" />
    <a
        href="{{ Route::url('index.php?option=' . $option . '&alias=' . $model->get('alias') . '&active=info') }}"
        class="btn btn-ghost"
    >{{ Lang::txt('JCANCEL') }}</a>
</div>
