{{--
  mod_grouppages — unapproved group pages/modules widget

  Shows $groups with pending page and $module approvals.

  Variables: $unapprovedPages, $unapprovedModules, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $rows = [];
  if ($unapprovedPages) {
      foreach ($unapprovedPages as $page) {
          $gid = $page->get('gidNumber');
          $rows[$gid]['pages'] = ($rows[$gid]['pages'] ?? 0) + 1;
      }
  }
  if ($unapprovedModules) {
      foreach ($unapprovedModules as $mod) {
          $gid = $mod->get('gidNumber');
          $rows[$gid]['modules'] = ($rows[$gid]['modules'] ?? 0) + 1;
      }
  }
@endphp

@if (count($rows) > 0)
  <table class="w-full text-xs">
    <thead>
      <tr class="border-b border-base-200">
        <th class="text-left font-medium opacity-70 pb-1.5 pr-2">
          {{ Lang::txt('MOD_GROUPPAGES_COL_GROUP') }}
        </th>
        <th class="text-center font-medium opacity-70 pb-1.5 w-16">
          {{ Lang::txt('MOD_GROUPPAGES_COL_PAGES') }}
        </th>
        <th class="text-center font-medium opacity-70 pb-1.5 w-16">
          {{ Lang::txt('MOD_GROUPPAGES_COL_MODULES') }}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $gidNumber => $row)
        @php
          $group = \Hubzero\User\Group::getInstance($gidNumber);
          $cn    = $group ? $group->get('cn') : $gidNumber;
          $desc  = $group ? $group->get('description') : $gidNumber;
        @endphp
        <tr class="border-b border-base-200/50">
          <td class="py-1.5 pr-2 truncate max-w-0">{{ $desc }}</td>
          <td class="py-1.5 text-center">
            <a href="{{ Route::url('index.php?option=com_groups&gid=' . $cn . '&controller=pages', false) }}"
               class="link link-hover font-medium {{ ($row['pages'] ?? 0) > 0 ? 'text-warning' : '' }}">
              {{ $row['pages'] ?? 0 }}
            </a>
          </td>
          <td class="py-1.5 text-center">
            <a href="{{ Route::url('index.php?option=com_groups&gid=' . $cn . '&controller=modules', false) }}"
               class="link link-hover font-medium {{ ($row['modules'] ?? 0) > 0 ? 'text-warning' : '' }}">
              {{ $row['modules'] ?? 0 }}
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <div class="flex items-center gap-2 text-sm text-success-fg py-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ Lang::txt('MOD_GROUPPAGES_NO_RESULTS') }}
  </div>
@endif
