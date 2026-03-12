{{--
  Support — Query folder edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $tmpl    = Request::getWord('tmpl', '');
  $no_html = Request::getInt('no_html', 0);
  $text    = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  $toolbarTitle = Lang::txt('COM_SUPPORT_TICKETS') . ': '
      . Lang::txt('COM_SUPPORT_QUERY_FOLDER') . ': ' . $text;

  $formAction   = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $formId       = ($tmpl == 'component') ? 'component' : 'item';
  $invalidMsg   = e(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
@endphp

@if(!$no_html && !$tmpl)
  @php
    \Hubzero\Facades\Toolbar::title($toolbarTitle, 'support');
    \Hubzero\Facades\Toolbar::apply();
    \Hubzero\Facades\Toolbar::save();
    \Hubzero\Facades\Toolbar::spacer();
    \Hubzero\Facades\Toolbar::cancel();
  @endphp
@endif

<form
    action="{{ $formAction }}"
    method="post"
    name="adminForm"
    id="{{ $formId }}-form"
    class="editform form-validate"
    data-invalid-msg="{{ $invalidMsg }}"
>
    @if($tmpl == 'component')
        <fieldset>
            <div class="configuration">
                <div class="configuration-options">
                    <button type="button" id="btn-apply" data-task="applyfolder">
                        {{ Lang::txt('JAPPLY') }}
                    </button>
                    <button type="button" id="btn-save" data-task="savefolder">
                        {{ Lang::txt('JSAVE') }}
                    </button>
                    <button
                        type="button"
                        id="btn-cancel"
                        @if(Request::getBool('refresh', 0)) data-refreah="true" @endif
                    >{{ Lang::txt('JCANCEL') }}</button>
                </div>

                {{ Lang::txt('COM_SUPPORT_QUERY_FOLDER') . ': ' . $text }}
            </div>
        </fieldset>
    @endif

    @if(!$tmpl)
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-7">
            <fieldset class="adminform">
                <legend><span>{{ Lang::txt('JDETAILS') }}</span></legend>

                <div class="input-wrap">
                    <label for="field-title">
                        {{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}:
                        <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                    </label>
                    <input
                        type="text"
                        name="fields[title]"
                        id="field-title"
                        class="required"
                        value="{{ $row->title }}"
                    />
                </div>

                <div class="input-wrap" data-hint="{{ Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT') }}">
                    <label for="field-alias">
                        {{ Lang::txt('COM_SUPPORT_FIELD_ALIAS') }}:
                    </label>
                    <input
                        type="text"
                        name="fields[alias]"
                        id="field-alias"
                        value="{{ $row->alias }}"
                    />
                    <span class="hint">{{ Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT') }}</span>
                </div>
            </fieldset>
        </div>
        <div class="md:col-span-5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row">{{ Lang::txt('COM_SUPPORT_FIELD_ID') }}:</th>
                        <td>
                            {{ $row->id }}
                            <input
                                type="hidden"
                                name="fields[id]"
                                id="field-id"
                                value="{{ $row->id }}"
                            />
                        </td>
                    </tr>
                    @if($row->created_by)
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_SUPPORT_FIELD_CREATED') }}:</th>
                            <td>
                                @php $createdDate = Date::of($row->created)->toLocal('Y-m-d H:i:s'); @endphp
                                <time datetime="{{ $row->created }}">
                                    {{ $createdDate }}
                                </time>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_SUPPORT_FIELD_CREATOR') }}:</th>
                            <td>
                                @php $createdByUser = User::getInstance($row->created_by); @endphp
                                {{ $createdByUser->get('name') }}
                            </td>
                        </tr>
                        @php
                            $isModified = $row->modified_by
                                && $row->modified_by != '0000-00-00 00:00:00';
                        @endphp
                        @if($isModified)
                            <tr>
                                <th scope="row">{{ Lang::txt('COM_SUPPORT_FIELD_MODIFIED') }}:</th>
                                <td>
                                    @php $modifiedDate = Date::of($row->modified)->toLocal('Y-m-d H:i:s'); @endphp
                                    <time datetime="{{ $row->modified }}">
                                        {{ $modifiedDate }}
                                    </time>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">{{ Lang::txt('COM_SUPPORT_FIELD_MODIFIER') }}:</th>
                                <td>
                                    @php $modifiedByUser = User::getInstance($row->modified_by); @endphp
                                    {{ $modifiedByUser->get('name') }}
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @else
        <fieldset class="adminform">
            <div class="input-wrap">
                <label for="field-title">
                    {{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}:
                    <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                </label>
                <input
                    type="text"
                    name="fields[title]"
                    id="field-title"
                    value="{{ $row->title }}"
                />
            </div>

            <input
                type="hidden"
                name="fields[alias]"
                id="field-alias"
                value="{{ $row->alias }}"
            />
        </fieldset>
    @endif

    <input type="hidden" name="no_html" value="{{ $no_html }}" />
    <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="savefolder" />

    {!! Html::input('token') !!}
</form>
