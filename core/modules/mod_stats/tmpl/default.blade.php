{{--
  Stats module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="space-y-1{{ $moduleclass_sfx }}">
  @foreach ($list as $item)
    <div class="flex justify-between text-sm">
      <span class="text-base-content/70">{{ $item->title }}</span>
      <span class="font-medium">{{ $item->data }}</span>
    </div>
  @endforeach
</div>
