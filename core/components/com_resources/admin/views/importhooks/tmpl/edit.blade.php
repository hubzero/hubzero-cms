{{--
  Resource Import Hook — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $title = $hook->get('id')
      ? Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_EDIT')
      : Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_ADD');
@endphp

<x-admin-toolbar
    title="{{ $title }}"
    icon="import"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELDSET_DETAILS') }}">

      <div class="admin-field">
        <label for="field-type" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_TYPE') }}
        </label>
        <select name="hook[type]" id="field-type" class="select select-bordered w-full">
          <option value="postparse" @selected($hook->get('type') == 'postparse' || !$hook->get('type'))>
            {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_TYPE_POSTPARSE') }}
          </option>
          <option value="postmap" @selected($hook->get('type') == 'postmap')>
            {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_TYPE_POSTMAP') }}
          </option>
          <option value="postconvert" @selected($hook->get('type') == 'postconvert')>
            {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_TYPE_POSTCONVERT') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-name" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_NAME') }}
        </label>
        <input type="text"
               name="hook[name]"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ $hook->get('name') }}" />
      </div>

      <div class="admin-field">
        <label for="field-notes" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_NOTES') }}
        </label>
        <textarea name="hook[notes]"
                  id="field-notes"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $hook->get('notes') }}</textarea>
      </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELDSET_FILE') }}">

      <div class="admin-field">
        <label for="field-file" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_SCRIPT') }}
        </label>
        @if($hook->get('file'))
          @php
            $rawUrl = Route::url(
                'index.php?option=com_resources&controller=importhooks&task=raw&id=' . $hook->get('id'), false
            );
          @endphp
          <p class="text-sm mb-2">
            {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_SCRIPT_CURRENT', $hook->get('file')) }}
            &mdash;
            <a rel="noopener" target="_blank" href="{{ $rawUrl }}" class="link link-primary">
              {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_SCRIPT_VIEWRAW') }}
            </a>
          </p>
        @endif
        <input type="file"
               name="file"
               id="field-file"
               class="file-input file-input-bordered w-full" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    @if($hook->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_ID') }}</td>
                <td>{{ $hook->get('id') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_CREATEDBY') }}</td>
                <td>
                  @php $createdBy = User::getInstance($hook->get('created_by')); @endphp
                  {{ $createdBy ? e($createdBy->get('name')) : '' }}
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_EDIT_FIELD_CREATEDON') }}</td>
                <td>{{ Date::of($hook->get('created_at'))->toLocal('m/d/Y @ g:i a') }}</td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>
    @endif
  @endslot

  <input type="hidden" name="hook[id]" value="{{ $hook->get('id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
