{{--
  Member Dashboard — individual module card wrapper.

  Variables (set by parent view):
    $module — module object with positioning data, params, title, etc.
    $admin  — boolean, admin context

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Module as ModuleFacade;

  $params = new \Hubzero\Config\Registry($module->params);

  $manifest = PATH_APP . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml';
  if (!file_exists($manifest)) {
      $manifest = PATH_CORE . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml';
  }
  $fields = new \Hubzero\Form\Form($module->module);
  $fields->loadFile($manifest, true, 'config/fields');

  $renderedModule = ModuleFacade::render($module, ['style' => 'none']);

  $settingsHtml = trim(
      $__view->view('parameters')
          ->set('admin', $admin)
          ->set('module', $module)
          ->set('params', $params->toArray())
          ->set('fields', $fields->getFieldset('basic'))
          ->loadTemplate()
  );

  $moduleClass = 'module '
      . strtolower($module->module) . ' '
      . $params->get('moduleclass_sfx')
      . ' draggable sortable';
@endphp

<div class="{{ $moduleClass }}"
     data-row="{{ $module->positioning->row }}"
     data-col="{{ $module->positioning->col }}"
     data-sizex="{{ $module->positioning->size_x }}"
     data-sizey="{{ $module->positioning->size_y }}"
     data-moduleid="{{ $module->id }}">

  <div class="inner">
    <div class="module-title">
      <h3>{{ e($module->title) }}</h3>
      <ul class="module-links">
        @if ($settingsHtml != '')
          <li>
            <a class="settings"
               title="{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS') }}"
               href="javascript:void(0);">
              <span>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS') }}</span>
            </a>
          </li>
        @endif
        <li>
          <a class="remove"
             title="{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_REMOVE') }}"
             href="javascript:void(0);">
            <span>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_REMOVE') }}</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="module-main">
      {!! $settingsHtml !!}
      <div class="module-content">
        @if ($admin)
          <div class="custom">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_ADMIN_CONTENT') }}</div>
        @elseif ($module->module == 'mod_custom')
          <div class="custom">{!! $module->content !!}</div>
        @else
          @php $module->user = false; @endphp
          {!! $renderedModule !!}
        @endif
      </div>
    </div>
  </div>
</div>
