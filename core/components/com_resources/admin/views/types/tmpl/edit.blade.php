{{--
  Resource Type — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo     = \Components\Resources\Helpers\Permissions::getActions('type');
  $text      = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
  $params    = new \Hubzero\Config\Registry($row->get('params'));
  $protected = in_array($row->id, [1, 2, 3, 6, 7, 31]);

  $database = App::get('db');
  $database->setQuery(
      "SELECT * FROM `#__extensions`"
      . " WHERE `type`='plugin'"
      . " AND `folder`='resources'"
      . " AND `enabled`=1"
  );
  $plugins = $database->loadObjectList();
  $lang = Lang::getRoot();
  $found = [];
  foreach ($plugins as $plugin) {
      if (in_array('plg_' . $plugin->element, $found)) {
          continue;
      }
      $found[] = 'plg_' . $plugin->element;
      if (strstr($plugin->name, '_')) {
          $plgPath = '/plugins/' . $plugin->folder . '/' . $plugin->element;
          $lang->load($plugin->name . '.sys')
              || $lang->load($plugin->name . '.sys', PATH_APP . $plgPath)
              || $lang->load($plugin->name . '.sys', PATH_CORE . $plgPath);
      }
  }

  $elements = new \Components\Resources\Models\Elements('', $row->customFields);
  $schema   = $elements->getSchema();
  if (!is_object($schema)) {
      $schema = new stdClass();
      $schema->fields = [];
  }
  if (count($schema->fields) <= 0) {
      $defaultTags = 'bio,credits,citations,sponsoredby,references,publications';
      $fs = explode(',', $config->get('tagsothr', $defaultTags));
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

  $skipPlugins = [
      'groups', 'coins', 'collections', 'dublincore', 'findthistext',
      'googlescholar', 'opengraph', 'watch', 'windowstools', 'citations',
      'questions', 'recommendations', 'reviews', 'share', 'sponsors',
      'usage', 'versions', 'wishlist'
  ];

  $elementHref = Route::url(
      'index.php?option=com_resources&controller=types&no_html=1&task=element&ctrl=fields', false
  );

  $__view->js();
  $__view->css();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_TYPES') }}: {{ $text }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-type" class="label">
          {{ Lang::txt('COM_RESOURCES_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="type[type]"
               id="field-type"
               class="input input-bordered w-full"
               maxlength="100"
               required
               value="{{ $row->type }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS') }}</label>
        <input type="text"
               name="type[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="100"
               @if($protected) readonly @endif
               value="{{ $row->alias }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-category" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_CATEGORY') }}</label>
        {!! \Components\Resources\Helpers\Html::selectType(
            $categories,
            'type[category]',
            $row->category,
            'field-category',
            Lang::txt('COM_RESOURCES_SELECT'),
            'class="select select-bordered w-full"',
            '',
            ''
        ) !!}
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox"
                 name="type[contributable]"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($row->contributable) />
          <span>
            {{ Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE') }}
            <span class="text-xs text-muted-foreground block">
              {{ Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE_EXPLANATION') }}
            </span>
          </span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox"
                 name="type[collection]"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($row->collection) />
          <span>
            {{ Lang::txt('COM_RESOURCES_FIELD_COLLECTION') }}
            <span class="text-xs text-muted-foreground block">
              {{ Lang::txt('COM_RESOURCES_FIELD_COLLECTION_EXPLANATION') }}
            </span>
          </span>
        </label>
      </div>

      @if($row->category != 27)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div class="admin-field">
            <label for="params-linkaction" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION') }}</label>
            <select name="params[linkAction]" id="params-linkaction" class="select select-bordered w-full">
              <option value="extension" @selected($params->get('linkAction') == 'extension')>
                {{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_BY_EXT') }}
              </option>
              <option value="external" @selected($params->get('linkAction') == 'external')>
                {{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_NEW_WINDOW') }}
              </option>
              <option value="lightbox" @selected($params->get('linkAction') == 'lightbox')>
                {{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_LIGHTBOX') }}
              </option>
              <option value="download" @selected($params->get('linkAction') == 'download')>
                {{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DOWNLOAD') }}
              </option>
            </select>
          </div>

          <div class="admin-field">
            <label for="param-restrict" class="label">
              {{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_HINT') }}
            </label>
            <select name="params[restrict_direct_access]" id="param-restrict" class="select select-bordered w-full">
              <option value="0" @selected(!$params->get('restrict_direct_access'))>
                {{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_DEFAULT') }}
              </option>
              <option value="1" @selected($params->get('restrict_direct_access') == 1)>
                {{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_NO') }}
              </option>
              <option value="2" @selected($params->get('restrict_direct_access') == 2)>
                {{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_YES') }}
              </option>
            </select>
          </div>
        </div>
      @endif

      <div class="admin-field mt-4">
        <label for="field-state" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_STATE') }}</label>
        <select name="type[state]" id="field-state" class="select select-bordered w-full">
          <option value="0" @selected($row->state == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
          <option value="1" @selected($row->state == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_DESCIPTION') }}</label>
        {!! $__view->editor(
            'type[description]',
            e($row->description),
            45,
            10,
            'field-description',
            ['class' => 'minimal']
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_FIELDSET_PLUGINS') }}">
        <div class="overflow-x-auto">
          <table class="admin-table text-sm">
            <thead>
              <tr>
                <th>{{ Lang::txt('COM_RESOURCES_COL_PLUGIN') }}</th>
                <th colspan="2">{{ Lang::txt('COM_RESOURCES_COL_ACTIVE') }}</th>
              </tr>
            </thead>
            <tbody>
              @php $pluginFound = []; @endphp
              @foreach($plugins as $plugin)
                @php
                  $plgKey = 'plg_' . $plugin->element;
                  if (in_array($plgKey, $pluginFound)) continue;
                  $pluginFound[] = $plgKey;
                  $pluginTitle = strstr($plugin->name, '_')
                      ? Lang::txt($plugin->name)
                      : $plugin->name;
                  $plgVal = $params->get($plgKey, 0);
                @endphp
                <tr>
                  <td class="font-medium">{{ $pluginTitle }}</td>
                  <td>
                    <label class="flex items-center gap-1 cursor-pointer">
                      <input type="radio"
                             name="params[{{ $plgKey }}]"
                             value="0"
                             class="radio radio-sm"
                             @checked($plgVal == 0) />
                      <span class="text-xs">{{ Lang::txt('COM_RESOURCES_OFF') }}</span>
                    </label>
                  </td>
                  <td>
                    <label class="flex items-center gap-1 cursor-pointer">
                      <input type="radio"
                             name="params[{{ $plgKey }}]"
                             value="1"
                             class="radio radio-sm"
                             @checked($plgVal == 1) />
                      <span class="text-xs">{{ Lang::txt('COM_RESOURCES_ON') }}</span>
                    </label>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
    </x-admin-fieldset>
  @endslot

  {{-- Custom Fields — full width below the two-column grid --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_TYPES_CUSTOM_FIELDS') }}">
      <div class="overflow-x-auto">
        <table class="admin-table text-sm" id="fields" data-href="{{ $elementHref }}">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_RESOURCES_TYPES_REORDER') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TYPES_FIELD') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TYPES_TYPE') }}</th>
              <th>{{ Lang::txt('Display in') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TYPES_REQUIRED') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_TYPES_OPTIONS') }}</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td colspan="6">
                <a class="btn btn-sm btn-primary" id="add-custom-field" href="#addRow">
                  {{ Lang::txt('COM_RESOURCES_NEW_ROW') }}
                </a>
              </td>
            </tr>
          </tfoot>
          <tbody id="field-items">
            @foreach($schema->fields as $i => $field)
              <tr>
                <td class="order">
                  <span class="handle cursor-move" title="{{ Lang::txt('COM_RESOURCES_MOVE_HANDLE') }}">
                    &#8597;
                  </span>
                </td>
                <td>
                  <input type="text"
                         name="fields[{{ $i }}][title]"
                         class="input input-bordered input-sm w-full"
                         maxlength="255"
                         aria-label="{{ Lang::txt('COM_RESOURCES_TYPES_FIELD') }} — {{ $field->label }}"
                         value="{{ $field->label }}" />
                  <input type="hidden"
                         name="fields[{{ $i }}][name]"
                         value="{{ $field->name }}" />
                </td>
                <td>
                  <select name="fields[{{ $i }}][type]"
                          id="fields-{{ $i }}-type"
                          class="select select-bordered select-sm"
                          aria-label="{{ Lang::txt('COM_RESOURCES_TYPES_TYPE') }} — {{ $field->label }}"
                    <optgroup label="{{ Lang::txt('COM_RESOURCES_FIELD_COMMON') }}">
                      <option value="text" @selected($field->type == 'text')>{{ Lang::txt('COM_RESOURCES_TYPES_TEXT') }}</option>
                      <option value="textarea" @selected($field->type == 'textarea')>{{ Lang::txt('COM_RESOURCES_TYPES_TEXTAREA') }}</option>
                      <option value="list" @selected($field->type == 'list')>{{ Lang::txt('COM_RESOURCES_TYPES_LIST') }}</option>
                      <option value="radio" @selected($field->type == 'radio')>{{ Lang::txt('COM_RESOURCES_TYPES_RADIO') }}</option>
                      <option value="checkbox" @selected($field->type == 'checkbox')>{{ Lang::txt('COM_RESOURCES_TYPES_CHECKBOX') }}</option>
                      <option value="hidden" @selected($field->type == 'hidden')>{{ Lang::txt('COM_RESOURCES_TYPES_HIDDEN') }}</option>
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_RESOURCES_FIELD_PREDEFINED') }}">
                      <option value="date" @selected($field->type == 'date')>{{ Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_DATE') }}</option>
                      <option value="geo" @selected($field->type == 'geo')>{{ Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_GEO') }}</option>
                      <option value="languages" @selected($field->type == 'languages')>{{ Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_LANG') }}</option>
                    </optgroup>
                  </select>
                </td>
                <td>
                  <select name="fields[{{ $i }}][display]"
                          id="fields-{{ $i }}-display"
                          class="select select-bordered select-sm"
                          aria-label="{{ Lang::txt('Display in') }} — {{ $field->label }}"
                    @foreach($plugins as $plugin)
                      @if(!in_array($plugin->element, $skipPlugins))
                        <option value="{{ $plugin->element }}"
                                @selected(isset($field->display) && $field->display == $plugin->element)>
                          {{ Lang::txt($plugin->name) }}
                        </option>
                      @endif
                    @endforeach
                  </select>
                </td>
                <td>
                  <input type="checkbox"
                         name="fields[{{ $i }}][required]"
                         class="checkbox checkbox-sm"
                         value="1"
                         aria-label="{{ Lang::txt('COM_RESOURCES_TYPES_REQUIRED') }} — {{ $field->label }}"
                         @checked($field->required) />
                </td>
                <td id="fields-{{ $i }}-options">
                  {!! $elements->getElementOptions($i, $field, 'fields') !!}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </x-admin-fieldset>

  <input type="hidden" name="type[id]" value="{{ $row->id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
