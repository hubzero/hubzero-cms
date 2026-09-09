{{--
  Popular Articles module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<ul class="menu menu-sm bg-base-100 rounded-box{{ $moduleclass_sfx }}">
  @foreach ($list as $item)
    <li>
      <a href="{{ $item->link }}">{{ $item->title }}</a>
    </li>
  @endforeach
</ul>
