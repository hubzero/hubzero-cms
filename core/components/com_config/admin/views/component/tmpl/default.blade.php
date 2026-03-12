{{--
  Component Configuration — Admin Blade view

  Renders the component's config.xml fieldsets in a tabbed form
  inside the modal popup (tmpl=component shell).

  Variables from controller:
    $form      — Hubzero\Form\Form instance
    $component — component object with ->option, ->id, ->params
    $model     — Components\Config\Models\Component

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Access\Access;

  $componentOption = $component->option ?? '';
  $componentId     = $component->id ?? 0;
  $configTitle     = Lang::txt($componentOption . '_configuration');
  $refreshFlag     = Request::getBool('refresh', 0);
  $componentPath   = $model->get('component.path');

  // Get fieldsets for tabs
  $fieldSets = $form ? $form->getFieldsets() : [];
  $fieldSetNames = array_keys($fieldSets);
  $activeTab = $fieldSetNames[0] ?? '';
@endphp

<form action="{{ Route::url('index.php?option=com_config', false) }}"
      id="component-form"
      name="adminForm"
      method="post"
      autocomplete="off"
      class="config-modal">

  {{-- Header bar --}}
  <div class="config-header">
    <h2 class="config-header-title">
      {{ $configTitle }}
    </h2>
    <div class="config-header-actions">
      <button type="button" id="btn-apply" class="config-btn config-btn-primary">
        {{ Lang::txt('JAPPLY') }}
      </button>
      <button type="button" id="btn-save" class="config-btn config-btn-default">
        Save &amp; Close
      </button>
      <button type="button" id="btn-cancel"
              class="config-btn config-btn-ghost"
              @if($refreshFlag) data-refresh="1" @endif>
        {{ Lang::txt('JCANCEL') }}
      </button>
    </div>
  </div>

  @if($form)
    {{-- Tabs --}}
    <div class="config-tabs">
      @foreach($fieldSets as $name => $fieldSet)
        @php
          $label = empty($fieldSet->label)
              ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL'
              : $fieldSet->label;
        @endphp
        <a class="config-tab {{ $name === $activeTab ? 'config-tab-active' : '' }}"
           data-tab="{{ $name }}"
           href="#"
           role="button">
          {{ Lang::txt($label) }}
        </a>
      @endforeach
    </div>

    {{-- Flash messages --}}
    @php
      $configMessages = [];
      if (app()->bound('notification')) {
          foreach (app('notification')->messages() as $msg) {
              $configMessages[] = ['type' => $msg['type'] ?? 'info', 'text' => $msg['message'] ?? ''];
          }
      }
    @endphp
    @if(count($configMessages))
      <div class="config-messages">
        @foreach($configMessages as $msg)
          <div class="config-message config-message-{{ $msg['type'] }}">
            {{ $msg['text'] }}
          </div>
        @endforeach
      </div>
    @endif

    {{-- Tab panels --}}
    <div class="config-panels">
      @foreach($fieldSets as $name => $fieldSet)
        <div class="config-tab-panel {{ $name !== $activeTab ? 'hidden' : '' }}"
             id="tab-{{ $name }}">

          @if(!empty($fieldSet->description))
            <p class="config-tab-desc">
              {{ Lang::txt($fieldSet->description) }}
            </p>
          @endif

          @if($name === 'permissions')
            {{-- Custom permissions UI --}}
            @php
              $comPath = App::get('component')->path($componentOption) . '/config/access.xml';
              $permActions = Access::getActionsFromFile($comPath, "/access/section[@name='component']/");
              if (!$permActions) {
                  $permActions = [];
              }

              $permDb = App::get('db');
              $permQuery = $permDb->getQuery()
                  ->select('id')
                  ->from('#__assets')
                  ->whereEquals('name', $componentOption);
              $permDb->setQuery($permQuery->toString());
              $permAssetId = (int) $permDb->loadResult();

              $permAssetRules = Access::getAssetRules($permAssetId);

              $permQuery = $permDb->getQuery()
                  ->select('a.id', 'value')
                  ->select('a.title', 'text')
                  ->select('COUNT(DISTINCT b.id)', 'level')
                  ->select('a.parent_id')
                  ->from('#__usergroups', 'a')
                  ->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
                  ->group('a.id')
                  ->group('a.title')
                  ->group('a.lft')
                  ->group('a.rgt')
                  ->group('a.parent_id')
                  ->order('a.lft', 'ASC');
              $permDb->setQuery($permQuery->toString());
              $permGroups = $permDb->loadObjectList();
            @endphp

            <p class="config-tab-desc">
              {{ Lang::txt('JLIB_RULES_SETTINGS_DESC') }}
            </p>

            <div class="config-permissions">
              @foreach($permGroups as $permGroup)
                @php
                  $indent = str_repeat('— ', $permGroup->level);
                  $canCalculate = ($permGroup->parent_id || !empty($componentOption));
                @endphp
                <details class="config-perm-group">
                  <summary class="config-perm-summary">
                    @if($permGroup->level > 0)
                      <span class="config-perm-indent">{!! $indent !!}</span>
                    @endif
                    {{ $permGroup->text }}
                  </summary>
                  <div class="config-perm-body">
                    <table class="config-perm-table">
                      <thead>
                        <tr>
                          <th>{{ Lang::txt('JLIB_RULES_ACTION') }}</th>
                          <th>{{ Lang::txt('JLIB_RULES_SELECT_SETTING') }}</th>
                          @if($canCalculate)
                            <th>{{ Lang::txt('JLIB_RULES_CALCULATED_SETTING') }}</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($permActions as $permAction)
                          @php
                            $assetRule     = $permAssetRules->allow($permAction->name, $permGroup->value);
                            $inheritedRule = Access::checkGroup($permGroup->value, $permAction->name, $permAssetId ?: $componentOption);
                            $selectName    = 'hzform[rules][' . $permAction->name . '][' . $permGroup->value . ']';
                            $selectId      = 'rules_' . $permAction->name . '_' . $permGroup->value;
                            $inheritLabel  = (empty($permGroup->parent_id) && empty($componentOption))
                                ? Lang::txt('JLIB_RULES_NOT_SET')
                                : Lang::txt('JLIB_RULES_INHERITED');
                          @endphp
                          <tr>
                            <td title="{{ Lang::txt($permAction->description) }}">
                              {{ Lang::txt($permAction->title) }}
                            </td>
                            <td>
                              <select name="{{ $selectName }}"
                                      id="{{ $selectId }}"
                                      class="config-perm-select">
                                <option value=""  {{ $assetRule === null  ? 'selected' : '' }}>{{ $inheritLabel }}</option>
                                <option value="1" {{ $assetRule === true  ? 'selected' : '' }}>{{ Lang::txt('JLIB_RULES_ALLOWED') }}</option>
                                <option value="0" {{ $assetRule === false ? 'selected' : '' }}>{{ Lang::txt('JLIB_RULES_DENIED') }}</option>
                              </select>
                            </td>
                            @if($canCalculate)
                              <td>
                                @php
                                  $isAdmin = Access::checkGroup($permGroup->value, 'core.admin', $permAssetId ?: $componentOption);
                                @endphp
                                @if($isAdmin === true && !empty($componentOption))
                                  <span class="config-perm-badge config-perm-badge-success">
                                    {{ Lang::txt('JLIB_RULES_ALLOWED_ADMIN') }}
                                  </span>
                                @elseif($inheritedRule === null)
                                  <span class="config-perm-badge config-perm-badge-neutral">
                                    {{ Lang::txt('JLIB_RULES_NOT_ALLOWED') }}
                                  </span>
                                @elseif($inheritedRule === true)
                                  <span class="config-perm-badge config-perm-badge-success">
                                    {{ Lang::txt('JLIB_RULES_ALLOWED') }}
                                  </span>
                                @elseif($inheritedRule === false)
                                  <span class="config-perm-badge config-perm-badge-error">
                                    {{ $assetRule === false
                                        ? Lang::txt('JLIB_RULES_NOT_ALLOWED')
                                        : Lang::txt('JLIB_RULES_NOT_ALLOWED_LOCKED') }}
                                  </span>
                                @endif
                                @if($assetRule === true && $inheritedRule === false)
                                  <span class="config-perm-conflict">{{ Lang::txt('JLIB_RULES_CONFLICT') }}</span>
                                @endif
                              </td>
                            @endif
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </details>
              @endforeach
            </div>

            <p class="config-perm-notes">
              {{ Lang::txt('JLIB_RULES_SETTING_NOTES') }}
            </p>
          @else
            {{-- Standard field rendering --}}
            <div class="config-fields">
              @foreach($form->getFieldset($name) as $field)
                @if($field->hidden)
                  {!! $field->input !!}
                @else
                  <div class="config-field">
                    <label class="config-field-label" for="{{ $field->id }}">
                      {!! strip_tags($field->label, '<span>') !!}
                    </label>
                    <div class="config-field-input">
                      {!! $field->input !!}
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @else
    <div class="p-4">
      <div class="alert alert-warning">
        {{ Lang::txt('COM_CONFIG_ERROR_COMPONENT_CONFIG_NOT_FOUND', $componentOption) }}
      </div>
    </div>
  @endif

  <input type="hidden" name="id" value="{{ $componentId }}" />
  <input type="hidden" name="component" value="{{ $componentOption }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="path" value="{{ $componentPath }}" />
  {!! Html::input('token') !!}
</form>

@php
  $__view->js('config-modal.blade.js');
@endphp
