{{--
  System Cache — Overview (OPcache + APCu stats)

  Variables from controller:
    $opcache       — array|null  (opcache_get_status() data)
    $opcacheConfig — array|null  (opcache_get_configuration() data)
    $apcu          — array|null  (apcu_cache_info() data)
    $apcuMem       — array|null  (apcu_sma_info() data)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Utility\Number;

  Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_OVERVIEW'), 'config');

  // Sub-nav tabs
  $task    = Request::getCmd('task', '');
  $baseUrl = 'index.php?option=' . $option . '&controller=' . $controller;
@endphp

{{-- Sub-navigation --}}
<div role="tablist" class="tabs tabs-lifted mb-6">
  <a href="{{ Route::url($baseUrl, false) }}"
     role="tab"
     class="tab @if($task === '' || $task === 'display') tab-active @endif">
    {{ Lang::txt('COM_SYSTEM_CACHE_OVERVIEW') }}
  </a>
  <a href="{{ Route::url($baseUrl . '&task=opcache', false) }}"
     role="tab"
     class="tab @if($task === 'opcache') tab-active @endif">
    {{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE') }}
  </a>
  <a href="{{ Route::url($baseUrl . '&task=apcu', false) }}"
     role="tab"
     class="tab @if($task === 'apcu') tab-active @endif">
    {{ Lang::txt('COM_SYSTEM_CACHE_APCU') }}
  </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

  {{-- OPcache panel --}}
  <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
    <legend class="fieldset-legend">OPcache</legend>

    @if(!$opcache)
      <div role="alert" class="alert alert-warning">
        <span>{{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE_NOT_AVAILABLE') }}</span>
      </div>
    @else
      @php
        $mem      = $opcache['memory_usage'];
        $stats    = $opcache['opcache_statistics'];
        $total    = $mem['used_memory'] + $mem['free_memory'];
        $usedPct  = $total > 0 ? round($mem['used_memory'] / $total * 100, 1) : 0;
        $hitRate  = round($stats['opcache_hit_rate'], 1);
      @endphp

      <table class="table table-sm w-full">
        <tbody>
          <tr>
            <th class="text-left w-1/2">{{ Lang::txt('Status') }}</th>
            <td>
              @if($opcache['opcache_enabled'])
                <span class="badge badge-success badge-sm">Enabled</span>
              @else
                <span class="badge badge-ghost badge-sm">Disabled</span>
              @endif
            </td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_MEMORY') }}</th>
            <td>
              {{ Number::formatBytes($mem['used_memory']) }}
              / {{ Number::formatBytes($total) }}
              ({{ $usedPct }}%)
            </td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_HIT_RATE') }}</th>
            <td>{{ $hitRate }}%</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_HITS') }}</th>
            <td>{{ number_format($stats['hits']) }}</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_MISSES') }}</th>
            <td>{{ number_format($stats['misses']) }}</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_ENTRIES') }}</th>
            <td>{{ number_format($stats['num_cached_scripts']) }}</td>
          </tr>
          @if(isset($stats['oom_restarts']))
          <tr>
            <th class="text-left">OOM Restarts</th>
            <td>{{ number_format($stats['oom_restarts']) }}</td>
          </tr>
          @endif
        </tbody>
      </table>

      @if($opcacheConfig)
        @php
          $directives = $opcacheConfig['directives'];
          $show = [
            'opcache.memory_consumption',
            'opcache.max_accelerated_files',
            'opcache.interned_strings_buffer',
            'opcache.validate_timestamps',
            'opcache.revalidate_freq',
            'opcache.jit',
            'opcache.jit_buffer_size',
          ];
        @endphp
        <h4 class="font-semibold mt-4 mb-2 text-sm">Configuration</h4>
        <table class="table table-sm w-full">
          <tbody>
            @foreach($show as $key)
              @if(isset($directives[$key]))
                @php
                  $val = $directives[$key];
                  if (is_bool($val)) {
                      $display = $val ? 'true' : 'false';
                  } elseif (is_numeric($val) && $val > 1048576) {
                      $display = Number::formatBytes($val);
                  } else {
                      $display = (string) $val;
                  }
                @endphp
                <tr>
                  <th class="text-left font-mono text-xs">{{ $key }}</th>
                  <td>{{ $display }}</td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      @endif
    @endif
  </fieldset>

  {{-- APCu panel --}}
  <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
    <legend class="fieldset-legend">APCu</legend>

    @if(!$apcu)
      <div role="alert" class="alert alert-warning">
        <span>{{ Lang::txt('COM_SYSTEM_CACHE_APCU_NOT_AVAILABLE') }}</span>
      </div>
    @else
      @php
        $totalMem = $apcuMem['seg_size'] * $apcuMem['num_seg'];
        $availMem = $apcuMem['avail_mem'];
        $usedMem  = $totalMem - $availMem;
        $usedPct  = $totalMem > 0 ? round($usedMem / $totalMem * 100, 1) : 0;
        $hits     = $apcu['num_hits'] ?? 0;
        $misses   = $apcu['num_misses'] ?? 0;
        $totalReq = $hits + $misses;
        $hitRate  = $totalReq > 0 ? round($hits / $totalReq * 100, 1) : 0;
      @endphp

      <table class="table table-sm w-full">
        <tbody>
          <tr>
            <th class="text-left w-1/2">{{ Lang::txt('COM_SYSTEM_CACHE_MEMORY') }}</th>
            <td>
              {{ Number::formatBytes($usedMem) }}
              / {{ Number::formatBytes($totalMem) }}
              ({{ $usedPct }}%)
            </td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_HIT_RATE') }}</th>
            <td>{{ $hitRate }}%</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_HITS') }}</th>
            <td>{{ number_format($hits) }}</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_MISSES') }}</th>
            <td>{{ number_format($misses) }}</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_ENTRIES') }}</th>
            <td>{{ number_format($apcu['num_entries'] ?? 0) }}</td>
          </tr>
          <tr>
            <th class="text-left">{{ Lang::txt('COM_SYSTEM_CACHE_UPTIME') }}</th>
            <td>
              @if(isset($apcu['start_time']))
                @php
                  $uptime = time() - $apcu['start_time'];
                  $days   = floor($uptime / 86400);
                  $hours  = floor(($uptime % 86400) / 3600);
                  $mins   = floor(($uptime % 3600) / 60);
                @endphp
                {{ $days }}d {{ $hours }}h {{ $mins }}m
              @else
                n/a
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    @endif
  </fieldset>

</div>
