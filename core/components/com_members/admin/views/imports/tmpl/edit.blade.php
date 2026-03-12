{{--
  Member Import — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Admin::getActions('component');

  $title = $import->get('id')
      ? Lang::txt('COM_MEMBERS_IMPORT_TITLE_EDIT')
      : Lang::txt('COM_MEMBERS_IMPORT_TITLE_ADD');

  $__view->css('import')->js('import');
@endphp

@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $title, 'import');
  if ($canDo->get('core.admin')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  {{-- Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DETAILS') }}">
      <div class="admin-field">
        <label for="field-name" class="label">
          {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="import[name]"
               id="field-name"
               class="input input-bordered w-full"
               required
               value="{{ $import->get('name') }}" />
      </div>

      <div class="admin-field">
        <label for="field-notes" class="label">
          {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_NOTES') }}
        </label>
        <textarea name="import[notes]"
                  id="field-notes"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $import->get('notes') }}</textarea>
  </x-admin-fieldset>

  {{-- Hooks fieldset --}}
  @php
    $hooksData = json_decode($import->get('hooks') ?: '');
    if (!is_object($hooksData)) {
        $hooksData = new stdClass();
    }
    $hooksData->postparse   = $hooksData->postparse ?? [];
    $hooksData->postmap     = $hooksData->postmap ?? [];
    $hooksData->postconvert = $hooksData->postconvert ?? [];
  @endphp

  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_HOOKS') }}">
      @if($hooks->count())
        <div class="admin-field">
          <label for="field-hookpostparse" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTPARSEHOOK') }}
          </label>
          <select name="hooks[postparse][]"
                  id="field-hookpostparse"
                  class="select select-bordered w-full"
                  multiple="multiple">
            @foreach($hooksData->postparse as $hookId)
              @php $importHook = $hooks->fetch('id', $hookId); @endphp
              <option selected="selected"
                      value="{{ $importHook->get('id') }}">{{ $importHook->get('name') }}</option>
            @endforeach
            @foreach($hooks as $hook)
              @if($hook->get('event') != 'postparse' || in_array($hook->get('id'), $hooksData->postparse))
                @continue
              @endif
              <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
            @endforeach
          </select>
          <a class="hook-up" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
          <a class="hook-down" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
          <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
        </div>

        <div class="admin-field">
          <label for="field-hookpostmap" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTMAPHOOK') }}
          </label>
          <select name="hooks[postmap][]"
                  id="field-hookpostmap"
                  class="select select-bordered w-full"
                  multiple="multiple">
            @foreach($hooksData->postmap as $hookId)
              @php $importHook = $hooks->fetch('id', $hookId); @endphp
              <option selected="selected"
                      value="{{ $importHook->get('id') }}">{{ $importHook->get('name') }}</option>
            @endforeach
            @foreach($hooks as $hook)
              @if($hook->get('event') != 'postmap' || in_array($hook->get('id'), $hooksData->postmap))
                @continue
              @endif
              <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
            @endforeach
          </select>
          <a class="hook-up" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
          <a class="hook-down" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
          <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
        </div>

        <div class="admin-field">
          <label for="field-hookpostconvert" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTCONVERTHOOK') }}
          </label>
          <select name="hooks[postconvert][]"
                  id="field-hookpostconvert"
                  class="select select-bordered w-full"
                  multiple="multiple">
            @foreach($hooksData->postconvert as $hookId)
              @php $importHook = $hooks->fetch('id', $hookId); @endphp
              <option selected="selected"
                      value="{{ $importHook->get('id') }}">{{ $importHook->get('name') }}</option>
            @endforeach
            @foreach($hooks as $hook)
              @if($hook->get('event') != 'postconvert' || in_array($hook->get('id'), $hooksData->postconvert))
                @continue
              @endif
              <option value="{{ $hook->get('id') }}">{{ $hook->get('name') }}</option>
            @endforeach
          </select>
          <a class="hook-up" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP') }}</a> |
          <a class="hook-down" href="#">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN') }}</a><br />
          <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT') }}</span>
        </div>
      @else
        <div class="admin-field">
          <em>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_NO_HOOKS_FOUND') }}</em>
          <input type="hidden" name="hooks[postparse][]" value="" />
          <input type="hidden" name="hooks[postmap][]" value="" />
          <input type="hidden" name="hooks[postconvert][]" value="" />
        </div>
      @endif
  </x-admin-fieldset>

  {{-- Params fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_PARAMS') }}">
      <div class="admin-field">
        <label for="param-approved" class="label">
          {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED') }}
        </label>
        <select name="params[approved]"
                id="param-approved"
                class="select select-bordered w-full">
          <option value="0" @selected($params->get('approved', 1) == 0)>{{ Lang::txt('JNO') }}</option>
          <option value="1" @selected($params->get('approved', 1) == 1)>{{ Lang::txt('JYES') }}</option>
        </select>
        <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED_HINT') }}</span>
      </div>

      <div class="admin-field">
        <label for="param-emailnew" class="label">
          {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW') }}
        </label>
        <select name="params[emailnew]"
                id="param-emailnew"
                class="select select-bordered w-full">
          <option value="0" @selected($params->get('emailnew', 0) == 0)>{{ Lang::txt('JNO') }}</option>
          <option value="1" @selected($params->get('emailnew', 0) == 1)>{{ Lang::txt('JYES') }}</option>
        </select>
        <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW_HINT') }}</span>
  </x-admin-fieldset>

  {{-- Field mapping partial --}}
  @include('com_members::admin.views.imports.tmpl._fieldmap', ['import' => $import])

  @slot('sidebar')
    {{-- Meta table --}}
    @if($import->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_ID') }}</td>
                <td>{{ $import->get('id') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY') }}</td>
                <td>
                  @php
                    $createdBy = User::getInstance($import->get('created_by'));
                  @endphp
                  @if($createdBy)
                    {{ $createdBy->get('name') }}
                  @endif
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON') }}</td>
                <td>
                  <time datetime="{{ $import->get('created_at') }}">
                    {{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}
                  </time>
                </td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>
    @endif

    {{-- Upload fieldset --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_UPLOAD') }}">
        <div class="admin-field">
          <label for="field-file" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATAFILEUPLOAD') }}
          </label>
          <input type="file" name="file" id="field-file" class="file-input file-input-bordered w-full" />
        </div>
    </x-admin-fieldset>

    {{-- Data fieldset --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DATA') }}">
        @php
          $fileHint = Lang::txt(
              'COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_HINT',
              $import->fileSpacePath()
          );
        @endphp
        <div class="admin-field">
          <label for="field-importfile" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE') }}
          </label>
          <select name="import[file]"
                  id="field-importfile"
                  class="select select-bordered w-full">
            <option value="">{!! Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL') !!}</option>
            @if(isset($files))
              @foreach($files as $file)
                @php $file = ltrim($file, DIRECTORY_SEPARATOR); @endphp
                <option value="{{ $file }}" @selected($import->get('file') == $file)>{{ $file }}</option>
              @endforeach
            @endif
          </select>
          <span class="hint">{{ $fileHint }}</span>
        </div>

        <div class="admin-field">
          <label for="field-importmode" class="label">
            {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE') }}
          </label>
          <select name="import[mode]"
                  id="field-importmode"
                  class="select select-bordered w-full">
            <option value="UPDATE">
              {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE') }}
            </option>
            <option value="PATCH" @selected($import->get('mode') == 'PATCH')>
              {{ Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_PATCH') }}
            </option>
          </select>
          <span class="hint">{!! Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_HINT') !!}</span>
        </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="import[id]" value="{{ $import->get('id') }}" />
</x-admin-edit>
