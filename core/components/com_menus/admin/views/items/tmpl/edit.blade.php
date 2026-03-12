{{--
  com_menus — Admin menu item edit

  Type-aware form: URL type shows link field as editable; alias type shows tip;
  component type adds home field. Options and module-assignment panels in right column
  via <details> accordion (replacing Html::sliders()).

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $isNew      = ($item->id == 0);
  $checkedOut = !($item->checked_out == 0 || $item->checked_out == User::get('id'));
  $canDo      = \Components\Menus\Helpers\Menus::getActions($item->get('parent_id'));

  Toolbar::title(
      Lang::txt($isNew ? 'COM_MENUS_VIEW_NEW_ITEM_TITLE' : 'COM_MENUS_VIEW_EDIT_ITEM_TITLE'),
      'menu-add'
  );

  if ($isNew && $canDo->get('core.create')) {
      if ($canDo->get('core.edit')) Toolbar::apply('items.apply');
      Toolbar::save('items.save');
  }
  if (!$isNew && !$checkedOut && $canDo->get('core.edit')) {
      Toolbar::apply('items.apply');
      Toolbar::save('items.save');
  }
  if ($canDo->get('core.create')) {
      Toolbar::save2new('items.save2new');
  }
  if (!$isNew && $canDo->get('core.create')) {
      Toolbar::save2copy('items.save2copy');
  }
  if ($isNew) {
      Toolbar::cancel('items.cancel');
  } else {
      Toolbar::cancel('items.cancel', 'JTOOLBAR_CLOSE');
  }
  Toolbar::divider();
  Toolbar::help('item');

@endphp

<x-admin-edit option="{{ $option }}" controller="{{ $controller }}">

  @php /* default slot = left/main column */ @endphp
    <div class="admin-fieldset">
      <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_MENUS_ITEM_DETAILS') }}</h3>
      <div class="admin-fieldset-body space-y-3">

        <div class="form-control">
          {!! $form->getLabel('type') !!}
          {!! $form->getInput('type') !!}
        </div>

        <div class="form-control">
          {!! $form->getLabel('title') !!}
          {!! $form->getInput('title') !!}
        </div>

        @if($item->type == 'url')
          @php $form->setFieldAttribute('link', 'readonly', 'false'); @endphp
          <div class="form-control">
            {!! $form->getLabel('link') !!}
            {!! $form->getInput('link') !!}
          </div>
        @endif

        @if($item->type == 'alias')
          <div class="form-control">
            {!! $form->getLabel('aliastip') !!}
          </div>
        @endif

        <div class="form-control">
          {!! $form->getLabel('alias') !!}
          {!! $form->getInput('alias') !!}
        </div>

        <div class="form-control">
          {!! $form->getLabel('note') !!}
          {!! $form->getInput('note') !!}
        </div>

        @if($item->type !== 'url')
          <div class="form-control">
            {!! $form->getLabel('link') !!}
            {!! $form->getInput('link') !!}
          </div>
        @endif

        <div class="grid grid-cols-2 gap-3">
          <div class="form-control">
            {!! $form->getLabel('access') !!}
            {!! $form->getInput('access') !!}
          </div>
          <div class="form-control">
            {!! $form->getLabel('published') !!}
            {!! $form->getInput('published') !!}
          </div>
        </div>

        <div class="form-control">
          {!! $form->getLabel('menutype') !!}
          {!! $form->getInput('menutype') !!}
        </div>

        <div class="form-control">
          {!! $form->getLabel('parent_id') !!}
          {!! $form->getInput('parent_id') !!}
        </div>

        <div class="form-control">
          {!! $form->getLabel('menuordering') !!}
          {!! $form->getInput('menuordering') !!}
        </div>

        <div class="form-control">
          {!! $form->getLabel('browserNav') !!}
          {!! $form->getInput('browserNav') !!}
        </div>

        @if($item->type == 'component')
          <div class="form-control">
            {!! $form->getLabel('home') !!}
            {!! $form->getInput('home') !!}
          </div>
        @endif

        <div class="grid grid-cols-2 gap-3">
          <div class="form-control">
            {!! $form->getLabel('language') !!}
            {!! $form->getInput('language') !!}
          </div>
          <div class="form-control">
            {!! $form->getLabel('template_style_id') !!}
            {!! $form->getInput('template_style_id') !!}
          </div>
        </div>

        <div class="form-control">
          {!! $form->getLabel('id') !!}
          {!! $form->getInput('id') !!}
        </div>

      </div>
    </div>
  @slot('sidebar')

    {{-- Options panels (request + params fieldsets) --}}
    @include('com_menus::admin.views.items.tmpl.edit_options')

    {{-- Module assignment panel --}}
    @if(!empty($modules))
      <details class="admin-fieldset" name="menu-options">
        <summary class="admin-fieldset-heading cursor-pointer select-none">
          {{ Lang::txt('COM_MENUS_ITEM_MODULE_ASSIGNMENT') }}
        </summary>
        <div class="admin-fieldset-body">
          @include('com_menus::admin.views.items.tmpl.edit_modules')
        </div>
      </details>
    @endif

  @endslot

  <input type="hidden" name="task" value="" />
  {!! $form->getInput('component_id') !!}
  <input type="hidden" id="fieldtype" name="fieldtype" value="" />

</x-admin-edit>
