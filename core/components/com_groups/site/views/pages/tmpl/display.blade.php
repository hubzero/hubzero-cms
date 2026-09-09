{{--
  Pages toolbar and filter bar (sub-view within page manager).

  Variables (passed via $__view->view):
    $group      — Group object
    $categories — collection of Category models
    $pages      — array: page tree
    $config     — Registry: component config
    $search     — string: search term

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $cn = $group->get('cn');
@endphp

<ul class="toolbar toolbar-pages flex flex-wrap items-center gap-2 mb-4">
  <li>
    <a class="btn btn-sm btn-primary"
       href="{{ Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=pages&task=add') }}">
      {{ Lang::txt('COM_GROUPS_PAGES_NEW_PAGE') }}
    </a>
  </li>
  <li>
    <select name="filer" class="select select-bordered select-sm">
      <option value="">{{ Lang::txt('COM_GROUPS_PAGES_PAGE_FILTER') }}</option>
      @foreach($categories as $cat)
        <option data-color="#{{ $cat->get('color') }}"
                value="{{ $cat->get('id') }}">{{ $cat->get('title') }}</option>
      @endforeach
    </select>
  </li>
  <li class="text-sm text-base-content/60">{{ Lang::txt('COM_GROUPS_PAGES_PAGE_OR') }}</li>
  <li>
    <input type="text" name="search" class="input input-bordered input-sm"
           placeholder="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_SEARCH') }}"
           value="{{ e($search ?? '') }}" />
  </li>
</ul>

{!! $__view->view('list')
     ->set('level', 0)
     ->set('pages', $pages)
     ->set('categories', $categories)
     ->set('group', $group)
     ->set('config', $config)
     ->display() !!}
