{{--
  mod_whosonline — admin dashboard Blade template

  Variables: $rows, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $found     = [];
  $siteRows  = [];
  $adminRows = [];

  foreach ($rows as $row) {
      $key = $row->client_id . '.' . $row->userid;
      if ($row->userid && in_array($key, $found)) {
          continue;
      }
      $found[] = $key;
      if ($row->client_id == 0) {
          $siteRows[] = $row;
      } else {
          $adminRows[] = $row;
      }
  }

  $editAuthorized  = User::authorise('core.manage', 'com_members');
  $displayLimit    = (int) $params->get('display_limit', 25);
  $totalUnique     = count($siteRows) + count($adminRows);
  $needsScroll     = $totalUnique > 30;
@endphp

{{-- Summary counts --}}
<div class="flex mb-3 gap-2">
  <div class="flex-1 text-center py-1.5 px-2 bg-base-200 rounded-md">
    <div class="text-2xl font-bold leading-none text-primary">{{ count($adminRows) }}</div>
    <div class="text-[0.65rem] uppercase tracking-wide opacity-70 mt-0.5">
      {{ Lang::txt('MOD_WHOSONLINE_COL_ADMIN') }}
    </div>
  </div>
  <div class="flex-1 text-center py-1.5 px-2 bg-base-200 rounded-md">
    <div class="text-2xl font-bold leading-none">{{ count($siteRows) }}</div>
    <div class="text-[0.65rem] uppercase tracking-wide opacity-70 mt-0.5">
      {{ Lang::txt('MOD_WHOSONLINE_COL_SITE') }}
    </div>
  </div>
</div>

{{-- Scrollable list $area --}}
<div class="{{ $needsScroll ? 'max-h-[28rem] overflow-y-auto overflow-x-hidden' : '' }}">

  {{-- ── Administrator section ──────────────────────────── --}}
  @if (!empty($adminRows))
    <div class="mb-2">
      {{-- Section header band --}}
      <div class="text-[0.65rem] font-bold uppercase tracking-wider text-primary-dark
                  py-1 px-2 bg-primary/10 rounded-t">
        {{ Lang::txt('MOD_WHOSONLINE_COL_ADMIN') }}
      </div>
      {{-- Rows --}}
      <table class="w-full border-collapse text-[0.78rem]">
        <tbody>
          @foreach (array_slice($adminRows, 0, $displayLimit) as $row)
            @php
              $user     = User::getInstance($row->username);
              $name     = $user->get('name') ?: $row->username;
              $uname    = $user->get('username') ?: $row->username;
              $hoursAgo = round((time() - (int)$row->time) / 3600.0, 1);
            @endphp
            <tr class="border-b border-primary/[0.08]">
              <td class="py-1 px-2 overflow-hidden text-ellipsis whitespace-nowrap max-w-0 w-1/2">
                @if ($editAuthorized && $row->userid)
                  <a href="{{ Route::url('index.php?option=com_members&task=edit&id=' . $row->userid, false) }}"
                     class="link link-hover font-medium">{{ $name }}</a>
                @else
                  <span class="font-medium">{{ $name }}</span>
                @endif
                @if ($uname && $uname !== $name)
                  <span class="opacity-70 text-[0.7rem]"> {{ $uname }}</span>
                @endif
              </td>
              <td class="py-1 pr-2 opacity-70 whitespace-nowrap text-[0.72rem] w-auto">
                {!! sprintf(Lang::txt('MOD_WHOSONLINE_HOURS_AGO'), $hoursAgo) !!}
              </td>
              @if ($editAuthorized)
                <td class="py-1 pr-2 whitespace-nowrap w-px">
                  @if ($row->userid)
                    <a href="{{ Route::url('index.php?option=com_login&task=logout&uid=' . $row->userid . '&' . Session::getFormToken() . '=1', false) }}"
                       $title="{{ Lang::txt('JLOGOUT') }}"
                       class="inline-block leading-none align-middle text-error/60">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                      </svg>
                    </a>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  {{-- ── Site section ───────────────────────────────────── --}}
  @if (!empty($siteRows))
    <div>
      {{-- Section header band --}}
      <div class="text-[0.65rem] font-bold uppercase tracking-wider
                  opacity-70 py-1 px-2 bg-base-300 rounded-t">
        {{ Lang::txt('MOD_WHOSONLINE_COL_SITE') }}
      </div>
      {{-- Rows --}}
      <table class="w-full border-collapse text-[0.78rem]">
        <tbody>
          @foreach (array_slice($siteRows, 0, $displayLimit) as $row)
            @php
              $user     = User::getInstance($row->username);
              $name     = $user->get('name') ?: $row->username;
              $uname    = $user->get('username') ?: $row->username;
              $hoursAgo = round((time() - (int)$row->time) / 3600.0, 1);
            @endphp
            <tr class="border-b border-base-200">
              <td class="py-1 px-2 overflow-hidden text-ellipsis whitespace-nowrap max-w-0 w-1/2">
                @if ($editAuthorized && $row->userid)
                  <a href="{{ Route::url('index.php?option=com_members&task=edit&id=' . $row->userid, false) }}"
                     class="link link-hover font-medium">{{ $name }}</a>
                @else
                  <span class="font-medium">{{ $name }}</span>
                @endif
                @if ($uname && $uname !== $name)
                  <span class="opacity-70 text-[0.7rem]"> {{ $uname }}</span>
                @endif
              </td>
              <td class="py-1 pr-2 opacity-70 whitespace-nowrap text-[0.72rem] w-auto">
                {!! sprintf(Lang::txt('MOD_WHOSONLINE_HOURS_AGO'), $hoursAgo) !!}
              </td>
              @if ($editAuthorized)
                <td class="py-1 pr-2 whitespace-nowrap w-px">
                  @if ($row->userid)
                    <a href="{{ Route::url('index.php?option=com_login&task=logout&uid=' . $row->userid . '&' . Session::getFormToken() . '=1', false) }}"
                       $title="{{ Lang::txt('JLOGOUT') }}"
                       class="inline-block leading-none align-middle text-error/60">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                      </svg>
                    </a>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  {{-- Empty state --}}
  @if (empty($siteRows) && empty($adminRows))
    <div class="text-center opacity-70 py-4 text-[0.78rem]">
      {{ Lang::txt('MOD_WHOSONLINE_NO_RESULTS') }}
    </div>
  @endif

</div>{{-- end scrollable $area --}}

<div class="pt-2 text-xs">
  <a href="{{ Route::url('index.php?option=com_members&controller=whosonline', false) }}"
     class="link link-hover">{!! Lang::txt('MOD_WHOSONLINE_VIEW_ALL') !!}</a>
</div>
