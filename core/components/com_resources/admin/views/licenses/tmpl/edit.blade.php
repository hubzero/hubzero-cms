{{--
  Resource License — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Resources\Helpers\Permissions::getActions('license');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_LICENSES') }}: {{ $text }}"
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
               maxlength="100"
               required
               value="{{ $row->title }}" />
      </div>

      <div class="admin-field">
        <label for="field-name" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[name]"
               id="field-name"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->name }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-url" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_URL') }}</label>
        <input type="text"
               name="fields[url]"
               id="field-url"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->url }}" />
      </div>

      <div class="admin-field">
        <label for="field-text" class="label">
          {{ Lang::txt('COM_RESOURCES_FIELD_CONTENT') }}
          <span class="text-error">*</span>
        </label>
        <textarea name="fields[text]"
                  id="field-text"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="15"
                  required>{{ $row->text }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_RESOURCES_FIELD_ID') }}</td>
              <td>{{ $row->id ?: Lang::txt('JNONE') }}</td>
            </tr>
            @if($row->id)
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_ORDERING') }}</td>
                <td>{{ $row->ordering }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[ordering]" value="{{ $row->ordering }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
