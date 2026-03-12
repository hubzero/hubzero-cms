{{--
  System Cache — OPcache scripts list

  Variables from controller:
    $opcache — array|null  (opcache_get_status() data)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Utility\Number;

  Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_OPCACHE'), 'config');
  if ($opcache) {
      Toolbar::custom('resetopcache', 'refresh', '', Lang::txt('COM_SYSTEM_CACHE_RESET_OPCACHE'), false);
  }

  $task    = Request::getCmd('task', '');
  $baseUrl = 'index.php?option=' . $option . '&controller=' . $controller;
@endphp

{{-- Sub-navigation --}}
<div role="tablist" class="tabs tabs-lifted mb-6">
  <a href="{{ Route::url($baseUrl, false) }}"
     role="tab"
     class="tab">
    {{ Lang::txt('COM_SYSTEM_CACHE_OVERVIEW') }}
  </a>
  <a href="{{ Route::url($baseUrl . '&task=opcache', false) }}"
     role="tab"
     class="tab tab-active">
    {{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE') }}
  </a>
  <a href="{{ Route::url($baseUrl . '&task=apcu', false) }}"
     role="tab"
     class="tab">
    {{ Lang::txt('COM_SYSTEM_CACHE_APCU') }}
  </a>
</div>

@if(!$opcache)
  <div role="alert" class="alert alert-warning">
    <span>{{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE_NOT_AVAILABLE') }}</span>
  </div>
@else
  @php
    $scripts = $opcache['scripts'] ?? [];
    usort($scripts, fn($a, $b) => $b['hits'] - $a['hits']);
  @endphp

  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        name="adminForm"
        id="adminForm">

    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">
        {{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE') }}
        ({{ number_format(count($scripts)) }} scripts)
      </legend>

      <div class="overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Script</th>
              <th class="priority-3">{{ Lang::txt('COM_SYSTEM_CACHE_HITS') }}</th>
              <th class="priority-3">{{ Lang::txt('COM_SYSTEM_CACHE_MEMORY') }}</th>
              <th class="priority-4">Last Used</th>
            </tr>
          </thead>
          <tbody>
            @forelse($scripts as $script)
              <tr>
                <td>
                  <span class="text-xs font-mono break-all">
                    {{ $script['full_path'] }}
                  </span>
                </td>
                <td class="priority-3">{{ number_format($script['hits']) }}</td>
                <td class="priority-3">{{ Number::formatBytes($script['memory_consumption']) }}</td>
                <td class="priority-4">{{ date('Y-m-d H:i:s', $script['last_used_timestamp']) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-6 text-muted-foreground">
                  No cached scripts.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </fieldset>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="opcache" />
  </form>
@endif
