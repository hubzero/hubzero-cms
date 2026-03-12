{{--
  com_languages — Language override edit form

  Variables: $item (object: key, override, language, client, file), $option, $controller, $task

  Keeps component overrider.js (AJAX string search).

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $canDo = \Components\Languages\Helpers\Utilities::getActions();
  $isNew = empty($item->key);

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );

  $cacheExpired = Request::getString('cache_expired') ? 'expired' : '';

  // Component-specific JS/CSS for AJAX override search
  $__view->js('overrider');
  $__view->css('overrider');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_TITLE') }}"
    icon="langmanager"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}"
      data-cache_expired="{{ $cacheExpired }}">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">

    {{-- Left column: override fields --}}
    <div class="min-w-0">
      <x-admin-fieldset legend="{{ $isNew
          ? Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND')
          : Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND') }}">

        <div class="admin-field">
          <label for="field-key" class="label">
            {{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_LABEL') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[key]"
                 id="field-key"
                 class="input input-bordered w-full"
                 required
                 value="{{ $item->key }}" />
          <p class="text-xs text-muted-foreground mt-1">{!! Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_DESC') !!}</p>
        </div>

        <div class="admin-field">
          <label for="field-override" class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_LABEL') }}</label>
          <textarea name="fields[override]"
                    id="field-override"
                    class="textarea textarea-bordered w-full"
                    rows="5">{{ $item->override }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">{!! Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_DESC') !!}</p>
        </div>

        @if ($item->client === 'administrator')
          <div class="admin-field">
            <label class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_LABEL') }}</label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox"
                     name="fields[both]"
                     id="field-both"
                     class="checkbox"
                     value="true" />
              <span class="text-sm">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_LABEL') }}</span>
            </label>
            <p class="text-xs text-muted-foreground mt-1">{!! Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_DESC') !!}</p>
          </div>
        @endif

        <div class="admin-field">
          <label for="field-language" class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_LABEL') }}</label>
          <input type="text"
                 name="fields[language]"
                 id="field-language"
                 class="input input-bordered w-full"
                 readonly
                 value="{{ $item->language }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_DESC') }}</p>
        </div>

        <div class="admin-field">
          <label for="field-client" class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_LABEL') }}</label>
          <input type="text"
                 name="fields[client]"
                 id="field-client"
                 class="input input-bordered w-full"
                 readonly
                 value="{{ $item->client }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_DESC') }}</p>
        </div>

        <div class="admin-field">
          <label for="field-file" class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_LABEL') }}</label>
          <input type="text"
                 name="fields[file]"
                 id="field-file"
                 class="input input-bordered w-full"
                 readonly
                 value="{{ $item->file }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_DESC') }}</p>
        </div>

      </x-admin-fieldset>
    </div>

    {{-- Right column: AJAX search --}}
    <div>
      <x-admin-fieldset legend="{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_LEGEND') }}">

        <p class="text-sm text-muted-foreground mb-3">
          {!! Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_TIP') !!}
        </p>

        <div id="refresh-status" class="overrider-spinner hidden text-sm text-muted-foreground mb-2">
          {{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_REFRESHING') }}
        </div>

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_LABEL') }}</label>
          <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio"
                     id="jform_searchtype0"
                     name="fields[searchtype]"
                     value="constant"
                     class="radio radio-sm" />
              <span class="text-sm">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_CONSTANT') }}</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio"
                     id="jform_searchtype1"
                     name="fields[searchtype]"
                     value="value"
                     checked
                     class="radio radio-sm" />
              <span class="text-sm">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_TEXT') }}</span>
            </label>
          </div>
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_DESC') }}</p>
        </div>

        <div class="admin-field">
          <label for="fields_searchstring" class="sr-only">{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON') }}</label>
          <input type="text"
                 name="fields[searchstring]"
                 id="fields_searchstring"
                 class="input input-bordered w-full" />
        </div>

        <button type="button"
                id="searchstrings"
                class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON') }}
        </button>

      </x-admin-fieldset>

      {{-- Search results (populated by overrider.js) --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND') }}">
        <div id="results-container">
          <a href="#"
             id="more-results"
             class="link link-primary text-sm hidden">
            {{ Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_MORE_RESULTS') }}
          </a>
        </div>
      </x-admin-fieldset>
    </div>

  </div>

  <input type="hidden" name="option"     value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task"       value="" />
  <input type="hidden" name="id"         value="{{ $item->key }}" />
  {!! Html::input('token') !!}

</form>
