{{--
  Tool Host Type — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOST_TYPES') . ': ' . $text, 'tools');
  Toolbar::save();
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
      <label for="field-name" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_NAME') }}</label>
      <input type="text"
             name="fields[name]"
             id="field-name"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->name ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-value" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_VALUE') }}</label>
      <input type="text"
             name="fields[value]"
             id="field-value"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->value ?? '' }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_TOOLS_FIELD_VALUE_HINT') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-description" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DESCRIPTION') }}</label>
      <input type="text"
             name="fields[description]"
             id="field-description"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->description ?? '' }}" />
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_COL_BIT') }}</td>
          <td class="font-mono">{{ $bit !== '' ? $bit : '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_COL_REFERENCES') }}</td>
          <td>{{ $refs ?? 0 }}</td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[status]" value="{{ $status ?? 'new' }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->name ?? '' }}" />
</x-admin-edit>
