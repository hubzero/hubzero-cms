{{--
  System Information — tabbed view

  Variables from controller:
    $info         — array  (php, dbversion, dbcollation, phpversion, server,
                            sapi_name, version, platform, useragent)
    $php_settings — array  (safe_mode, open_basedir, display_errors, ...)
    $php_info     — string (phpinfo() HTML output)
    $config       — array  (configuration key => value pairs)
    $directory    — array  (dir => ['message' => ..., 'writable' => bool])

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_SYSTEM_INFO'), 'systeminfo');
  Toolbar::help('sysinfo');

  // Make Html::phpsetting() and Html::directory() available
  Html::addIncludePath(dirname(PATH_COMPONENT) . '/helpers/html');

  $activeTab = Request::getCmd('tab', 'system');
  $tabs = [
    'system'      => Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'),
    'phpsettings' => Lang::txt('COM_SYSTEM_INFO_RELEVANT_PHP_SETTINGS'),
    'config'      => Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'),
    'directory'   => Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'),
    'phpinfo'     => Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'),
  ];
@endphp

{{-- Tabs --}}
<div role="tablist" class="tabs tabs-lifted mb-6">
  @foreach($tabs as $tabKey => $tabLabel)
    <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&tab=' . $tabKey, false) }}"
       role="tab"
       class="tab @if($activeTab === $tabKey) tab-active @endif">
      {{ $tabLabel }}
    </a>
  @endforeach
</div>

{{-- Tab panels --}}
<div>

  {{-- System Information --}}
  @if($activeTab === 'system')
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION') }}</legend>
      <table class="table table-sm w-full">
        <thead>
          <tr>
            <th class="text-left w-1/3">{{ Lang::txt('COM_SYSTEM_INFO_SETTING') }}</th>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_INFO_VALUE') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_PHP_BUILT_ON') }}</th>
            <td>{{ $info['php'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_DATABASE_VERSION') }}</th>
            <td>{{ $info['dbversion'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_DATABASE_COLLATION') }}</th>
            <td>{{ $info['dbcollation'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_PHP_VERSION') }}</th>
            <td>{{ $info['phpversion'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_WEB_SERVER') }}</th>
            <td>{!! Html::system('server', $info['server']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_WEBSERVER_TO_PHP_INTERFACE') }}</th>
            <td>{{ $info['sapi_name'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_CMS_VERSION') }}</th>
            <td>{{ $info['version'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_PLATFORM_VERSION') }}</th>
            <td>{{ $info['platform'] }}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_USER_AGENT') }}</th>
            <td>{{ $info['useragent'] }}</td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  @endif

  {{-- PHP Settings --}}
  @if($activeTab === 'phpsettings')
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_INFO_RELEVANT_PHP_SETTINGS') }}</legend>
      <table class="table table-sm w-full">
        <thead>
          <tr>
            <th class="text-left w-1/3">{{ Lang::txt('COM_SYSTEM_INFO_SETTING') }}</th>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_INFO_VALUE') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_SAFE_MODE') }}</th>
            <td>{!! Html::phpsetting('boolean', $php_settings['safe_mode']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_OPEN_BASEDIR') }}</th>
            <td>{!! Html::phpsetting('string', $php_settings['open_basedir']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_DISPLAY_ERRORS') }}</th>
            <td>{!! Html::phpsetting('boolean', $php_settings['display_errors']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_SHORT_OPEN_TAGS') }}</th>
            <td>{!! Html::phpsetting('boolean', $php_settings['short_open_tag']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_FILE_UPLOADS') }}</th>
            <td>{!! Html::phpsetting('boolean', $php_settings['file_uploads']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_OUTPUT_BUFFERING') }}</th>
            <td>{!! Html::phpsetting('boolean', $php_settings['output_buffering']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_SESSION_SAVE_PATH') }}</th>
            <td>{!! Html::phpsetting('string', $php_settings['session.save_path']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_SESSION_AUTO_START') }}</th>
            <td>{!! Html::phpsetting('integer', $php_settings['session.auto_start']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_XML_ENABLED') }}</th>
            <td>{!! Html::phpsetting('set', $php_settings['xml']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_ZLIB_ENABLED') }}</th>
            <td>{!! Html::phpsetting('set', $php_settings['zlib']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_ZIP_ENABLED') }}</th>
            <td>{!! Html::phpsetting('set', $php_settings['zip']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_DISABLED_FUNCTIONS') }}</th>
            <td>{!! Html::phpsetting('string', $php_settings['disable_functions']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_MBSTRING_ENABLED') }}</th>
            <td>{!! Html::phpsetting('set', $php_settings['mbstring']) !!}</td>
          </tr>
          <tr>
            <th class="text-left font-normal">{{ Lang::txt('COM_SYSTEM_INFO_ICONV_AVAILABLE') }}</th>
            <td>{!! Html::phpsetting('set', $php_settings['iconv']) !!}</td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  @endif

  {{-- Configuration File --}}
  @if($activeTab === 'config')
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE') }}</legend>
      <table class="table table-sm w-full">
        <thead>
          <tr>
            <th class="text-left w-1/3">{{ Lang::txt('COM_SYSTEM_INFO_SETTING') }}</th>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_INFO_VALUE') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($config as $key => $value)
            <tr>
              <td>{{ $key }}</td>
              <td>
                @if(is_array($value))
                  @foreach($value as $ky => $val)
                    @if(is_array($val))
                      @foreach($val as $k => $v)
                        {!! e($k) . ' = ' . e($v) !!}<br />
                      @endforeach
                    @else
                      {!! e($ky) . ' = ' . e($val) !!}<br />
                    @endif
                  @endforeach
                @else
                  {{ $value }}
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </fieldset>
  @endif

  {{-- Directory Permissions --}}
  @if($activeTab === 'directory')
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS') }}</legend>
      <table class="table table-sm w-full">
        <thead>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_INFO_DIRECTORY') }}</th>
            <th class="text-left w-32">{{ Lang::txt('COM_SYSTEM_INFO_STATUS') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($directory as $dir => $info)
            <tr>
              <td>{!! Html::directory('message', $dir, $info['message']) !!}</td>
              <td>{!! Html::directory('writable', $info['writable']) !!}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </fieldset>
  @endif

  {{-- PHP Information --}}
  @if($activeTab === 'phpinfo')
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION') }}</legend>
      {!! $php_info !!}
    </fieldset>
  @endif

</div>
