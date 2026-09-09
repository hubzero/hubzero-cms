{{--
  Group categories list (sub-view within page manager).

  Variables (passed via $__view->view):
    $group      — Group object
    $categories — collection of Category models

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $cn = $group->get('cn');
  $catBase = 'index.php?option=com_groups&cn=' . $cn . '&controller=categories';
@endphp

<ul class="toolbar toolbar-categories">
  <li class="new">
    <a class="btn btn-sm btn-primary" href="{{ Route::url($catBase . '&task=add') }}">
      {{ Lang::txt('COM_GROUPS_PAGES_NEW_CATEGORY') }}
    </a>
  </li>
</ul>

@if($categories->count() > 0)
  <ul class="space-y-2 mt-4">
    @foreach($categories as $category)
      @php
        $catId   = $category->get('id');
        $editUrl = Route::url($catBase . '&task=edit&categoryid=' . $catId);
        $delUrl  = Route::url($catBase . '&task=delete&categoryid=' . $catId);
      @endphp
      <li class="card bg-base-100 shadow-sm">
        <div class="card-body p-4 flex flex-row items-center gap-4">
          <div class="w-4 h-4 rounded-full shrink-0"
               style="background-color: #{{ e($category->get('color', 'ccc')) }};"></div>
          <div class="flex-1 min-w-0">
            <a href="{{ $editUrl }}" class="font-semibold link link-hover">
              {{ $category->get('title') }}
            </a>
            <div class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_X_PAGES', $category->getPages('count')) }}
            </div>
          </div>
          <div class="dropdown dropdown-end">
            <button tabindex="0" role="button" class="btn btn-sm btn-ghost">
              {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_CATEGORY') }}
              <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-48 p-2 shadow">
              <li><a href="{{ $editUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY') }}</a></li>
              <li class="divider"></li>
              <li><a href="{{ $delUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_DELETE_CATEGORY') }}</a></li>
            </ul>
          </div>
        </div>
      </li>
    @endforeach
  </ul>
@else
  <x-empty-state :title="Lang::txt('COM_GROUPS_PAGES_NO_CATEGORIES')" />
@endif
