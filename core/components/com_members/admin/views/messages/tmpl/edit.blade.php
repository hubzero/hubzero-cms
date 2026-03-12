{{--
  Messaging Actions — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Permissions::getActions('component');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_MENU_MESSAGING') }}: {{ $text }}"
    icon="user"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
    <div class="admin-field">
      <label for="field-component" class="label">
        {{ Lang::txt('COM_MEMBERS_FIELD_COMPONENT') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[component]"
             id="field-component"
             class="input input-bordered w-full"
             value="{{ $row->component }}" />
    </div>

    <div class="admin-field">
      <label for="field-action" class="label">
        {{ Lang::txt('COM_MEMBERS_FIELD_ACTION') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[action]"
             id="field-action"
             class="input input-bordered w-full"
             value="{{ $row->action }}" />
    </div>

    <div class="admin-field">
      <label for="field-title" class="label">
        {{ Lang::txt('COM_MEMBERS_FIELD_DESCRIPTION') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             value="{{ $row->title }}" />
    </div>

    <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  </x-admin-fieldset>

  <x-slot name="sidebar">
    @if($row->id)
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_FIELD_ID') }}</td>
            <td>{{ $row->id }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
