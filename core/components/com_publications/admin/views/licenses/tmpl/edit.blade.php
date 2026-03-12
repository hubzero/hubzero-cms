{{--
  Publications License — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Publications\Helpers\Permissions::getActions('license');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_PUBLICATIONS_LICENSE') . ': ' . $text, 'publications');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $licText = preg_replace("/\r\n/", "\r", trim($row->text ?? ''));
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<x-admin-edit
    option="{{ $option }}"
    controller="licenses"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_LICENSE_DETAILS') }}">

    <div class="admin-field">
      <label for="field-title" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_TITLE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             maxlength="100"
             required
             value="{{ $row->title ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-name" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_NAME') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[name]"
             id="field-name"
             class="input input-bordered w-full"
             maxlength="100"
             required
             value="{{ $row->name ?? '' }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_PUBLICATIONS_LICENSE_NAME_HINT') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-url" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_URL') }}</label>
      <input type="text"
             name="fields[url]"
             id="field-url"
             class="input input-bordered w-full"
             maxlength="100"
             value="{{ $row->url ?? '' }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_PUBLICATIONS_LICENSE_URL_HINT') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-info" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_ABOUT') }}
        <span class="text-error">*</span>
      </label>
      <textarea name="fields[info]"
                id="field-info"
                class="textarea textarea-bordered w-full"
                rows="5"
                required>{{ $row->info ?? '' }}</textarea>
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_PUBLICATIONS_LICENSE_DESC_HINT') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-text" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_CONTENT') }}</label>
      <textarea name="fields[text]"
                id="field-text"
                class="textarea textarea-bordered w-full font-mono text-sm"
                rows="20">{{ $licText }}</textarea>
    </div>

    <div class="admin-field">
      <label for="field-icon" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ICON') }}</label>
      <input type="text"
             name="fields[icon]"
             id="field-icon"
             class="input input-bordered w-full"
             value="{{ $row->icon ?? '' }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ICON_HINT') }}</p>
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_ID') }}</td>
          <td>{{ $row->id ?: Lang::txt('JNONE') }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_DEFAULT') }}</td>
          <td>
            @if(method_exists($row, 'isMain') ? $row->isMain() : ($row->main ?? 0))
              {{ Lang::txt('COM_PUBLICATIONS_LICENSE_YES') }}
            @else
              {{ Lang::txt('COM_PUBLICATIONS_LICENSE_NO') }}
            @endif
          </td>
        </tr>
        @if($row->id)
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_ORDERING') }}</td>
            <td>{{ $row->ordering }}</td>
          </tr>
        @endif
      </tbody>
    </table>

    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_LICENSE_CONFIGURATION') }}">

      {{-- Active --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN') }}</p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[active]" id="field-active1" value="1"
                   class="radio radio-sm"
                   {{ ($row->active ?? 1) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[active]" id="field-active0" value="0"
                   class="radio radio-sm"
                   {{ ($row->active ?? 1) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

      {{-- Customizable --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE_HINT') }}</p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[customizable]" id="field-customizable1" value="1"
                   class="radio radio-sm"
                   {{ ($row->customizable ?? 0) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[customizable]" id="field-customizable0" value="0"
                   class="radio radio-sm"
                   {{ ($row->customizable ?? 0) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

      {{-- Agreement --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('Agreement required') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">
          {{ Lang::txt('Do we require publication authors to agree to license terms?') }}
        </p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[agreement]" id="field-agreement1" value="1"
                   class="radio radio-sm"
                   {{ ($row->agreement ?? 0) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[agreement]" id="field-agreement0" value="0"
                   class="radio radio-sm"
                   {{ ($row->agreement ?? 0) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

      {{-- Derivatives --}}
      <fieldset class="border border-base-300 rounded-box p-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('Allow Derivatives') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">
          {{ Lang::txt('Are derivatives allowed under the terms of this license?') }}
        </p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[derivatives]" id="field-derivatives1" value="1"
                   class="radio radio-sm"
                   {{ ($row->derivatives ?? 0) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[derivatives]" id="field-derivatives0" value="0"
                   class="radio radio-sm"
                   {{ ($row->derivatives ?? 0) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[ordering]" value="{{ $row->ordering ?? 0 }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->id ?? 0 }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
