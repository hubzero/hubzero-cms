{{--
  Resource Role — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Resources\Helpers\Permissions::getActions('role');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $assignedTypes = [];
  if ($row->id) {
      foreach ($row->types()->rows() as $t) {
          $assignedTypes[] = $t->get('id');
      }
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_ROLES') }}: {{ $text }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_RESOURCES_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               required
               value="{{ $row->title }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ $row->alias }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_FIELDSET_TYPES') }}">
      @if($types)
        <div class="space-y-2">
          @foreach($types as $type)
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="types[]"
                     value="{{ $type->id }}"
                     class="checkbox checkbox-sm"
                     @checked(in_array($type->id, $assignedTypes)) />
              <span>{{ $type->type }}</span>
            </label>
          @endforeach
        </div>
      @endif
  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_RESOURCES_FIELD_ID') }}</td>
              <td>{{ $row->id ?: Lang::txt('JNONE') }}</td>
            </tr>
            @if($row->created_by)
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_CREATOR') }}</td>
                <td>{{ User::getInstance($row->created_by)->get('name') }}</td>
              </tr>
            @endif
            @if($row->created)
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_CREATED') }}</td>
                <td>{{ $row->created }}</td>
              </tr>
            @endif
            @if($row->modified_by)
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_MODIFIER') }}</td>
                <td>{{ User::getInstance($row->modified_by)->get('name') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_MODIFIED') }}</td>
                <td>{{ $row->modified }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->created_by }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->created }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
