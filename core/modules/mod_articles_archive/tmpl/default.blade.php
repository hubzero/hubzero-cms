{{--
  Articles Archive module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if (!empty($list))
  <ul class="menu menu-sm bg-base-100 rounded-box{{ $moduleclass_sfx }}">
    @foreach ($list as $item)
      <li>
        <a href="{{ $item->link }}">{{ $item->text }}</a>
      </li>
    @endforeach
  </ul>
@endif
