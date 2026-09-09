{{--
  Tool Version — Admin edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . $text, 'tools');
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('version');

  $__view->js();

  $zonesUrl = $row->get('id')
    ? Route::url('index.php?option=' . $option . '&controller=versions&task=displayZones&tmpl=component&version=' . $row->get('id'), false)
    : null;
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{{ implode('<br>', $__view->getErrors()) }}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Tab nav --}}
  <div class="tabs tabs-border mb-4">
    <button type="button" class="tab tab-active" data-tab="details">{{ Lang::txt('JDETAILS') }}</button>
    <button type="button" class="tab" data-tab="zones">{{ Lang::txt('COM_TOOLS_FIELDSET_ZONES') }}</button>
  </div>

  {{-- Details tab --}}
  <div data-tab-panel="details">
    <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELD_VERSION_DETAILS') }}">

      <div class="admin-field">
        <label for="field-command" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_COMMAND') }}</label>
        <input type="text"
               name="fields[vnc_command]"
               id="field-command"
               class="input input-bordered w-full"
               value="{{ $row->vnc_command }}" />
      </div>

      <div class="admin-field">
        <label for="field-timeout" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TIMEOUT') }}</label>
        <input type="text"
               name="fields[vnc_timeout]"
               id="field-timeout"
               class="input input-bordered w-full"
               value="{{ $row->vnc_timeout }}" />
      </div>

      <div class="admin-field">
        <label for="field-hostreq" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_HOSTREQ') }}</label>
        <input type="text"
               name="fields[hostreq]"
               id="field-hostreq"
               class="input input-bordered w-full"
               value="{{ implode(', ', $row->hostreq ?? []) }}" />
        <p class="text-xs text-muted-foreground mt-1">Comma-separated list of required host types.</p>
      </div>

      <div class="admin-field">
        <label for="field-mw" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_MIDDLEWARE') }}</label>
        <input type="text"
               name="fields[mw]"
               id="field-mw"
               class="input input-bordered w-full"
               value="{{ $row->mw }}" />
      </div>

      <div class="admin-field">
        <label for="field-params" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_PARAMS') }}</label>
        <textarea name="fields[params]"
                  id="field-params"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="8">{{ $row->params }}</textarea>
      </div>

    </x-admin-fieldset>

    @if($doi)
      <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELD_VERSION_DOI') }}">
        @if($doiError = $doi->getError())
          <div class="alert alert-warning">{{ $doiError }}</div>
        @else
          <div class="grid grid-cols-2 gap-4">
            <div class="admin-field">
              <label for="field-doi_shoulder" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DOI_SHOULDER') }}</label>
              <input type="text" name="doi[doi_shoulder]" id="field-doi_shoulder"
                     class="input input-bordered w-full"
                     value="{{ $doi->doi_shoulder }}" />
            </div>
            <div class="admin-field">
              <label for="field-doi" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DOI_DOI') }}</label>
              <input type="text" name="doi[doi]" id="field-doi"
                     class="input input-bordered w-full"
                     value="{{ $doi->doi }}" />
            </div>
          </div>
          <div class="admin-field mt-2">
            <label for="field-doi_label" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DOI_LABEL') }}</label>
            <input type="text" name="doi[doi_label]" id="field-doi_label"
                   class="input input-bordered w-full"
                   value="{{ $doi->doi_label }}" />
          </div>
          <input type="hidden" name="doi[id]"             value="{{ $doi->id }}" />
          <input type="hidden" name="doi[rid]"            value="{{ $doi->rid }}" />
          <input type="hidden" name="doi[local_revision]" value="{{ $row->revision }}" />
          <input type="hidden" name="doi[versionid]"      value="{{ $row->id }}" />
          <input type="hidden" name="doi[alias]"          value="{{ $row->toolname }}" />
        @endif
      </x-admin-fieldset>
    @endif
  </div>

  {{-- Zones tab --}}
  <div data-tab-panel="zones" class="hidden">
    <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELDSET_ZONES') }}">
      @if($zonesUrl)
        <iframe width="100%"
                height="400"
                name="zoneslist"
                id="zoneslist"
                style="border:0"
                src="{{ $zonesUrl }}"></iframe>
      @else
        <p class="text-muted-foreground">{{ Lang::txt('COM_TOOLS_ZONES_ADDED_AFTER_SAVE') }}</p>
      @endif
    </x-admin-fieldset>
  </div>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_TITLE') }}</td>
          <td>{{ $parent->title }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_TOOLNAME') }}</td>
          <td>{{ $parent->toolname }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_VERSION') }}</td>
          <td>{{ $row->id }}</td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[id]"      value="{{ $parent->id }}" />
  <input type="hidden" name="fields[version]" value="{{ $row->id }}" />
  <input type="hidden" name="task"            value="save" />
</x-admin-edit>
