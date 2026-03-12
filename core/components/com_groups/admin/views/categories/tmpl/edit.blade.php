{{--
  Groups Categories — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Groups\Helpers\Permissions::getActions('group');

  Toolbar::title(
      $group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
      'groups'
  );

  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();


  $__view->js();
@endphp

@include('com_groups::admin.views.pages.tmpl.menu')

@php
  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&gid=' . $group->cn, false
  );
  $invalidMsg = Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED');
@endphp

<form action="{{ $formAction }}"
      name="adminForm"
      id="item-form"
      method="post"
      class="editform form-validate"
      data-invalid-msg="{{ $invalidMsg }}">

  <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_CATEGORIES_CATEGORY') }}">
    <div class="admin-field">
      <label for="field-title" class="label text-base-content">
        {{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="category[title]"
             id="field-title"
             class="input input-bordered w-full required"
             required
             value="{{ $category->get('title') }}" />
    </div>

    <div class="admin-field"
         data-hint="{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_HINT') }}">
      <label for="field-color" class="label text-base-content">
        {{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR') }}
      </label>
      <input type="text"
             maxlength="6"
             name="category[color]"
             id="field-color"
             class="input input-bordered w-full"
             value="{{ $category->get('color') }}"
             placeholder="{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_PLACEHOLDER') }}" />
    </div>
  </x-admin-fieldset>

  <input type="hidden" name="category[id]" value="{{ $category->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="gid" value="{{ $group->cn }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
