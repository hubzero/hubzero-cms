{{--
  Global Application Configuration — Admin Blade view

  Tab-based form for editing hub-wide configuration (site, system, server,
  API, permissions, filters, plus any extra registered sections).

  Variables from controller:
    $form        — Hubzero\Form\Form instance
    $data        — array of raw config data (all sections)
    $model       — Components\Config\Models\Application

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_CONFIG_GLOBAL_CONFIGURATION'), 'config');
  Toolbar::apply('application.apply');
  Toolbar::save('application.save');
  Toolbar::divider();
  Toolbar::cancel('application.cancel');
  Toolbar::divider();
  Toolbar::help('global_config');

  // Fixed sections rendered via fieldsets; anything else goes into "Others"
  $knownSections = [
      'app', 'site', 'offline', 'meta', 'seo', 'cookie',
      'system', 'debug', 'cache', 'session', 'hub_secret',
      'server', 'locale', 'ftp', 'messagequeue', 'database', 'mail',
      'api', 'permissions', 'filters', 'rate_limit', 'asset_id',
  ];
  $others = [];
  foreach ($data ?? [] as $section => $values) {
      if (in_array($section, $knownSections)) {
          continue;
      }
      if (empty($values) || !is_array($values)) {
          continue;
      }
      $others[$section] = $values;
  }

  // Build tab list (label => id)
  $tabs = [
      Lang::txt('JSITE')                  => 'site',
      Lang::txt('COM_CONFIG_SYSTEM')       => 'system',
      Lang::txt('COM_CONFIG_SERVER')       => 'server',
      Lang::txt('COM_CONFIG_API')          => 'api',
      Lang::txt('COM_CONFIG_PERMISSIONS')  => 'permissions',
      Lang::txt('COM_CONFIG_TEXT_FILTERS') => 'filters',
  ];
  foreach ($others as $key => $values) {
      $tabs[$key] = $key;
  }
  $firstTab = 'site';

  // Render all fields in a named fieldset as stacked cfg-field rows.
  // The form engine produces full <label> and input HTML — output them directly.
  $renderFieldset = function (string $name) use ($form): string {
      $out = '';
      foreach ($form->getFieldset($name) as $field) {
          if ($field->hidden) {
              $out .= $field->input;
          } else {
              $out .= '<div class="cfg-field">'
                    . $field->label
                    . $field->input
                    . '</div>';
          }
      }
      return $out;
  };
@endphp

<form action="{{ Route::url('index.php?option=com_config', false) }}"
      id="application-form"
      name="adminForm"
      method="post"
      autocomplete="off">

  {{-- Tab bar --}}
  <div class="config-tabs">
    @foreach($tabs as $label => $id)
      <a class="config-tab {{ $id === $firstTab ? 'config-tab-active' : '' }}"
         data-tab="{{ $id }}"
         href="#"
         role="button">{{ $label }}</a>
    @endforeach
  </div>

  {{-- Tab panels --}}
  <div class="config-panels">

    {{-- Site tab --}}
    <div class="config-tab-panel" id="tab-site">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SITE_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('site') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_OFFLINE_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('offline') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_METADATA_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('metadata') !!}</div>
          </div>
        </div>
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SEO_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('seo') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_COOKIE_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('cookie') !!}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- System tab --}}
    <div class="config-tab-panel hidden" id="tab-system">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SYSTEM_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('system') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SECRET_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('hub_secret') !!}</div>
          </div>
        </div>
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_DEBUG_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('debug') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_CACHE_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('cache') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SESSION_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('session') !!}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Server tab --}}
    <div class="config-tab-panel hidden" id="tab-server">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_SERVER_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('server') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_LOCATION_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('locale') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_MQ_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('messagequeue') !!}</div>
          </div>
        </div>
        <div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_DATABASE_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('database') !!}</div>
          </div>
          <div class="admin-fieldset">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_MAIL_SETTINGS') }}</h3>
            <div class="admin-fieldset-body">{!! $renderFieldset('mail') !!}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- API tab --}}
    <div class="config-tab-panel hidden" id="tab-api">
      <div class="admin-fieldset">
        <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_API') }}</h3>
        <div class="admin-fieldset-body">{!! $renderFieldset('api') !!}</div>
      </div>
    </div>

    {{-- Permissions tab — Rules field renders its own Blade-aware UI --}}
    <div class="config-tab-panel hidden" id="tab-permissions">
      <div class="admin-fieldset">
        <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_PERMISSION_SETTINGS') }}</h3>
        <div class="admin-fieldset-body p-0">
          @foreach($form->getFieldset('permissions') as $field)
            {!! $field->input !!}
          @endforeach
        </div>
      </div>
    </div>

    {{-- Filters tab --}}
    <div class="config-tab-panel hidden" id="tab-filters">
      <div class="admin-fieldset">
        <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_CONFIG_TEXT_FILTER_SETTINGS') }}</h3>
        <div class="admin-fieldset-body">
          <p class="cfg-filters-note">{!! Lang::txt('COM_CONFIG_TEXT_FILTERS_DESC') !!}</p>
          @foreach($form->getFieldset('filters') as $field)
            {!! $field->input !!}
          @endforeach
        </div>
      </div>
    </div>

    {{-- Dynamic "others" tabs --}}
    @foreach($others as $section => $values)
      <div class="config-tab-panel hidden" id="tab-{{ $section }}">
        <div class="admin-fieldset">
          <h3 class="admin-fieldset-heading">
            {{ Lang::txt('COM_CONFIG_OTHER_SETTINGS', $section) }}
          </h3>
          <div class="admin-fieldset-body">
            @foreach($values as $key => $val)
              @if(is_array($val))
                @foreach($val as $k => $v)
                  @php
                    $fId   = 'hzform_' . $section . '_' . $key . '_' . $k;
                    $fName = 'hzother[' . $section . '][' . $key . '][' . $k . ']';
                  @endphp
                  <div class="cfg-field">
                    <label for="{{ $fId }}">{{ $key }}</label>
                    <input type="text" name="{{ $fName }}" id="{{ $fId }}"
                           value="{{ $v }}" />
                  </div>
                @endforeach
              @else
                @php
                  $fId   = 'hzform_' . $section . '_' . $key;
                  $fName = 'hzother[' . $section . '][' . $key . ']';
                @endphp
                <div class="cfg-field">
                  <label for="{{ $fId }}">{{ $key }}</label>
                  <input type="text" name="{{ $fName }}" id="{{ $fId }}"
                         value="{{ $val }}" />
                </div>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    @endforeach

  </div>{{-- .config-panels --}}

  <input type="hidden" name="task" value="" autocomplete="off" />
  {!! Html::input('token') !!}

</form>

@php
  $__view->js('config-modal.blade.js');
@endphp
