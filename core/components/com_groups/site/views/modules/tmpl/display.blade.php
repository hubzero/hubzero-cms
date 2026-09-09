{{--
  Group modules list (sub-view within page manager).

  Variables (passed via $__view->view):
    $group   — Group object
    $modules — collection of Module models

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $cn      = $group->get('cn');
  $modBase = 'index.php?option=com_groups&cn=' . $cn . '&controller=modules&task=';

  // Build array of unique positions
  $positions = [];
  foreach ($modules as $module) {
      $pos = $module->get('position');
      if ($pos !== '' && !in_array($pos, $positions)) {
          $positions[] = $pos;
      }
  }
@endphp

<ul class="toolbar toolbar-modules flex flex-wrap items-center gap-2 mb-4">
  <li>
    <a class="btn btn-sm btn-primary"
       href="{{ Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=modules&task=add') }}">
      {{ Lang::txt('COM_GROUPS_PAGES_NEW_MODULE') }}
    </a>
  </li>
  <li>
    <select class="select select-bordered select-sm">
      <option value="">{{ Lang::txt('COM_GROUPS_PAGES_MODULE_FILTER') }}</option>
      @foreach($positions as $position)
        <option value="{{ $position }}">{{ $position }}</option>
      @endforeach
    </select>
  </li>
  <li class="text-sm text-base-content/60">{{ Lang::txt('COM_GROUPS_PAGES_MODULE_OR') }}</li>
  <li>
    <input type="text" class="input input-bordered input-sm"
           placeholder="{{ Lang::txt('COM_GROUPS_PAGES_MODULE_SEARCH') }}" />
  </li>
</ul>

@if($modules->count() > 0)
  <ul class="space-y-2">
    @foreach($modules as $module)
      @php
        $mid     = $module->get('id');
        $editUrl = Route::url($modBase . 'edit&moduleid=' . $mid);
        $delUrl  = Route::url($modBase . 'delete&moduleid=' . $mid);
        $pubUrl  = Route::url($modBase . 'publish&moduleid=' . $mid);
        $unpubUrl = Route::url($modBase . 'unpublish&moduleid=' . $mid);

        $pages = [];
        $menus = $module->menu('list');
        foreach ($menus as $menu) {
            $pages[] = $menu->getPageTitle();
        }
      @endphp
      <li class="card bg-base-100 shadow-sm">
        <div class="card-body p-4 flex flex-row items-center gap-4">
          <div class="flex-1 min-w-0">
            <a href="{{ $editUrl }}" class="font-semibold link link-hover">
              {{ $module->get('title') }}
            </a>
            @if($module->get('approved') == 0)
              <span class="badge badge-warning badge-sm ml-2">
                {{ Lang::txt('COM_GROUPS_PAGES_MODULE_PENDING_APPROVAL') }}
              </span>
            @endif
            <div class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON', implode(', ', $pages)) }}
            </div>
          </div>

          <div class="text-sm text-base-content/60 shrink-0">
            <span class="font-semibold">{{ Lang::txt('COM_GROUPS_PAGES_MODULE_POSITION') }}:</span>
            {{ $module->get('position') }}
          </div>

          <div class="shrink-0">
            @if($module->get('state') == 0)
              <a class="badge badge-ghost" href="{{ $pubUrl }}"
                 title="{{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE') }}">
                {{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE') }}
              </a>
            @else
              <a class="badge badge-success" href="{{ $unpubUrl }}"
                 title="{{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE') }}">
                {{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE') }}
              </a>
            @endif
          </div>

          <div class="dropdown dropdown-end">
            <button tabindex="0" role="button" class="btn btn-sm btn-ghost">
              {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_MODULE') }}
              <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">
              <li><a href="{{ $editUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_EDIT_MODULE') }}</a></li>
              <li class="divider"></li>
              @if($module->get('state') == 0)
                <li><a href="{{ $pubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE') }}</a></li>
              @else
                <li><a href="{{ $unpubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE') }}</a></li>
              @endif
              <li class="divider"></li>
              <li><a href="{{ $delUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_DELETE_MODULE') }}</a></li>
            </ul>
          </div>
        </div>
      </li>
    @endforeach
  </ul>
@else
  <x-empty-state :title="Lang::txt('COM_GROUPS_PAGES_NO_MODULES')" />
@endif
