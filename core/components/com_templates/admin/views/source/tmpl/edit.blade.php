{{--
  com_templates — Template source file editor

  Variables: $file (File model with source()/get('name')/get('extension_id'))

  Keeps templates.js for form validation (Hubzero.submitbutton).

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Templates\Helpers\Utilities::getActions();

  Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_EDIT_FILE'), 'thememanager');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('source');

  // templates.js: Hubzero.submitbutton form validation
  $__view->js('templates');

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );

  $filenameLegend = Lang::txt(
      'COM_TEMPLATES_TEMPLATE_FILENAME',
      $file->get('name'),
      $file->template()->element
  );
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

  <x-admin-fieldset legend="{{ $filenameLegend }}">

    <div class="admin-field">
      <label for="field-source" class="label">
        {{ Lang::txt('COM_TEMPLATES_FIELD_SOURCE_LABEL') }}
      </label>
      <textarea name="fields[source]"
                id="field-source"
                class="textarea textarea-bordered w-full font-mono text-sm"
                rows="40"
                cols="80">{{ $file->source() }}</textarea>
    </div>

  </x-admin-fieldset>

  <input type="hidden" name="option"                  value="{{ $option }}" />
  <input type="hidden" name="controller"              value="{{ $controller }}" />
  <input type="hidden" name="task"                    value="" />
  <input type="hidden" name="fields[extension_id]"    value="{{ $file->get('extension_id') }}" />
  <input type="hidden" name="fields[filename]"        value="{{ $file->get('name') }}" />
  {!! Html::input('token') !!}

</form>
