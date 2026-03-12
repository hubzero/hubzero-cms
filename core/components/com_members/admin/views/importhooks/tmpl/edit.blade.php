{{--
  Import Hook — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $title = $hook->get('id')
      ? Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_EDIT')
      : Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_ADD');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ $title }}"
    icon="import"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELDSET_DETAILS') }}">
    <div class="admin-field">
      <label for="field-event" class="label">
        {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE') }}
      </label>
      <select name="hook[event]"
              id="field-event"
              class="select select-bordered w-full">
        <option value="postparse"
                @selected($hook->get('event') == 'postparse')>
          {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTPARSE') }}
        </option>
        <option value="postmap"
                @selected($hook->get('event') == 'postmap')>
          {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTMAP') }}
        </option>
        <option value="postconvert"
                @selected($hook->get('event') == 'postconvert')>
          {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTCONVERT') }}
        </option>
      </select>
    </div>

    <div class="admin-field">
      <label for="field-name" class="label">
        {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_NAME') }}
      </label>
      <input type="text"
             name="hook[name]"
             id="field-name"
             class="input input-bordered w-full"
             value="{{ $hook->get('name') }}" />
    </div>

    <div class="admin-field">
      <label for="field-notes" class="label">
        {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_NOTES') }}
      </label>
      <textarea name="hook[notes]"
                id="field-notes"
                class="textarea textarea-bordered w-full"
                rows="5">{{ $hook->get('notes') }}</textarea>
    </div>
  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELDSET_FILE') }}">
    <div class="admin-field">
      <label for="field-file" class="label">
        {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT') }}
      </label>
      @if($hook->get('file'))
        <p>
          {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT_CURRENT', $hook->get('file')) }}
          &mdash;
          <a rel="noopener"
             target="_blank"
             href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=raw&id=' . $hook->get('id'), false) !!}">
            {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT_VIEWRAW') }}
          </a>
        </p>
      @endif
      <input type="file" name="file" id="field-file" />
    </div>
  </x-admin-fieldset>

  <input type="hidden" name="hook[id]" value="{{ $hook->get('id') }}" />
  <input type="hidden" name="hook[type]" value="{{ $hook->get('type') }}" />

  <x-slot name="sidebar">
    @if($hook->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_ID') }}</td>
            <td>{{ $hook->get('id') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_CREATEDBY') }}</td>
            <td>
              @php
                $createdBy = User::getInstance($hook->get('created_by'));
              @endphp
              @if($createdBy)
                {{ $createdBy->get('name') }}
              @endif
            </td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_CREATEDON') }}</td>
            <td>
              <time datetime="{{ $hook->get('created_at') }}">
                {{ Date::of($hook->get('created_at'))->toLocal('m/d/Y @ g:i a') }}
              </time>
            </td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
