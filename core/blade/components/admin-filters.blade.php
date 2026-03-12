{{--
  Admin filter bar content layout.

  Search on the left, filter dropdowns on the right.
  Goes inside the `filters` slot of <x-admin-form>.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="admin-filters flex flex-wrap items-center gap-2 w-full">
  @if(isset($search) && $search->isNotEmpty())
    <div class="flex items-center gap-2">
      {{ $search }}
    </div>
  @endif

  <div class="flex items-center gap-2 ml-auto">
    {{ $slot }}
  </div>
</div>
