{{--
  Latest Users module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if (!empty($names))
  <ul class="list bg-base-100 rounded-box{{ $moduleclass_sfx }}">
    @foreach ($names as $name)
      <li class="list-row py-1">{{ $name->username }}</li>
    @endforeach
  </ul>
@endif
