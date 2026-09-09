{{--
  Member Dashboard — module settings form.

  Variables (set by parent view):
    $module — module object
    $admin  — boolean, admin context
    $params — module params as array
    $fields — form fieldset fields

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $count = 0;
  foreach ($fields as $field) {
      if ($field->getAttribute('member_dashboard', 0) == 1) {
          $count++;
      }
  }

  if ($count < 1 || $admin) {
      return;
  }
@endphp

<div class="module-settings hidden">
  <h4>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULES_SETTINGS', e($module->title)) }}</h4>
  <form action="{{ Route::url('index.php?option=' . Request::getCmd('option', 'com_members')) }}"
        method="post">
    @foreach ($fields as $field)
      @php
        if (strtolower($field->type) == 'spacer') {
            continue;
        }
        if (!$field->getAttribute('member_dashboard', 0)) {
            continue;
        }
        $name = trim(str_replace('params[', '', rtrim($field->name, ']')));
        if (isset($params[$name])) {
            $field->setValue($params[$name]);
        }
      @endphp

      <label>
        <span class="tooltips" title="{{ Lang::txt($field->description) }}">
          {{ $field->title }}:
        </span>
        {!! $field->input !!}
      </label>
    @endforeach

    {!! Html::input('token') !!}

    <div class="form-controls">
      <button class="btn btn-success btn-sm save" type="submit">
        {{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS_SAVE') }}
      </button>
      <button class="btn btn-ghost btn-sm cancel" type="button">
        {{ Lang::txt('JCANCEL') }}
      </button>
    </div>
  </form>
</div>
