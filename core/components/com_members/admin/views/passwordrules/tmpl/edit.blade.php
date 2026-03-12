{{--
  Password Rules — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}: {{ $text }}"
    icon="user"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}">
    <div class="admin-field">
      <label for="field-rule" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_RULE') }}</label>
      {!! $rules_list !!}
    </div>

    <div class="admin-field">
      <label for="field-description" class="label">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_DESCRIPTION') }}
      </label>
      <input type="text"
             name="fields[description]"
             id="field-description"
             class="input input-bordered w-full"
             value="{{ $row->get('description') }}" />
    </div>

    <div class="admin-field">
      <label for="field-failuremsg" class="label">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_FAILURE_MESSAGE') }}
      </label>
      <input type="text"
             name="fields[failuremsg]"
             id="field-failuremsg"
             class="input input-bordered w-full"
             value="{{ $row->get('failuremsg') }}" />
    </div>

    <div class="admin-field">
      <label for="field-value" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_VALUE') }}</label>
      <input type="text"
             name="fields[value]"
             id="field-value"
             class="input input-bordered w-full"
             value="{{ $row->get('value') }}" />
    </div>

    <div class="admin-field">
      <label for="field-group" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_GROUP') }}</label>
      <input type="text"
             name="fields[grp]"
             id="field-group"
             class="input input-bordered w-full"
             value="{{ $row->get('grp') }}" />
    </div>

    <div class="admin-field">
      <label for="field-class" class="label">{{ Lang::txt('COM_MEMBERS_PASSWORD_RULES_CLASS') }}</label>
      <input type="text"
             name="fields[class]"
             id="field-class"
             class="input input-bordered w-full"
             value="{{ $row->get('class') }}" />
    </div>

    <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  </x-admin-fieldset>

  <x-slot name="sidebar">
    @if($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_PASSWORD_ID') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
