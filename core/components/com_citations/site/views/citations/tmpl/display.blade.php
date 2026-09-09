{{--
  Citations — introduction / overview page.

  Variables from controller (displayTask):
    $title         — Page title string
    $yearlystats   — Array of yearly citation counts by affiliation
    $typestats     — Array of citation counts by type
    $allow_import  — 0/1/2 permission for adding citations
    $allow_bulk_import — 0/1/2 permission for bulk import
    $isAdmin       — Boolean admin flag

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }

  Document::setTitle(Lang::txt('COM_CITATIONS'));

  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse', false);
  $canImport = $allow_import == 1
      || $allow_bulk_import == 1
      || ($allow_import == 2 && $isAdmin)
      || ($allow_bulk_import == 2 && $isAdmin);
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS')">
  @slot('actions')
    <a class="btn"
       href="{{ $browseUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BROWSE') }}
    </a>
    @if($allow_import == 1 || ($allow_import == 2 && $isAdmin))
      <a class="btn"
         href="{{ Route::url('index.php?option=' . $option . '&task=add', false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ Lang::txt('COM_CITATIONS_SUBMIT_CITATION') }}
      </a>
    @endif
    @if($allow_bulk_import == 1 || ($allow_bulk_import == 2 && $isAdmin))
      <a class="btn"
         href="{{ Route::url('index.php?option=' . $option . '&task=import', false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
        </svg>
        {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION') }}
      </a>
    @endif
  @endslot

  {{-- Introduction --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-base">{{ Lang::txt('COM_CITATIONS_WHAT_ARE_CITATIONS') }}</h2>
        <p class="text-sm text-base-content/70">
          {{ Lang::txt('COM_CITATIONS_WHAT_ARE_CITATIONS_DESC') }}
        </p>
      </div>
    </div>
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h2 class="card-title text-base">{{ Lang::txt('COM_CITATIONS_SUBMIT_CITATIONS') }}</h2>
        <p class="text-sm text-base-content/70">
          @if($canImport)
            @php
              $submitUrl = Route::url('index.php?option=' . $option . '&task=add', false);
            @endphp
            {!! Lang::txt('COM_CITATIONS_SUBMIT_CITATIONS_DESC', $submitUrl) !!}
          @else
            {!! Lang::txt('COM_CITATIONS_SUBMIT_CITATIONS_DESC_NOTALLOWED', '/support') !!}
          @endif
        </p>
      </div>
    </div>
  </div>

  {{-- Search --}}
  <x-search-bar
      :action="$browseUrl"
      query=""
      :placeholder="Lang::txt('COM_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER')"
      :buttonLabel="Lang::txt('COM_CITATIONS_SEARCH')"
  />

  {{-- Metrics --}}
  <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_CITATIONS_METRICS') }}</h2>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Yearly Stats --}}
    @php
      $totalAll = 0;
      foreach ($yearlystats as $year => $amt) {
          $totalAll += intval($amt['affiliate']) + intval($amt['non-affiliate']);
      }
    @endphp
    @if($totalAll > 0)
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-sm">
            {{ Lang::txt('COM_CITATIONS_TABLE_METRICS_YEAR') }}
          </h3>
          <div class="overflow-x-auto">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>{{ Lang::txt('COM_CITATIONS_YEAR') }}</th>
                  <th class="text-right">{{ Lang::txt('COM_CITATIONS_AFFILIATED') }}</th>
                  <th class="text-right">{{ Lang::txt('COM_CITATIONS_NONAFFILIATED') }}</th>
                  <th class="text-right font-bold">{{ Lang::txt('COM_CITATIONS_TOTAL') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($yearlystats as $year => $amt)
                  @php
                    $yearTotal = intval($amt['affiliate']) + intval($amt['non-affiliate']);
                  @endphp
                  <tr>
                    <td>{{ $year }}</td>
                    <td class="text-right">{{ $amt['affiliate'] }}</td>
                    <td class="text-right">{{ $amt['non-affiliate'] }}</td>
                    <td class="text-right font-semibold">{{ $yearTotal }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="font-bold">
                  <td colspan="3">{{ Lang::txt('COM_CITATIONS_TOTAL') }}</td>
                  <td class="text-right">{{ $totalAll }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    @endif

    {{-- Type Stats --}}
    @php
      $typeData = [];
      foreach ($typestats as $type => $count) {
          $typeData[] = ['name' => trim($type), 'count' => intval($count)];
      }
      usort($typeData, fn($a, $b) => $b['count'] - $a['count']);
      $typeSum = array_sum(array_column($typeData, 'count'));
    @endphp
    @if($typeSum > 0)
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-sm">
            {{ Lang::txt('COM_CITATIONS_TABLE_METRICS_TYPE') }}
          </h3>
          <div class="overflow-x-auto">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>{{ Lang::txt('COM_CITATIONS_TYPE') }}</th>
                  <th class="w-1/2">{{ Lang::txt('COM_CITATIONS_PERCENT') }}</th>
                  <th class="text-right">{{ Lang::txt('COM_CITATIONS_TOTAL') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($typeData as $td)
                  @php
                    $pct = $typeSum > 0 ? round(100 * $td['count'] / $typeSum, 1) : 0;
                  @endphp
                  <tr>
                    <td class="whitespace-nowrap">{{ $td['name'] }}</td>
                    <td>
                      <div class="flex items-center gap-2">
                        <progress class="progress progress-primary w-full"
                                  value="{{ $pct }}" max="100"></progress>
                        <span class="text-xs text-base-content/50 w-12 text-right">{{ $pct }}%</span>
                      </div>
                    </td>
                    <td class="text-right">{{ $td['count'] }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="font-bold">
                  <td>{{ Lang::txt('COM_CITATIONS_TOTAL') }}</td>
                  <td class="text-right">100%</td>
                  <td class="text-right">{{ $typeSum }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    @endif
  </div>

</x-page-container>
