{{--
  com_menus — Admin menu edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $isNew = $item->isNew();
  $canDo = \Components\Menus\Helpers\Menus::getActions();

  Toolbar::title(
      Lang::txt($isNew ? 'COM_MENUS_VIEW_NEW_MENU_TITLE' : 'COM_MENUS_VIEW_EDIT_MENU_TITLE'),
      'menu'
  );

  if ($isNew && $canDo->get('core.create')) {
      if ($canDo->get('core.edit')) {
          Toolbar::apply();
      }
      Toolbar::save();
  }
  if (!$isNew && $canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  if ($canDo->get('core.create')) {
      Toolbar::save2new();
  }
  if ($isNew) {
      Toolbar::cancel();
  } else {
      Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
  }
  Toolbar::divider();
  Toolbar::help('menu');

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&cid=' . (int) $item->get('id'),
      false, false
  );
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>

    <x-admin-fieldset legend="{{ Lang::txt('COM_MENUS_MENU_DETAILS') }}">

      <div class="admin-field">
        {!! $form->getLabel('title') !!}
        {!! $form->getInput('title') !!}
      </div>

      <div class="admin-field">
        {!! $form->getLabel('menutype') !!}
        {!! $form->getInput('menutype') !!}
      </div>

      <div class="admin-field">
        {!! $form->getLabel('description') !!}
        {!! $form->getInput('description') !!}
      </div>

    </x-admin-fieldset>

    <input type="hidden" name="cid" value="{{ (int) $item->get('id') }}" />

    @slot('sidebar')
      @if(!$isNew)
        <div class="admin-fieldset">
          <h3 class="admin-fieldset-heading">{{ Lang::txt('JDETAILS') }}</h3>
          <div class="admin-fieldset-body">
            <table class="w-full text-sm">
              <tr>
                <th class="py-1 pr-3 text-left text-muted-foreground font-normal whitespace-nowrap">{{ Lang::txt('JGLOBAL_FIELD_ID_LABEL') }}</th>
                <td class="py-1">{{ (int) $item->get('id') }}</td>
              </tr>
            </table>
          </div>
        </div>
      @endif
    @endslot

</x-admin-edit>
