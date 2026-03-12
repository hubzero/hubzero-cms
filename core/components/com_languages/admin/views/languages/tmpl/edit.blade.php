{{--
  com_languages — Language edit form

  Variables: $item (language object), $option, $controller, $task

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $canDo = \Components\Languages\Helpers\Utilities::getActions();
  $isNew = empty($item->lang_id);

  $titleKey = $isNew
      ? 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE'
      : 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_EDIT_TITLE';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt($titleKey) }}"
    icon="langmanager"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>

  {{-- Main (left) column --}}
  <x-admin-fieldset legend="{{ $isNew
      ? Lang::txt('COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE')
      : Lang::txt('JGLOBAL_RECORD_NUMBER', $item->lang_id) }}">

    <div class="admin-field">
      <label for="field-title" class="label">
        {{ Lang::txt('JGLOBAL_TITLE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             maxlength="50"
             required
             value="{{ $item->title }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_TITLE_DESC') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-title_native" class="label">
        {{ Lang::txt('COM_LANGUAGES_FIELD_TITLE_NATIVE_LABEL') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[title_native]"
             id="field-title_native"
             class="input input-bordered w-full"
             maxlength="50"
             required
             value="{{ $item->title_native }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_TITLE_NATIVE_DESC') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-sef" class="label">
        {{ Lang::txt('COM_LANGUAGES_FIELD_LANG_CODE_LABEL') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[sef]"
             id="field-sef"
             class="input input-bordered w-full"
             maxlength="7"
             required
             value="{{ $item->sef }}" />
      <p class="text-xs text-muted-foreground mt-1">{!! Lang::txt('COM_LANGUAGES_FIELD_LANG_CODE_DESC') !!}</p>
    </div>

    <div class="admin-field">
      <label for="field-image" class="label">
        {{ Lang::txt('COM_LANGUAGES_FIELD_IMAGE_LABEL') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[image]"
             id="field-image"
             class="input input-bordered w-full"
             maxlength="7"
             required
             value="{{ $item->image }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_IMAGE_DESC') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-lang_code" class="label">
        {{ Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[lang_code]"
             id="field-lang_code"
             class="input input-bordered w-full"
             maxlength="7"
             required
             value="{{ $item->lang_code }}" />
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_DESC') }}</p>
    </div>

    @if ($canDo->get('core.edit.state'))
      <div class="admin-field">
        <label for="field-published" class="label">{{ Lang::txt('JSTATUS') }}</label>
        <select name="fields[published]"
                id="field-published"
                class="select select-bordered w-full">
          <option value="0" {{ $item->published == 0 ? 'selected' : '' }}>
            {{ Lang::txt('JUNPUBLISHED') }}
          </option>
          <option value="1" {{ $item->published == 1 ? 'selected' : '' }}>
            {{ Lang::txt('JPUBLISHED') }}
          </option>
          <option value="-2" {{ $item->published == -2 ? 'selected' : '' }}>
            {{ Lang::txt('JTRASHED') }}
          </option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_PUBLISHED_DESC') }}</p>
      </div>
    @endif

    <div class="admin-field">
      <label for="field-access" class="label">{{ Lang::txt('JFIELD_ACCESS_LABEL') }}</label>
      <select name="fields[access]"
              id="field-access"
              class="select select-bordered w-full">
        {!! Html::select('options', Html::access('assetgroups'),
            'value', 'text', $item->access) !!}
      </select>
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('JFIELD_ACCESS_DESC') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-description" class="label">{{ Lang::txt('JGLOBAL_DESCRIPTION') }}</label>
      <textarea name="fields[description]"
                id="field-description"
                class="textarea textarea-bordered w-full"
                rows="5">{{ $item->description }}</textarea>
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_DESCRIPTION_DESC') }}</p>
    </div>

    @if (!$isNew)
      <div class="admin-field">
        <label for="field-lang_id" class="label">{{ Lang::txt('JGLOBAL_FIELD_ID_LABEL') }}</label>
        <input type="text"
               id="field-lang_id"
               class="input input-bordered w-full"
               readonly
               value="{{ $item->lang_id }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('JGLOBAL_FIELD_ID_DESC') }}</p>
      </div>
    @endif

  </x-admin-fieldset>

  <input type="hidden" name="fields[lang_id]" value="{{ (int) $item->lang_id }}" />

  {{-- Sidebar (right) column --}}
  @slot('sidebar')

    <details class="admin-fieldset">
      <summary class="admin-fieldset-heading">
        {{ Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS') }}
      </summary>
      <div class="admin-fieldset-body space-y-4">
        <div class="admin-field">
          <label for="field-metakey" class="label">{{ Lang::txt('JFIELD_META_KEYWORDS_LABEL') }}</label>
          <textarea name="fields[metakey]"
                    id="field-metakey"
                    class="textarea textarea-bordered w-full"
                    rows="3">{{ $item->metakey }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('JFIELD_META_KEYWORDS_DESC') }}</p>
        </div>

        <div class="admin-field">
          <label for="field-metadesc" class="label">{{ Lang::txt('JFIELD_META_DESCRIPTION_LABEL') }}</label>
          <textarea name="fields[metadesc]"
                    id="field-metadesc"
                    class="textarea textarea-bordered w-full"
                    rows="3">{{ $item->metadesc ?? '' }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('JFIELD_META_DESCRIPTION_DESC') }}</p>
        </div>
      </div>
    </details>

    <details class="admin-fieldset">
      <summary class="admin-fieldset-heading">
        {{ Lang::txt('COM_LANGUAGES_FIELDSET_SITE_NAME_LABEL') }}
      </summary>
      <div class="admin-fieldset-body space-y-4">
        <div class="admin-field">
          <label for="field-sitename" class="label">{{ Lang::txt('COM_LANGUAGES_FIELD_SITE_NAME_LABEL') }}</label>
          <input type="text"
                 name="fields[sitename]"
                 id="field-sitename"
                 class="input input-bordered w-full"
                 value="{{ $item->sitename }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_FIELD_SITE_NAME_DESC') }}</p>
        </div>
      </div>
    </details>

  @endslot

</x-admin-edit>
