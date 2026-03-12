{{--
  Support — Status edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Support\Helpers\Permissions::getActions('status');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': '
      . Lang::txt('COM_SUPPORT_STATUS') . ': ' . $text,
      'support'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('status');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column: Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_FIELD_ALIAS') }}
        </label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ $row->get('alias', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT') }}
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-open" class="label text-base-content">
            {{ Lang::txt('COM_SUPPORT_FIELD_FOR') }}
            <span class="text-error">*</span>
          </label>
          <select name="fields[open]"
                  id="field-open"
                  class="select select-bordered w-full"
                  required>
            <option value="1" @selected($row->get('open') == 1)>
              {{ Lang::txt('COM_SUPPORT_FIELD_FOR_OPEN') }}
            </option>
            <option value="0" @selected($row->get('open') == 0)>
              {{ Lang::txt('COM_SUPPORT_FIELD_FOR_CLOSED') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-color" class="label text-base-content">
            {{ Lang::txt('Color') }}
          </label>
          <input type="text"
                 name="fields[color]"
                 id="field-color"
                 class="input input-bordered w-full"
                 value="{{ $row->get('color', '') }}" />
        </div>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_SUPPORT_FIELD_ID') }}</td>
              <td>
                {{ $row->get('id', 0) ?: Lang::txt('JNEW') }}
                <input type="hidden" name="fields[id]"
                       value="{{ $row->get('id', 0) }}" />
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

</x-admin-edit>
