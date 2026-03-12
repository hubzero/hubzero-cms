{{--
  Quota Import — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS_IMPORT'), 'user');
@endphp

@include('com_members::admin.views.quotas.tmpl._submenu')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  <div class="lg:col-span-8">
    <form action="{!! Route::url('index.php?option=' . $option, false) !!}"
          method="post"
          name="adminForm"
          id="adminForm">
      <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_QUOTA_IMPORT_LEGEND') }}">
        <div class="admin-field">
          <label for="conf_text" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT') }}</label>
          <div class="alert alert-info text-sm mb-2">
            {{ Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT_NOTE') }}
          </div>
          <textarea name="conf_text"
                    id="conf_text"
                    class="textarea textarea-bordered w-full"
                    rows="10"></textarea>
        </div>

        <div class="admin-field">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   name="overwrite_existing"
                   value="1"
                   class="checkbox checkbox-sm" />
            <span>{{ Lang::txt('COM_MEMBERS_QUOTA_OVERWRITE_EXISTING') }}</span>
          </label>
        </div>

        <button type="submit" class="btn btn-primary mt-2">
          {{ Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT') }}
        </button>
      </x-admin-fieldset>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="task" value="processImport" />
      {!! Html::input('token') !!}
    </form>
  </div>

  <div class="lg:col-span-4">
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_QUOTA_IMPORT_MISSING_USERS') }}">
      <p class="text-sm text-muted-foreground mb-3">
        {{ Lang::txt('COM_MEMBERS_QUOTA_MISSING_USERS_IMPORT_DESCRIPTION') }}
      </p>
      <form action="{!! Route::url('index.php?option=' . $option, false) !!}" method="post">
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="importMissing" />
        <button type="submit" class="btn btn-sm btn-secondary">
          {{ Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT') }}
        </button>
      </form>
    </x-admin-fieldset>
  </div>
</div>
