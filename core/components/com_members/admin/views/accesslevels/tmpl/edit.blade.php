{{--
  Access Levels — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_ACCESSLEVELS') }}: {{ $text }}"
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
      <label for="field-title" class="label">
        {{ Lang::txt('COM_MEMBERS_LEVEL_FIELD_TITLE_LABEL') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             required
             value="{{ $row->get('title') }}" />
    </div>
  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_USER_GROUPS_HAVING_ACCESS') }}">
    {!! Html::access('usergroups', 'fields[rules]', $row->get('rules')) !!}
  </x-admin-fieldset>

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="id" value="{{ $row->get('id') }}" />

  <x-slot name="sidebar">
    @if($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_FIELD_ID') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
