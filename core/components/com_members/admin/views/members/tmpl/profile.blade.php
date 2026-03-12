{{--
  Profile field builder

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');

  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_PROFILE'), 'user');
  if ($canDo->get('core.edit') || $canDo->get('core.create')) {
      Toolbar::apply('applyprofile');
      Toolbar::save('saveprofile');
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::divider();
  Toolbar::help('profile');

  // Build field data for the formbuilder JS
  $elements = array();

  foreach ($fields as $field) {
      $element = new stdClass();
      $element->label      = (string)$field->get('label');
      $element->name       = (string)$field->get('name');
      $element->field_type = (string)$field->get('type');
      if ($element->field_type == 'select') {
          $element->field_type = 'dropdown';
      }
      if ($element->field_type == 'textarea' || $element->field_type == 'editor') {
          $element->field_type = 'paragraph';
      }
      $element->create   = (int)$field->get('action_create');
      $element->update   = (int)$field->get('action_update');
      $element->edit     = (int)$field->get('action_edit');
      $element->browse   = (int)$field->get('action_browse');
      $element->access   = (int)$field->get('access');
      $element->field_id = (int)$field->get('id');

      $element->field_options = new stdClass();
      $element->field_options->description          = (string)$field->get('description');
      $element->field_options->placeholder          = (string)$field->get('placeholder');
      $element->field_options->include_other_option = (bool)$field->get('option_other');
      $element->field_options->include_blank_option = (bool)$field->get('option_blank');
      $element->field_options->min   = (int)$field->get('min');
      $element->field_options->max   = (int)$field->get('max');
      $element->field_options->value = (string)$field->get('default_value');

      $options = $field->options;

      if ($options->count()) {
          $element->field_options->options = array();
          foreach ($options as $opt) {
              $o = new stdClass();
              $o->field_id = (int)$opt->get('id');
              $o->label    = (string)$opt->get('label');
              $o->value    = (string)$opt->get('value', $opt->get('label'));
              $o->checked  = (bool)$opt->get('checked');
              $dependents = $opt->get('dependents', '[]');
              $dependents = $dependents ? $dependents : '[]';
              $dependents = json_decode($dependents);
              $o->dependents = implode(', ', $dependents);
              $element->field_options->options[] = $o;
          }
      }

      $elements[] = $element;
  }

  $json = new stdClass();
  $json->fields = $elements;
  $json = json_encode($json);

  // Build access levels for JS
  $levels = array();
  foreach (Html::access('assetgroups') as $level) {
      $levels[] = '{"value":' . $level->value . ',"text":"' . e($level->text) . '"}';
  }
  $accessLevelsJson = '[' . implode(',', $levels) . ']';

  $__view->css('formbuilder.css')
       ->js('vendor.js')
       ->js('formbuilder.blade.js')
       ->js('profile-builder.blade.js');
@endphp

@php
  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
@endphp

<template id="profile-builder-data">{{ json_encode($elements) }}</template>
<template id="profile-builder-accesses">{{ $accessLevelsJson }}</template>

<form action="{!! $formAction !!}"
      method="post"
      name="adminForm"
      id="item-form">
  <div class="fb-main">{{ Lang::txt('COM_MEMBERS_PROFILE') }}</div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="saveprofile" />
  <input type="hidden" name="profile" id="profile-schema" value="{{ $json }}" />

  {!! Html::input('token') !!}
</form>
