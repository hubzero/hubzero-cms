{{--
  Windows app — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS') . ': ' . $text, 'tools');
  Toolbar::apply();
  Toolbar::save();
  Toolbar::spacer();
  Toolbar::cancel();
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="admin-field">
      <label for="field-alias" class="label text-base-content">
        {{ Lang::txt('COM_TOOLS_FIELD_NAME') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[alias]"
             id="field-alias"
             class="input input-bordered w-full"
             required
             value="{{ $row->get('alias', '') }}" />
    </div>

    <div class="admin-field">
      <label for="field-title" class="label text-base-content">
        {{ Lang::txt('COM_TOOLS_FIELD_TITLE') }}
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
      <label for="field-path" class="label text-base-content">
        {{ Lang::txt('COM_TOOLS_FIELD_UUID') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[path]"
             id="field-path"
             class="input input-bordered w-full font-mono"
             required
             value="{{ $row->get('path', '') }}" />
    </div>

    <div class="admin-field">
      <label for="field-introtext" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DESCRIPTION') }}</label>
      <textarea name="fields[introtext]"
                id="field-introtext"
                rows="5"
                class="textarea textarea-bordered w-full">{{ $row->get('introtext', '') }}</textarea>
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_ID') }}</td>
          <td>{{ $row->get('id', 0) ?: '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_STATUS') }}</td>
          <td>
            @if($row->get('status'))
              <span class="badge badge-sm badge-info">{{ $row->get('status') }}</span>
            @else
              <span class="text-muted-foreground">—</span>
            @endif
          </td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[id]"   value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[type]" value="{{ $row->get('type') }}" />
</x-admin-edit>
