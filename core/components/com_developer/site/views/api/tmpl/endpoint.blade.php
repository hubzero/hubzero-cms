{{--
  API endpoint documentation page — details for a specific API section.

  Variables from controller:
    $documentation  — Array from API Doc Generator
    $active         — Active section name
    $tokens         — Collection of user's active access tokens

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$host = $_SERVER['HTTP_HOST'];
$url = 'https://' . $host;

$available = [];
$endpoints = [];
foreach ($documentation['sections'][$active] as &$endpoint) {
    $version = str_replace('_', '.', $endpoint['_metadata']['version']);
    $version = number_format((float) $version, 1);
    $endpoint['_metadata']['version'] = $version;
    $available[] = $version;
    if (!isset($endpoints[$version])) {
        $endpoints[$version] = [];
    }
}

foreach (array_keys($endpoints) as $version) {
    foreach ($documentation['sections'][$active] as $endpoint) {
        if ($endpoint['_metadata']['version'] > $version) {
            continue;
        }
        $controller = $endpoint['_metadata']['controller'] ?? '';
        $key = $controller . $endpoint['_metadata']['method'];
        if (
            !isset($endpoints[$version][$key])
            || $endpoint['_metadata']['version'] > $endpoints[$version][$key]['_metadata']['version']
        ) {
            $endpoints[$version][$key] = $endpoint;
        }
    }
}

$token = '';
if (!empty($tokens)) {
    $token = $tokens->first()->access_token;
}

$versions = array_unique($available);
asort($versions);
$versions = array_reverse($versions);

$activeVersion = \Hubzero\Facades\Request::getString('version', reset($versions));
$activeVersion = str_replace('_', '.', $activeVersion);
$activeVersion = number_format((float) $activeVersion, 1);

$baseRoute = 'index.php?option=com_developer&controller=api';
$skipKeys = ['name', 'method', 'description', 'replaces', 'deprecated', 'uri', 'parameters', 'return', '_metadata'];

$activeEndpoints = $endpoints[$activeVersion] ?? [];
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_DOCS') . ': ' . Lang::txt('COM_DEVELOPER_API_ENDPOINT')">
  @slot('actions')
    <a class="btn btn-ghost btn-sm"
       href="{{ Route::url('index.php?option=com_developer&controller=api') }}">
      {{ Lang::txt('COM_DEVELOPER_API_HOME') }}
    </a>
  @endslot

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
    {{-- Left sidebar nav --}}
    <aside class="lg:sticky lg:top-4 lg:self-start lg:max-h-[calc(100vh-2rem)] lg:overflow-y-auto">
      {!! $__view->view('_menu')
            ->set('documentation', $documentation)
            ->set('active', $active)
            ->set('version', $activeVersion)
            ->loadTemplate() !!}
    </aside>

    {{-- Main content --}}
    <div class="min-w-0">
      {{-- Active tokens --}}
      @if (!empty($tokens) && count($tokens) > 0)
        {!! $__view->view('_active_tokens')
              ->set('tokens', $tokens)
              ->loadTemplate() !!}
      @endif

      {{-- Section header with version dropdown --}}
      <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold" id="{{ $active }}">
          {{ ucfirst($active) }}
        </h2>
        @if (!empty($versions))
          <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-sm btn-outline">
              {{ $activeVersion }}
              <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
              </svg>
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg z-10 w-32 p-2">
              @foreach ($versions as $version)
                <li>
                  <a href="{{ Route::url($baseRoute . '&task=endpoint&active=' . $active . '&version=' . $version) }}">
                    {{ $version }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

      {{-- Endpoints --}}
      @foreach ($activeEndpoints as $endpoint)
    @php
    $key = $endpoint['_metadata']['component'] . '-' . $endpoint['_metadata']['method'];
    if ($endpoint['_metadata']['version'] > $activeVersion) {
        continue;
    }
    $inherited = '';
    if ($endpoint['_metadata']['version'] < $activeVersion) {
        $inherited = ' ' . Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_INHERITED', $endpoint['_metadata']['version']);
    }
    $httpMethod = strtoupper($endpoint['method']);
    $methodClasses = [
        'GET' => 'badge-success',
        'POST' => 'badge-info',
        'PUT' => 'badge-warning',
        'DELETE' => 'badge-error',
    ];
    $badgeCls = $methodClasses[$httpMethod] ?? 'badge-neutral';

    if (strpos($endpoint['uri'], '/api/') === 0) {
        $fullEndpoint = $url . $endpoint['uri'];
    } else {
        $fullEndpoint = $url . '/api/v' . $activeVersion . $endpoint['uri'];
    }
    @endphp
    <div class="collapse collapse-arrow bg-base-100 border border-base-300 mb-3" id="{{ $key }}">
      <input type="checkbox" />
      <div class="collapse-title font-medium flex items-center gap-2">
        <span class="badge {{ $badgeCls }} badge-sm font-mono">{{ $httpMethod }}</span>
        {{ $endpoint['name'] }}{{ $inherited }}
      </div>
      <div class="collapse-content">
        @if ($endpoint['description'])
          <p class="mb-3">{{ $endpoint['description'] }}</p>
        @endif

        @if ($endpoint['method'] && $endpoint['uri'])
          <pre class="bg-base-200 rounded-lg p-3 text-sm mb-3"><code>{{ $endpoint['method'] }}
{{ $endpoint['uri'] }}</code></pre>
        @endif

        @if (!empty($endpoint['replaces']))
          <div class="alert alert-info mb-3">
            {{ Lang::txt('COM_DEVELOPER_API_DOC_REPLACES', $endpoint['replaces']) }}
          </div>
        @endif

        @if (!empty($endpoint['deprecated']))
          <div class="alert alert-warning mb-3">
            {{ Lang::txt('COM_DEVELOPER_API_DOC_DEPRECATED', $endpoint['deprecated']) }}
          </div>
        @endif

        @foreach ($endpoint as $k => $v)
          @if (!in_array($k, $skipKeys))
            <p><strong>{{ $k }}:</strong> {{ $v }}</p>
          @endif
        @endforeach

        @if (count($endpoint['parameters']) > 0)
          <div class="overflow-x-auto mt-3">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>{{ Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_NAME') }}</th>
                  <th>{{ Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_TYPE') }}</th>
                  <th>{{ Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DESC') }}</th>
                  <th>{{ Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DEFAULT') }}</th>
                  <th>{{ Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_ACCEPTED_VALUES') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($endpoint['parameters'] as $param)
                  <tr>
                    <td>{{ $param['name'] ?? '' }}</td>
                    <td class="text-base-content/60 text-sm">{{ $param['type'] ?? '' }}</td>
                    <td>
                      @if (!empty($param['required']))
                        <span class="badge badge-error badge-xs mr-1">{{ Lang::txt('JREQUIRED') }}</span>
                      @endif
                      {{ $param['description'] ?? '' }}
                    </td>
                    <td><code class="text-sm">{{ $param['default'] ?? 'null' }}</code></td>
                    <td>
                      @if (isset($param['allowedValues']))
                        <code class="text-sm">{{ $param['allowedValues'] }}</code>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        {{-- Try It Out --}}
        <div class="try-it-out mt-4"
             data-endpoint="{{ e($fullEndpoint) }}"
             data-method="{{ $httpMethod }}"
             data-token="{{ $token }}">
          <div class="flex items-center justify-between">
            <h4 class="font-semibold">Try it out</h4>
            <button class="try-it-btn btn btn-sm btn-outline">Try it out</button>
          </div>
          <div class="try-it-form hidden mt-3">
            @if (count($endpoint['parameters']) > 0)
              @foreach ($endpoint['parameters'] as $param)
                @if (isset($param['name']))
                  @php
                  $isPathParam = strpos($endpoint['uri'], '{' . $param['name'] . '}') !== false;
                  $paramId = 'param-' . $key . '-' . $param['name'];
                  $paramType = $param['type'] ?? 'string';
                  $defaultVal = $param['default'] ?? '';
                  $placeholder = $isPathParam
                      ? 'Replaces {' . $param['name'] . '} in URL'
                      : $defaultVal;
                  @endphp
                  <div class="form-control mb-2">
                    <label class="label" for="{{ $paramId }}">
                      {{ $param['name'] }}
                      @if (!empty($param['required']))
                        <span class="text-error">*</span>
                      @endif
                      @if ($isPathParam)
                        <span class="badge badge-xs badge-outline ml-1">path</span>
                      @endif
                      <span class="text-base-content/60 text-xs">({{ $paramType }})</span>
                    </label>
                    @if (isset($param['type']) && $param['type'] === 'text')
                      <textarea id="{{ $paramId }}"
                                name="{{ $param['name'] }}"
                                class="textarea textarea-bordered textarea-sm"
                                placeholder="{{ $defaultVal }}"
                                rows="3"></textarea>
                    @else
                      <input type="text"
                             id="{{ $paramId }}"
                             name="{{ $param['name'] }}"
                             class="input input-bordered input-sm"
                             placeholder="{{ $placeholder }}" />
                    @endif
                  </div>
                @endif
              @endforeach
            @endif
            <button class="try-it-btn execute btn btn-sm btn-primary mt-2">Execute</button>
            <div class="response-section hidden mt-3"></div>
          </div>
        </div>
      </div>
    </div>
      @endforeach
    </div>
  </div>

</x-page-container>
