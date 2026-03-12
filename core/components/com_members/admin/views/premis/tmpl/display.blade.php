{{--
  com_members — PREMIS import form

  Variables: $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Members\Helpers\Permissions::getActions('component');

  Toolbar::title(
      Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('COM_MEMBERS_PREMIS'),
      'user'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::addNew();
      Toolbar::editList();
      Toolbar::deleteList();
  }
@endphp

@include('com_members::admin.views.registration.tmpl._submenu')

@if ($__view->getError())
  <div class="alert alert-error">
    <p>{!! implode('<br />', $__view->getErrors()) !!}</p>
  </div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PREMIS') }}">
    <div class="admin-field">
      <label for="field-upload" class="label">
        {{ Lang::txt('COM_MEMBERS_PREMIS') }} file
      </label>
      <input type="file"
             name="upload"
             id="field-upload"
             class="file-input file-input-bordered w-full" />
    </div>

    <div class="admin-field">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('Import') }}
      </button>
    </div>
  </x-admin-fieldset>

  <input type="hidden" name="task" value="save" />
</x-admin-edit>
