{{--
  Publications Category — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Publications\Helpers\Permissions::getActions('category');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_CATEGORY') . ': ' . $text, 'category');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $__view->css();

  $dcTypes = [
      'Collection', 'Dataset', 'Event', 'Image',
      'InteractivePublication', 'MovingImage', 'PhysicalObject',
      'Service', 'Software', 'Sound', 'StillImage', 'Text',
  ];

  $params = $row->params;

  // Load publications plugins
  $database = App::get('db');
  $database->setQuery(
      "SELECT * FROM `#__extensions`"
      . " WHERE `type`='plugin' AND `folder`='publications'"
  );
  $plugins = $database->loadObjectList() ?: [];
  $foundPlugins = [];

  // Build elements/custom fields schema
  $elements   = new \Components\Publications\Models\Elements('', $row->customFields ?? '');
  $schema     = $elements->getSchema();
  if (!is_object($schema)) {
      $schema = new stdClass();
      $schema->fields = [];
  }
  if (count($schema->fields) <= 0) {
      $fs = explode(
          ',',
          $config->get('tagsothr', 'bio,credits,citations,sponsoredby,references,publications')
      );
      foreach ($fs as $f) {
          $f = trim($f);
          $element = new stdClass();
          $element->name     = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($f));
          $element->label    = ucfirst($f);
          $element->type     = 'text';
          $element->required = '';
          $element->value    = '';
          $element->default  = '';
          $element->description = '';
          $schema->fields[] = $element;
      }
  }

  $routeUrl = Route::url(
      'index.php?option=com_publications'
      . '&controller=categories'
      . '&no_html=1'
      . '&task=element'
      . '&ctrl=fields', false
  );

  $__view->js('categories.blade.js');
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Category information --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_CATEGORY_INFORMATION') }}">

    @if($row->id)
      <div class="admin-field">
        <label class="label text-base-content">{{ Lang::txt('ID') }}</label>
        <span class="font-mono">{{ $row->id }}</span>
      </div>
    @endif

    <div class="admin-field">
      <label for="field-name" class="label text-base-content">
        {{ Lang::txt('COM_PUBLICATIONS_FIELD_NAME') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="prop[name]"
             id="field-name"
             class="input input-bordered w-full"
             maxlength="100"
             required
             value="{{ $row->name ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-alias" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') }}</label>
      <input type="text"
             name="prop[alias]"
             id="field-alias"
             class="input input-bordered w-full"
             maxlength="100"
             value="{{ $row->alias ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-url_alias" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS') }}</label>
      <input type="text"
             name="prop[url_alias]"
             id="field-url_alias"
             class="input input-bordered w-full"
             maxlength="100"
             value="{{ $row->url_alias ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-dc_type" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE') }}</label>
      <select name="prop[dc_type]"
              id="field-dc_type"
              class="select select-bordered w-full">
        @foreach($dcTypes as $dct)
          <option value="{{ $dct }}" {{ ($row->dc_type ?? '') == $dct ? 'selected' : '' }}>
            {{ $dct }}
          </option>
        @endforeach
      </select>
      <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DUBLIN_CORE') }}</p>
    </div>

    <div class="admin-field">
      <label for="field-description" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ABOUT') }}</label>
      <input type="text"
             name="prop[description]"
             id="field-description"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->description ?? '' }}" />
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_ITEM_CONFIG') }}">

      {{-- Status --}}
      <fieldset class="border border-base-300 rounded-box p-3 mb-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_STATUS') }}</legend>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="prop[state]" id="field-state1" value="1"
                   class="radio radio-sm"
                   {{ ($row->state ?? 1) == 1 ? 'checked' : '' }} />
            {{ Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="prop[state]" id="field-state0" value="0"
                   class="radio radio-sm"
                   {{ ($row->state ?? 1) != 1 ? 'checked' : '' }} />
            {{ Lang::txt('COM_PUBLICATIONS_STATUS_INACTIVE') }}
          </label>
        </div>
      </fieldset>

      {{-- Contributable --}}
      <fieldset class="border border-base-300 rounded-box p-3">
        <legend class="text-sm font-semibold px-1">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE') }}</legend>
        <p class="text-xs text-muted-foreground mb-2">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE_HINT') }}</p>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="prop[contributable]" id="field-contributable1" value="1"
                   class="radio radio-sm"
                   {{ (method_exists($row, 'isContributable') ? $row->isContributable() : ($row->contributable ?? 0)) ? 'checked' : '' }} />
            {{ Lang::txt('JYES') }}
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="prop[contributable]" id="field-contributable0" value="0"
                   class="radio radio-sm"
                   {{ !(method_exists($row, 'isContributable') ? $row->isContributable() : ($row->contributable ?? 0)) ? 'checked' : '' }} />
            {{ Lang::txt('JNO') }}
          </label>
        </div>
      </fieldset>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="prop[id]" value="{{ $row->id ?? 0 }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>

{{-- Master type config (below the two-column layout) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
  <div>
    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_CATS_MASTER_TYPE_CONFIG') }}">
      @foreach($types as $mt)
        <fieldset class="border border-base-300 rounded-box p-3 mb-2">
          <legend class="text-sm font-semibold px-1">{{ $mt }}</legend>
          <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio"
                     name="params[type_{{ $mt }}]"
                     value="1"
                     class="radio radio-sm"
                     {{ $params->get('type_' . $mt, 1) == 1 ? 'checked' : '' }} />
              {{ Lang::txt('COM_PUBLICATIONS_INCLUDE_CHOICE') }}
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio"
                     name="params[type_{{ $mt }}]"
                     value="0"
                     class="radio radio-sm"
                     {{ $params->get('type_' . $mt, 1) == 0 ? 'checked' : '' }} />
              {{ Lang::txt('COM_PUBLICATIONS_NOT_APPLICABLE') }}
            </label>
          </div>
        </fieldset>
      @endforeach
    </x-admin-fieldset>
  </div>

  <div>
    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_PLUGINS') }}">
      <div class="overflow-x-auto">
        <table class="table table-sm w-full">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_PUBLICATIONS_PLUGIN') }}</th>
              <th colspan="2">{{ Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($plugins as $plugin)
              @php
                $plgKey = 'plg_' . $plugin->element;
                if (in_array($plgKey, $foundPlugins)) { continue; }
                $foundPlugins[] = $plgKey;
                if (strstr($plugin->name, '_')) {
                    Lang::load($plugin->name) || Lang::load(
                        $plugin->name,
                        PATH_CORE . DS . 'plugins' . DS . $plugin->folder . DS . $plugin->element
                    );
                }
                $plgName  = strstr($plugin->name, '_')
                    ? Lang::txt($plugin->name)
                    : ucfirst($plugin->name);
                $plgInput = 'params[plg_' . $plugin->element . ']';
                $plgVal   = $params->get('plg_' . $plugin->element, 0);
              @endphp
              <tr>
                <td>{{ $plgName }}</td>
                <td>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="{{ $plgInput }}" value="0"
                           class="radio radio-sm"
                           {{ $plgVal == 0 ? 'checked' : '' }} />
                    {{ Lang::txt('JOFF') }}
                  </label>
                </td>
                <td>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="{{ $plgInput }}" value="1"
                           class="radio radio-sm"
                           {{ $plgVal == 1 ? 'checked' : '' }} />
                    {{ Lang::txt('JON') }}
                  </label>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </x-admin-fieldset>
  </div>
</div>

{{-- Custom fields (only if curation mode is off) --}}
@if(!$config->get('curation', 0))
  <div class="mt-6">
    <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_TYPES_CUSTOM_FIELDS') }}">
      <div class="overflow-x-auto">
        <table class="table table-sm w-full" id="fields" data-href="{!! $routeUrl !!}">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_PUBLICATIONS_TYPES_REORDER') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_TYPES_FIELD') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_TYPES_TYPE') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_TYPES_REQUIRED') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_TYPES_OPTIONS') }}</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td colspan="5">
                <button type="button" id="add-custom-field" class="btn btn-sm btn-ghost">
                  {{ Lang::txt('COM_PUBLICATIONS_ADD_ROW') }}
                </button>
              </td>
            </tr>
          </tfoot>
          <tbody id="field-items">
            @foreach($schema->fields as $fi => $field)
              <tr>
                <td class="order">
                  <span class="handle cursor-move" title="{{ Lang::txt('COM_PUBLICATIONS_MOVE_HANDLE') }}">
                    &#8597;
                  </span>
                </td>
                <td>
                  <input type="text"
                         name="fields[{{ $fi }}][title]"
                         class="input input-bordered input-sm w-full"
                         value="{{ $field->label }}"
                         maxlength="255"
                         aria-label="{{ Lang::txt('COM_PUBLICATIONS_TYPES_FIELD') }}" />
                  <input type="hidden"
                         name="fields[{{ $fi }}][name]"
                         value="{{ $field->name }}" />
                </td>
                <td>
                  <select name="fields[{{ $fi }}][type]"
                          id="fields-{{ $fi }}-type"
                          class="select select-bordered select-sm"
                          aria-label="{{ Lang::txt('COM_PUBLICATIONS_TYPES_TYPE') }}">
                    <optgroup label="{{ Lang::txt('COM_PUBLICATIONS_COMMON') }}">
                      <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_TEXT') }}
                      </option>
                      <option value="textarea" {{ $field->type == 'textarea' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_TEXTAREA') }}
                      </option>
                      <option value="list" {{ $field->type == 'list' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_LIST') }}
                      </option>
                      <option value="radio" {{ $field->type == 'radio' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_RADIO') }}
                      </option>
                      <option value="checkbox" {{ $field->type == 'checkbox' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_CHECKBOX') }}
                      </option>
                      <option value="hidden" {{ $field->type == 'hidden' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_TYPES_HIDDEN') }}
                      </option>
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_PUBLICATIONS_PRE_DEFINED') }}">
                      <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>
                        {{ Lang::txt('Date') }}
                      </option>
                      <option value="geo" {{ $field->type == 'geo' ? 'selected' : '' }}>
                        {{ Lang::txt('Geo Location') }}
                      </option>
                      <option value="languages" {{ $field->type == 'languages' ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_LANGUAGE_LIST') }}
                      </option>
                    </optgroup>
                  </select>
                </td>
                <td>
                  <input type="checkbox"
                         name="fields[{{ $fi }}][required]"
                         value="1"
                         class="checkbox checkbox-sm"
                         aria-label="{{ Lang::txt('COM_PUBLICATIONS_TYPES_REQUIRED') }}"
                         {{ $field->required ? 'checked' : '' }} />
                </td>
                <td id="fields-{{ $fi }}-options">
                  {!! $elements->getElementOptions($fi, $field, 'fields') !!}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </x-admin-fieldset>
  </div>
@endif
