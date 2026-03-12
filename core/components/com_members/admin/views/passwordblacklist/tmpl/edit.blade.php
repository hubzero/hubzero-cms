{{--
  Password Blacklist — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}: {{ $text }}"
    icon="user"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}">
    <div class="admin-field">
      <label for="field-word" class="label">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST_WORD') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[word]"
             id="field-word"
             class="input input-bordered w-full"
             required
             value="{{ $row->get('word') }}" />
    </div>

    <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  </x-admin-fieldset>

  <x-slot name="sidebar">
    @if($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST_ID') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
