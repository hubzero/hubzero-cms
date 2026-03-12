{{--
  System Cache — APCu entries list

  Variables from controller:
    $apcu    — array|null  (apcu_cache_info() data)
    $apcuMem — array|null  (apcu_sma_info() data)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Utility\Number;

  Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_APCU'), 'config');
  if ($apcu) {
      Toolbar::custom('resetapcu', 'refresh', '', Lang::txt('COM_SYSTEM_CACHE_RESET_APCU'), false);
  }

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
     class="tab">
    {{ Lang::txt('COM_SYSTEM_CACHE_OPCACHE') }}
  </a>
  <a href="{{ Route::url($baseUrl . '&task=apcu', false) }}"
     role="tab"
     class="tab tab-active">
    {{ Lang::txt('COM_SYSTEM_CACHE_APCU') }}
  </a>
</div>

@if(!$apcu)
  <div role="alert" class="alert alert-warning">
    <span>{{ Lang::txt('COM_SYSTEM_CACHE_APCU_NOT_AVAILABLE') }}</span>
  </div>
@else
  @php
    $entries = $apcu['cache_list'] ?? [];
    usort($entries, fn($a, $b) => ($b['num_hits'] ?? 0) - ($a['num_hits'] ?? 0));
  @endphp

  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        name="adminForm"
        id="adminForm">

    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">
        {{ Lang::txt('COM_SYSTEM_CACHE_APCU') }}
        ({{ number_format(count($entries)) }} entries)
      </legend>

      <div class="overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Key</th>
              <th class="priority-3">{{ Lang::txt('COM_SYSTEM_CACHE_HITS') }}</th>
              <th class="priority-3">Size</th>
              <th class="priority-4">Created</th>
              <th class="priority-4">TTL</th>
            </tr>
          </thead>
          <tbody>
            @forelse($entries as $entry)
              @php
                $created = $entry['creation_time'] ?? $entry['mtime'] ?? 0;
                $ttl     = $entry['ttl'] ?? 0;
              @endphp
              <tr>
                <td>{{ $entry['info'] ?? $entry['key'] ?? '' }}</td>
                <td class="priority-3">{{ number_format($entry['num_hits'] ?? 0) }}</td>
                <td class="priority-3">{{ Number::formatBytes($entry['mem_size'] ?? 0) }}</td>
                <td class="priority-4">{{ $created ? date('Y-m-d H:i:s', $created) : 'n/a' }}</td>
                <td class="priority-4">{{ $ttl > 0 ? number_format($ttl) . 's' : 'none' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-6 text-muted-foreground">
                  No cached entries.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </fieldset>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="apcu" />
  </form>
@endif
