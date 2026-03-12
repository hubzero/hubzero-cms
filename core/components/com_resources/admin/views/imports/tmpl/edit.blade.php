{{--
  Resource Import — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css('import')->js('import');

  $title = $import->get('id')
      ? Lang::txt('COM_RESOURCES_IMPORT_TITLE_EDIT')
      : Lang::txt('COM_RESOURCES_IMPORT_TITLE_ADD');

  // Parse hooks JSON
  $hooksData = json_decode($import->get('hooks', ''));
  if (!is_object($hooksData)) {
      $hooksData = new stdClass();
  }
  $hooksData->postparse   = $hooksData->postparse ?? [];
  $hooksData->postmap     = $hooksData->postmap ?? [];
  $hooksData->postconvert = $hooksData->postconvert ?? [];
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
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DETAILS') }}">

      <div class="admin-field">
        <label for="field-name" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_NAME') }}
        </label>
        <input type="text"
               name="import[name]"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ $import->get('name') }}" />
      </div>

      <div class="admin-field">
        <label for="field-notes" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_NOTES') }}
        </label>
        <textarea name="import[notes]"
                  id="field-notes"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $import->get('notes') }}</textarea>
      </div>

  </x-admin-fieldset>

  {{-- Data --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DATA') }}">

      <div class="admin-field">
        <label for="field-importfile" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE') }}
        </label>
        <select name="import[file]" id="field-importfile" class="select select-bordered w-full">
          <option value="">
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL') }}
          </option>
          @if(isset($files))
            @foreach($files as $file)
              @php $file = ltrim($file, DIRECTORY_SEPARATOR); @endphp
              <option value="{{ $file }}" @selected($import->get('file') == $file)>
                {{ $file }}
              </option>
            @endforeach
          @endif
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $import->fileSpacePath()) }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-importmode" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE') }}
        </label>
        <select name="import[mode]" id="field-importmode" class="select select-bordered w-full" disabled>
          <option value="UPDATE">
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE') }}
          </option>
          <option value="PATCH"
                  @selected($import->get('mode') == Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH'))>
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH') }}
          </option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_HINT') }}
        </p>
      </div>

  </x-admin-fieldset>

  {{-- Hooks --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_HOOKS') }}">

      @foreach(['postparse', 'postmap', 'postconvert'] as $hookType)
        @php
          $labelKey = 'COM_RESOURCES_IMPORT_EDIT_FIELD_' . strtoupper($hookType) . 'HOOK';
          $selectedIds = $hooksData->$hookType ?? [];
        @endphp
        <div class="admin-field">
          <label for="field-hook{{ $hookType }}" class="label">
            {{ Lang::txt($labelKey) }}
          </label>
          <select name="hooks[{{ $hookType }}][]"
                  id="field-hook{{ $hookType }}"
                  class="select select-bordered w-full"
                  multiple
                  size="4">
            @foreach($selectedIds as $hookId)
              @php $importHook = $hooks->seek($hookId); @endphp
              @if(!empty($importHook))
                <option selected value="{{ $importHook->get('id') }}">
                  {{ $importHook->get('name') }}
                </option>
              @endif
            @endforeach
            @foreach($hooks as $hook)
              @if($hook->get('type') != $hookType || in_array($hook->get('id'), $selectedIds))
                @continue
              @endif
              <option value="{{ $hook->get('id') }}">
                {{ $hook->get('name') }}
              </option>
            @endforeach
          </select>
          <div class="mt-1">
            <a class="hook-up btn btn-xs btn-ghost" href="#">
              {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP') }}
            </a>
            <a class="hook-down btn btn-xs btn-ghost" href="#">
              {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}
            </a>
          </div>
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT') }}
          </p>
        </div>
      @endforeach

  </x-admin-fieldset>

  {{-- Params --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_PARAMS') }}">

      <div class="admin-field">
        <label for="param-status" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS') }}
        </label>
        @php $paramStatus = $params->get('status', 1); @endphp
        <select name="params[status]" id="param-status" class="select select-bordered w-full">
          <option value="2" @selected($paramStatus == 2)>{{ Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') }}</option>
          <option value="5" @selected($paramStatus == 5)>{{ Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') }}</option>
          <option value="3" @selected($paramStatus == 3)>{{ Lang::txt('COM_RESOURCES_PENDING') }}</option>
          <option value="0" @selected($paramStatus == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
          <option value="1" @selected($paramStatus == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
          <option value="4" @selected($paramStatus == 4)>{{ Lang::txt('JTRASHED') }}</option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-paramsaccess" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS') }}
        </label>
        @php
          $rconfig = Component::params('com_resources');
        @endphp
        {!! \Components\Resources\Helpers\Html::selectAccess(
            $rconfig->get('accesses'),
            $params->get('access', 0),
            'params[access]'
        ) !!}
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-paramsgroup" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP') }}
        </label>
        {!! \Components\Resources\Helpers\Html::selectGroup(
            $groups,
            $params->get('group', ''),
            'params[group]',
            'import-group'
        ) !!}
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="param-titlematch" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE') }}
        </label>
        <select name="params[titlematch]" id="param-titlematch" class="select select-bordered w-full">
          <option value="0">{{ Lang::txt('JNo') }}</option>
          <option value="1" @selected($params->get('titlematch', 0) == 1)>{{ Lang::txt('JYes') }}</option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="param-requiredfields" class="label">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED') }}
        </label>
        <select name="params[requiredfields]" id="param-requiredfields" class="select select-bordered w-full">
          <option value="0">
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_NO') }}
          </option>
          <option value="1" @selected($params->get('requiredfields', 1) == 1)>
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_YES') }}
          </option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_HINT') }}
        </p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    @if($import->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ID') }}</td>
                <td>{{ $import->get('id') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDBY') }}</td>
                <td>
                  @php $createdBy = User::getInstance($import->get('created_by')); @endphp
                  {{ $createdBy ? e($createdBy->get('name')) : '' }}
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDON') }}</td>
                <td>{{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}</td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>
    @endif

    <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_UPLOAD') }}">
        <div class="admin-field">
          <label for="field-file" class="label">
            {{ Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATAFILEUPLOAD') }}
          </label>
          <input type="file"
                 name="file"
                 id="field-file"
                 class="file-input file-input-bordered w-full" />
          <p class="text-xs text-muted-foreground mt-1">.csv, .xml</p>
        </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="id" value="{{ $import->get('id') }}" />
  <input type="hidden" name="import[id]" value="{{ $import->get('id') }}" />
</x-admin-edit>
