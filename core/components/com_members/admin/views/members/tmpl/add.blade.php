{{--
  Members — Quick add member

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Members\Helpers\Permissions::getActions('component');

  Toolbar::title(
      Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('JACTION_CREATE'),
      'user'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::save('new');
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PROFILE') }}">

      <div class="admin-field">
        <label for="username" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_USERNAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="profile[username]"
               id="username"
               class="input input-bordered w-full"
               required />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_MEMBERS_FIELD_USERNAME_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="email" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_EMAIL') }}
          <span class="text-error">*</span>
        </label>
        <input type="email"
               name="profile[email]"
               id="email"
               class="input input-bordered w-full"
               required />
      </div>

      <div class="admin-field">
        <label for="password" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_PASSWORD') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="profile[password]"
               id="password"
               class="input input-bordered w-full"
               required />
      </div>

      <div class="admin-field">
        <label for="givenName" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="profile[givenName]"
               id="givenName"
               class="input input-bordered w-full"
               required />
      </div>

      <div class="admin-field">
        <label for="middleName" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_MIDDLE_NAME') }}
        </label>
        <input type="text"
               name="profile[middleName]"
               id="middleName"
               class="input input-bordered w-full" />
      </div>

      <div class="admin-field">
        <label for="surname" class="label">
          {{ Lang::txt('COM_MEMBERS_FIELD_LAST_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="profile[surname]"
               id="surname"
               class="input input-bordered w-full"
               required />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <div class="alert alert-warning">
      <p>{{ Lang::txt('COM_MEMBERS_FIELD_USERNAME_NOTE') }}</p>
    </div>
  @endslot

  <input type="hidden" name="task" value="edit" />
</x-admin-edit>
