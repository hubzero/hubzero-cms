{{--
  Mass Mail — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS_MASS_MAIL'), 'massmail');
  Toolbar::custom('send', 'send.png', 'send_f2.png', 'COM_MEMBERS_TOOLBAR_MAIL_SEND_MAIL', false);
  Toolbar::cancel('cancelmail');
  Toolbar::divider();
  Toolbar::preferences('com_members');
@endphp

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      name="adminForm"
      method="post"
      id="item-form">

  <div class="alert alert-warning mb-4">
    {!! Lang::txt('COM_MEMBERS_MAIL_DO_NOT_USE_FOR_COMMERCIAL_USE') !!}
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_MAIL_DETAILS') }}">
      <div class="admin-field">
        {!! $form->getLabel('recurse') !!}
        {!! $form->getInput('recurse') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('mode') !!}
        {!! $form->getInput('mode') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('disabled') !!}
        {!! $form->getInput('disabled') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('group') !!}
        {!! $form->getInput('group') !!}
      </div>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_MAIL_MESSAGE') }}">
      <div class="admin-field">
        {!! $form->getLabel('subject') !!}
        {!! $form->getInput('subject') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('message') !!}
        {!! $form->getInput('message') !!}
      </div>
    </x-admin-fieldset>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="send" />
  {!! Html::input('token') !!}
</form>
