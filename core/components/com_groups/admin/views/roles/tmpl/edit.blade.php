{{--
  Groups Roles — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Groups\Helpers\Permissions::getActions();
  $tmpl  = Request::getCmd('tmpl', '');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  if ($tmpl != 'component') {
      Toolbar::title(
          Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_ROLES') . ': ' . $text
      );
      if ($canDo->get('core.edit')) {
          Toolbar::apply();
          Toolbar::save();
          Toolbar::spacer();
      }
      Toolbar::cancel();
      Toolbar::spacer();
      Toolbar::help('edit');
  }


  $__view->js();

  $permissions = $model->permissions;
@endphp

@php
  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
  $invalidMsg = Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED');
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform form-validate"
      data-invalid-msg="{{ $invalidMsg }}">

  @if($tmpl == 'component')
    <fieldset>
      <div class="configuration">
        <div class="fltrt configuration-options">
          <button type="button" id="btn-save">{{ Lang::txt('JTOOLBAR_SAVE') }}</button>
          <button type="button" id="btn-cancel">{{ Lang::txt('COM_GROUPS_MEMBER_CANCEL') }}</button>
        </div>
        {{ Lang::txt('COM_GROUPS_ROLES') }}: {{ $text }}
      </div>
    </fieldset>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">
    <div class="min-w-0">
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <div class="admin-field">
          <label for="field-name" class="label text-base-content">
            {{ Lang::txt('COM_GROUPS_NAME') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[name]"
                 id="field-name"
                 size="30"
                 maxlength="250"
                 class="input input-bordered w-full required"
                 required
                 value="{{ $model->get('name') }}" />
        </div>

        <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PERMISSIONS') }}">
          <div class="admin-field">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="fields[permissions][group.invite]"
                     id="field-permissions-invite"
                     value="1"
                     class="checkbox checkbox-sm"
                     @checked($permissions->get('group.invite') == 1) />
              <span>{{ Lang::txt('COM_GROUPS_PERMISSION_INVITE') }}</span>
            </label>
          </div>
          <div class="admin-field">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="fields[permissions][group.edit]"
                     id="field-permissions-edit"
                     value="1"
                     class="checkbox checkbox-sm"
                     @checked($permissions->get('group.edit') == 1) />
              <span>{{ Lang::txt('COM_GROUPS_PERMISSION_EDIT') }}</span>
            </label>
          </div>
          <div class="admin-field">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="fields[permissions][group.pages]"
                     id="field-permissions-pages"
                     value="1"
                     class="checkbox checkbox-sm"
                     @checked($permissions->get('group.pages') == 1) />
              <span>{{ Lang::txt('COM_GROUPS_PERMISSION_PAGES') }}</span>
            </label>
          </div>
        </x-admin-fieldset>
      </x-admin-fieldset>
    </div>

    <div>
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_GROUPS_FIELD_ID') }}:</td>
              <td>
                {{ $model->get('id') }}
                <input type="hidden" name="fields[id]" value="{{ $model->get('id') }}" />
                <input type="hidden" name="fields[gidNumber]" value="{{ $group->get('gidNumber') }}" />
              </td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>
    </div>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
  <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
  {!! Html::input('token') !!}
</form>
