{{--
  Breadcrumbs module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="breadcrumbs text-sm{{ $moduleclass_sfx }}" aria-label="{{ Lang::txt('MOD_BREADCRUMBS') }}">
  <ul>
    @php
      // Remove duplicate entries (multilanguage edge case)
      for ($i = 1; $i < $count; $i++) {
          if (!empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link == $list[$i - 1]->link) {
              unset($list[$i]);
          }
      }
      end($list);
      $lastKey = key($list);
    @endphp
    @foreach ($list as $key => $item)
      @if ($key != $lastKey)
        <li>
          @if (!empty($item->link))
            <a href="{{ $item->link }}">{{ html_entity_decode($item->name) }}</a>
          @else
            <span>{{ $item->name }}</span>
          @endif
        </li>
      @elseif ($params->get('showLast', 1))
        <li aria-current="page">{{ $item->name }}</li>
      @endif
    @endforeach
  </ul>
</div>
