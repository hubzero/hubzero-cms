{{--
  Member Dashboard — Add Modules dialog content.

  Loaded via AJAX into the <dialog> element. Renders
  a two-pane category list + module details view.

  Variables (set by addAction):
    $modules   — array of all available module objects
    $mymodules — array of module IDs already installed

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<ul class="module-list-triggers">
  @foreach ($modules as $module)
    @php
      $cls = in_array($module->id, $mymodules) ? 'installed' : '';
    @endphp
    <li class="{{ $cls }}">
      <a href="javascript:void(0);" data-module="{{ $module->id }}">
        {{ $module->title }}
      </a>
    </li>
  @endforeach
</ul>

<ul class="module-list-content">
  @foreach ($modules as $module)
    <li class="{{ $module->id }}">
      <div class="module-title-bar">
        <h3>{{ $module->title }}</h3>

        @if (in_array($module->id, $mymodules))
          <button class="btn btn-sm btn-ghost" disabled>
            {{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALLED') }}
          </button>
        @else
          <button class="btn btn-sm btn-primary install-module"
                  data-module="{{ $module->id }}">
            {{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALL') }}
          </button>
        @endif
      </div>

      @php
        $xml = null;
        $appManifest = PATH_APP . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml';
        $coreManifest = PATH_CORE . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml';

        if (file_exists($appManifest)) {
            $xml = simplexml_load_file($appManifest);
        } elseif (file_exists($coreManifest)) {
            $xml = simplexml_load_file($coreManifest);
        }
      @endphp

      @if ($xml)
        <dl class="module-details">
          @if (isset($xml->attributes()->version))
            <dt>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_VERSION') }}</dt>
            <dd>{{ $xml->attributes()->version }}</dd>
          @endif

          @if ($xml->description && $xml->description != 'MOD_CUSTOM_XML_DESCRIPTION')
            @php
              $desc = (string) $xml->description;
              if (!strstr($desc, ' ')) {
                  Lang::load($module->module, PATH_APP . DS . 'modules' . DS . $module->module)
                  || Lang::load($module->module, PATH_CORE . DS . 'modules' . DS . $module->module);
                  $desc = Lang::txt($desc);
              }
            @endphp
            <dt>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_DESCRIPTION') }}</dt>
            <dd>{{ $desc }}</dd>
          @endif

          @if (isset($xml->images) && isset($xml->images->image) && !empty($xml->images->image))
            <dt>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_SCREENSHOTS') }}</dt>
            <dd>
              @foreach ($xml->images->image as $image)
                <img src="{{ $image }}" alt="" />
              @endforeach
            </dd>
          @endif
        </dl>
      @endif
    </li>
  @endforeach
</ul>
