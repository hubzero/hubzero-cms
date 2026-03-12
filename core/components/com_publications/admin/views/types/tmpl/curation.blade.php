{{--
  Publications Master Type — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Publications\Helpers\Permissions::getActions('type');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(
      Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE') . ': ' . $text,
      'publications'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $__view->css()->js('curation.blade.js');

  $params = new \Hubzero\Config\Registry($row->params ?? '');

  $pluginActive = Plugin::isEnabled('projects', $row->alias ?? '');

  // Available panels and default config: 0=hide, 1=show, 2=show+require
  $panels = [
      'content'     => 2,
      'description' => 2,
      'authors'     => 2,
      'audience'    => 0,
      'gallery'     => 1,
      'tags'        => 1,
      'access'      => 0,
      'license'     => 2,
      'citations'   => 1,
      'notes'       => 1,
  ];
  // These panels cannot be hidden
  $required = ['content', 'description', 'authors'];
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_MTYPE_INFO') }}">

    <div class="admin-field">
      <label for="field-type" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_NAME') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[type]"
             id="field-type"
             class="input input-bordered w-full"
             maxlength="100"
             required
             value="{{ $row->type ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-alias" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') }}</label>
      <input type="text"
             name="fields[alias]"
             id="field-alias"
             class="input input-bordered w-full"
             maxlength="100"
             value="{{ $row->alias ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-description" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DESCRIPTION') }}</label>
      <input type="text"
             name="fields[description]"
             id="field-description"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->description ?? '' }}" />
    </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_DRAFT_SECTIONS') }}">
    <div class="overflow-x-auto">
      <table class="table table-sm w-full">
        <thead>
          <tr>
            <th>{{ Lang::txt('COM_PUBLICATIONS_DRAFT_SECTIONS') }}</th>
            <th>{{ Lang::txt('COM_PUBLICATIONS_HIDE') }}</th>
            <th>{{ Lang::txt('COM_PUBLICATIONS_SHOW') }}</th>
            <th>{{ Lang::txt('COM_PUBLICATIONS_SHOW_AND_REQUIRE') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($panels as $panel => $defaultVal)
            @php
              $inputName = 'params[show_' . $panel . ']';
              $current   = $params->get('show_' . $panel, $defaultVal);
              $disabled  = in_array($panel, $required);
            @endphp
            <tr>
              <td class="font-medium">{{ ucfirst($panel) }}</td>
              <td>
                <input type="radio"
                       name="{{ $inputName }}"
                       value="0"
                       class="radio radio-sm"
                       aria-label="{{ Lang::txt('COM_PUBLICATIONS_HIDE') }} {{ ucfirst($panel) }}"
                       {{ $current == 0 ? 'checked' : '' }}
                       {{ $disabled ? 'disabled' : '' }} />
              </td>
              <td>
                <input type="radio"
                       name="{{ $inputName }}"
                       value="1"
                       class="radio radio-sm"
                       aria-label="{{ Lang::txt('COM_PUBLICATIONS_SHOW') }} {{ ucfirst($panel) }}"
                       {{ $current == 1 ? 'checked' : '' }} />
              </td>
              <td>
                <input type="radio"
                       name="{{ $inputName }}"
                       value="2"
                       class="radio radio-sm"
                       aria-label="{{ Lang::txt('COM_PUBLICATIONS_SHOW_AND_REQUIRE') }} {{ ucfirst($panel) }}"
                       {{ $current == 2 ? 'checked' : '' }} />
              </td>
            </tr>
          @endforeach

          {{-- Metadata (hide/show only) --}}
          <tr>
            <td class="font-medium">{{ Lang::txt('COM_PUBLICATIONS_FIELD_METADATA') }}</td>
            <td>
              <input type="radio" name="params[show_metadata]" value="0"
                     class="radio radio-sm"
                     aria-label="{{ Lang::txt('COM_PUBLICATIONS_HIDE') }} {{ Lang::txt('COM_PUBLICATIONS_FIELD_METADATA') }}"
                     {{ $params->get('show_metadata', 0) == 0 ? 'checked' : '' }} />
            </td>
            <td>
              <input type="radio" name="params[show_metadata]" value="1"
                     class="radio radio-sm"
                     aria-label="{{ Lang::txt('COM_PUBLICATIONS_SHOW') }} {{ Lang::txt('COM_PUBLICATIONS_FIELD_METADATA') }}"
                     {{ $params->get('show_metadata', 0) == 1 ? 'checked' : '' }} />
            </td>
            <td></td>
          </tr>

          {{-- Submitter (hide/show only) --}}
          <tr>
            <td class="font-medium">{{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER') }}</td>
            <td>
              <input type="radio" name="params[show_submitter]" value="0"
                     class="radio radio-sm"
                     aria-label="{{ Lang::txt('COM_PUBLICATIONS_HIDE') }} {{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER') }}"
                     {{ $params->get('show_submitter', 0) == 0 ? 'checked' : '' }} />
            </td>
            <td>
              <input type="radio" name="params[show_submitter]" value="1"
                     class="radio radio-sm"
                     aria-label="{{ Lang::txt('COM_PUBLICATIONS_SHOW') }} {{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER') }}"
                     {{ $params->get('show_submitter', 0) == 1 ? 'checked' : '' }} />
            </td>
            <td></td>
          </tr>
        </tbody>
      </table>
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
          <td>{{ Lang::txt('COM_PUBLICATIONS_MTYPE_IS_SUPPORTED') }}</td>
          <td>
            @if($pluginActive)
              <span class="badge badge-success">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_ON') }}</span>
            @else
              <span class="badge badge-ghost">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_OFF') }}</span>
            @endif
          </td>
        </tr>
      </tbody>
    </table>

    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_ITEM_CONFIG') }}">

      {{-- Contributable --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE') }}</p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[contributable]" value="1"
                   class="radio radio-sm"
                   {{ ($row->contributable ?? 0) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[contributable]" value="0"
                   class="radio radio-sm"
                   {{ ($row->contributable ?? 0) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

      {{-- Supporting --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('Supporting') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE_SUPPORT') }}</p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[supporting]" value="1"
                   class="radio radio-sm"
                   {{ ($row->supporting ?? 0) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[supporting]" value="0"
                   class="radio radio-sm"
                   {{ ($row->supporting ?? 0) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
        <p class="text-xs text-muted-foreground mt-2">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE_NOTICE') }}</p>
      </fieldset>

      {{-- Issue DOI --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('Issue DOI') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_DOI_QUESTION') }}</p>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="params[issue_doi]" value="1"
                   class="radio radio-sm"
                   {{ $params->get('issue_doi', 1) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('JOPTION_REQUIRED') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="params[issue_doi]" value="2"
                   class="radio radio-sm"
                   {{ $params->get('issue_doi', 1) == 2 ? 'checked' : '' }} />
            {{ Lang::txt('JOPTION_OPTIONAL') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="params[issue_doi]" value="0"
                   class="radio radio-sm"
                   {{ $params->get('issue_doi', 1) == 0 ? 'checked' : '' }} />
            {{ Lang::txt('COM_PUBLICATIONS_NA') }}
          </label>
        </div>
      </fieldset>

      {{-- Default Category --}}
      <div class="admin-field">
        <label for="field-default_category" class="label text-base-content">
          {{ Lang::txt('COM_PUBLICATIONS_MTYPE_DEFAULT_CAT') }}
        </label>
        <p class="text-xs text-muted-foreground mb-1">{{ Lang::txt('COM_PUBLICATIONS_MTYPE_CHOOSE_CAT') }}</p>
        <select name="params[default_category]"
                id="field-default_category"
                class="select select-bordered w-full">
          @foreach($cats as $cat)
            <option value="{{ $cat->id }}"
                    {{ $params->get('default_category', 1) == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[ordering]" value="{{ $row->ordering ?? 0 }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->id ?? 0 }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
