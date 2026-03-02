{{--
  Search module — clean daisyUI input for the navbar.

  Variables from module class:
    $text       — placeholder text
    $label      — accessible label
    $width      — input size (unused in blade, CSS controls width)
    $instances  — instance count (for unique IDs)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sfx    = $instances > 1 ? $module->id : '';
  $action = Route::url('index.php?option=com_search');
@endphp
<form action="{{ $action }}" method="get" id="searchform{{ $sfx }}">
  <label for="searchword{{ $sfx }}" class="sr-only">{{ $label }}</label>
  <input type="search"
         name="terms"
         id="searchword{{ $sfx }}"
         class="input input-bordered input-sm w-full"
         placeholder="{{ $text }}" />
</form>
