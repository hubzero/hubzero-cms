{{--
  Groups Roles — Assign role to members

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

  $canDo = \Components\Groups\Helpers\Permissions::getActions('group');
  $tmpl  = Request::getCmd('tmpl', '');
  $text  = ($task == 'edit') ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW');

  if ($tmpl != 'component') {
      Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
      if ($canDo->get('core.edit')) {
          Toolbar::save();
      }
      Toolbar::cancel();
  }


  $__view->js('roles.blade.js');
@endphp

@if($__view->getError())
  <p class="error">{!! implode('<br />', $__view->getErrors()) !!}</p>
@endif

@php
  $formUrl     = Route::url('index.php?option=' . $option, false);
  $formId      = ($tmpl == 'component') ? 'component' : 'item';
  $invalidMsg  = Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION');
  $redirectUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=membership&gid=' . $group->get('cn'), false
  );
@endphp

<form action="{{ $formUrl }}"
      method="post"
      name="adminForm"
      id="{{ $formId }}-form"
      data-invalid-msg="{{ $invalidMsg }}"
      class="editform form-validate"
      data-redirect="{{ $redirectUrl }}">

  @if($tmpl == 'component')
    <fieldset>
      <div class="configuration">
        <div class="fltrt configuration-options">
          <button type="button" id="btn-save" class="btn btn-primary">{{ Lang::txt('COM_GROUPS_MEMBER_SAVE') }}</button>
          <button type="button" id="btn-cancel" class="btn">{{ Lang::txt('COM_GROUPS_MEMBER_CANCEL') }}</button>
        </div>
        {{ Lang::txt('COM_GROUPS_ROLE_ASSIGN') }}
      </div>
    </fieldset>
  @endif

  <div>
    <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_DETAILS') }}">
      <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
      <input type="hidden" name="task" value="delegate" />

      @foreach($ids as $i => $id)
        <input type="hidden" name="id[{{ $i }}]" value="{{ $id }}" />
      @endforeach

      <div class="admin-field">
        <label for="field-roleid" class="label text-base-content">
          {{ Lang::txt('COM_GROUPS_ROLE_CHOOSE') }}
          <span class="text-error">*</span>
        </label>
        <select name="roleid"
                id="field-roleid"
                class="select select-bordered w-full required"
                required>
          <option value="0">{{ Lang::txt('COM_GROUPS_ROLE_SELECT') }}</option>
          @foreach($rows as $row)
            <option value="{{ $row->get('id') }}">{{ $row->get('name') }}</option>
          @endforeach
        </select>
      </div>
    </x-admin-fieldset>
  </div>

  {!! Html::input('token') !!}
</form>
