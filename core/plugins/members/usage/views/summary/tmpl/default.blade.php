{{--
  Member Usage — contribution statistics, tools, and resources tables.

  Variables from plugin (onMembers):
    $contribution     — array with contribs, first, last
    $rank             — user rank
    $citation_count   — total citations
    $cluster_users    — cluster education user count
    $cluster_classes  — cluster class count
    $cluster_schools  — cluster school count
    $tool_stats       — array of tool objects
    $tool_total_12    — 12-month tool user total
    $tool_total_14    — all-time tool user total
    $andmore_stats    — array of resource objects
    $andmore_total_12 — 12-month resource user total
    $andmore_total_14 — all-time resource user total
    $member           — member profile object
    $option           — component option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Plugins\Members\Usage\Usage as PlgMembersUsage;

  $__view->css('usage', 'com_usage');
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_USAGE') }}</h3>

<p class="alert alert-info mb-6">{!! Lang::txt('PLG_MEMBERS_USAGE_EXPLANATION') !!}</p>

{{-- Overview table --}}
<div class="overflow-x-auto mb-6">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm font-semibold mb-2">
      {{ Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW') }}
    </caption>
    <thead>
      <tr>
        <th>{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_ITEM') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_VALUE') }}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS') }}:</th>
        <td class="text-right">{{ $contribution['contribs'] }}</td>
      </tr>
      <tr>
        <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK') }}:</th>
        <td class="text-right">{{ $rank }}</td>
      </tr>
      <tr>
        <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST') }}:</th>
        <td class="text-right">{{ Date::of($contribution['first'])->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
      </tr>
      <tr>
        <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST') }}:</th>
        <td class="text-right">{{ Date::of($contribution['last'])->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
      </tr>
      <tr>
        <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_CITATIONS') }}:</th>
        <td class="text-right">{{ $citation_count }}</td>
      </tr>
      @if ($cluster_users)
        <tr>
          <th scope="row">{{ Lang::txt('PLG_MEMBERS_USAGE_CLUSTERS') }}:</th>
          <td class="text-right">
            {{ Lang::txt('PLG_MEMBERS_USAGE_USERS_IN_COURSES_SERVED', number_format($cluster_users), number_format($cluster_classes), number_format($cluster_schools)) }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

{{-- Tools table --}}
<div class="overflow-x-auto mb-6">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm font-semibold mb-2">
      {{ Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_TOOLS') }}
    </caption>
    <thead>
      <tr>
        <th>#</th>
        <th>{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_TOOL_TITLE') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_YEAR') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_TOTAL') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED') }}</th>
      </tr>
    </thead>
    <tbody>
      @if ($tool_stats)
        @php
          $count = 0;
          $sumUser12 = 0;
          $sumUser14 = 0;
          $sumSim12  = 0;
          $sumSim14  = 0;
        @endphp
        @foreach ($tool_stats as $row)
          @php
            $uc12 = PlgMembersUsage::getUsercount($row->id, 12, 7);
            $uc14 = PlgMembersUsage::getUsercount($row->id, 14, 7);
            $sc12 = PlgMembersUsage::getSimcount($row->id, 12);
            $sc14 = PlgMembersUsage::getSimcount($row->id, 14);

            $sumUser12 += intval($uc12);
            $sumUser14 += intval($uc14);
            $sumSim12  += intval($sc12);
            $sumSim14  += intval($sc14);

            $fmtUc12 = is_numeric($uc12) ? number_format($uc12) : $uc12;
            $fmtUc14 = is_numeric($uc14) ? number_format($uc14) : $uc14;
            $fmtSc12 = is_numeric($sc12) ? number_format($sc12) : $sc12;
            $fmtSc14 = is_numeric($sc14) ? number_format($sc14) : $sc14;

            $resUrl     = Route::url('index.php?option=com_resources&id=' . $row->id);
            $usageUrl12 = Route::url('index.php?option=com_usage&task=tools&id=' . $row->id . '&period=12');
            $usageUrl14 = Route::url('index.php?option=com_usage&task=tools&id=' . $row->id . '&period=14');
            $count++;
          @endphp
          <tr>
            <td>{{ $count }}</td>
            <td><a class="link link-hover" href="{{ $resUrl }}">{{ $row->title }}</a></td>
            <td class="text-right"><a class="link link-hover" href="{{ $usageUrl12 }}">{{ $fmtUc12 }}</a></td>
            <td class="text-right"><a class="link link-hover" href="{{ $usageUrl12 }}">{{ $fmtSc12 }}</a></td>
            <td class="text-right"><a class="link link-hover" href="{{ $usageUrl14 }}">{{ $fmtUc14 }}</a></td>
            <td class="text-right"><a class="link link-hover" href="{{ $usageUrl14 }}">{{ $fmtSc14 }}</a></td>
            <td class="text-right">{{ PlgMembersUsage::getCitationcount($row->id, 0) }}</td>
            <td class="text-right">{{ Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
          </tr>
        @endforeach
        @if ($tool_total_14 && $tool_total_12)
          <tr class="font-semibold bg-base-200">
            <td></td>
            <td>{{ Lang::txt('PLG_MEMBERS_USAGE_TOTAL') }}</td>
            <td class="text-right">{{ number_format($tool_total_12) }}</td>
            <td class="text-right">{{ number_format($sumSim12) }}</td>
            <td class="text-right">{{ number_format($tool_total_14) }}</td>
            <td class="text-right">{{ number_format($sumSim14) }}</td>
            <td></td>
            <td></td>
          </tr>
        @endif
      @else
        <tr>
          <td colspan="8" class="text-center text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

{{-- Resources table --}}
<div class="overflow-x-auto mb-6">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm font-semibold mb-2">
      {{ Lang::txt('PLG_MEMBERS_USAGE_TBL_CAPTION_RESOURCES') }}
    </caption>
    <thead>
      <tr>
        <th>#</th>
        <th>{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_RESOURCE_TITLE') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED') }}</th>
      </tr>
    </thead>
    <tbody>
      @if ($andmore_stats)
        @php
          $count   = 0;
          $totals  = ['usercount12' => 0, 'usercount14' => 0, 'citations' => 0];
          $serials = (array) PlgMembersUsage::getSerialResourceTypes();

          // First pass: find children of serial types
          $children = [];
          foreach ($andmore_stats as $row) {
              $children[$row->id] = in_array($row->type_id, $serials)
                  ? (array) PlgMembersUsage::getSerialResourceChildren($row->id)
                  : [];
          }

          // Second pass: group into parent/child
          $andmore = [0 => []];
          foreach ($andmore_stats as $row) {
              $hasParent = false;
              foreach ($children as $parent => $childs) {
                  if (in_array($row->id, $childs)) {
                      $andmore[$parent][] = $row;
                      $hasParent = true;
                      break;
                  }
              }
              if (!$hasParent) {
                  $andmore[0][] = $row;
              }
          }
        @endphp

        @foreach ($andmore[0] as $row)
          @php
            $uc12 = PlgMembersUsage::getUsercount($row->id, 12);
            $uc14 = PlgMembersUsage::getUsercount($row->id, 14);
            $cites = PlgMembersUsage::getCitationcount($row->id, 0);

            $fmtUc12 = is_numeric($uc12) ? number_format($uc12) : $uc12;
            $fmtUc14 = is_numeric($uc14) ? number_format($uc14) : $uc14;

            if (!in_array($row->type_id, $serials)) {
                $totals['usercount12'] += (int) str_replace(',', '', $fmtUc12);
                $totals['usercount14'] += (int) str_replace(',', '', $fmtUc14);
            }
            $totals['citations'] += (int) $cites;
            $count++;
          @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>
              <a class="link link-hover" href="{{ Route::url('index.php?option=com_resources&id=' . $row->id) }}">
                {{ $row->title }}
              </a>
              <span class="text-xs text-base-content/50 ml-1">{{ $row->type }}</span>
            </td>
            <td class="text-right">{{ $fmtUc12 }}</td>
            <td class="text-right">{{ $fmtUc14 }}</td>
            <td class="text-right">{{ $cites }}</td>
            <td class="text-right">{{ Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
          </tr>

          {{-- Child resources --}}
          @if (isset($andmore[$row->id]))
            @foreach ($andmore[$row->id] as $rw)
              @php
                $cUc12 = PlgMembersUsage::getUsercount($rw->id, 12);
                $cUc14 = PlgMembersUsage::getUsercount($rw->id, 14);
                $cCites = PlgMembersUsage::getCitationcount($rw->id, 0);

                $fmtCUc12 = is_numeric($cUc12) ? number_format($cUc12) : $cUc12;
                $fmtCUc14 = is_numeric($cUc14) ? number_format($cUc14) : $cUc14;

                $totals['usercount12'] += (int) str_replace(',', '', $fmtCUc12);
                $totals['usercount14'] += (int) str_replace(',', '', $fmtCUc14);
                $totals['citations']   += (int) $cCites;
                $count++;
              @endphp
              <tr class="bg-base-200/50">
                <td>{{ $count }}</td>
                <td class="pl-8">
                  <span class="text-base-content/40 mr-1">|-</span>
                  <a class="link link-hover" href="{{ Route::url('index.php?option=com_resources&id=' . $rw->id) }}">
                    {{ $rw->title }}
                  </a>
                  <span class="text-xs text-base-content/50 ml-1">{{ $rw->type }}</span>
                </td>
                <td class="text-right">{{ $fmtCUc12 }}</td>
                <td class="text-right">{{ $fmtCUc14 }}</td>
                <td class="text-right">{{ $cCites }}</td>
                <td class="text-right">{{ Date::of($rw->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
              </tr>
            @endforeach
          @endif
        @endforeach

        @if ($count)
          <tr class="font-semibold bg-base-200">
            <td></td>
            <td>{{ Lang::txt('TOTAL') }}</td>
            <td class="text-right">{{ number_format($totals['usercount12']) }}</td>
            <td class="text-right">{{ number_format($totals['usercount14']) }}</td>
            <td class="text-right">{{ number_format($totals['citations']) }}</td>
            <td></td>
          </tr>
        @endif
      @else
        <tr>
          <td colspan="6" class="text-center text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_USAGE_NO_RESULTS') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

<p class="text-sm text-base-content/60">{{ Lang::txt('PLG_MEMBERS_USAGE_FOOTNOTE') }}</p>
