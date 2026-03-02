{{--
  Menu module — daisyUI layout.

  Renders the flat menu item list as nested daisyUI menu markup.
  Items with children use <details>/<summary> for dropdowns.

  Variables from Menu::display():
    $list           — flat array of menu item objects (with level/deeper/shallower)
    $active_id      — ID of the currently active menu item
    $path           — array of IDs in the active tree
    $showAll        — show all children (bool)
    $class_sfx      — class suffix from params
    $params         — module params (Registry)
    $module         — module DB row

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@foreach($list as $item)
  @php
    $isActive = ($item->id == $active_id);
    $inPath   = in_array($item->id, $path);
    $isSep    = ($item->type === 'separator');
    $hasKids  = !empty($item->deeper);
  @endphp

  @if($isSep)
    {{-- skip separators --}}
  @elseif($hasKids)
    <li>
      <details>
        <summary @class(['active' => $isActive, 'font-semibold' => $inPath])>
          {{ $item->title }}
        </summary>
        <ul>
  @else
    <li>
      <a href="{{ $item->flink }}"
         @class(['active' => $isActive, 'font-semibold' => $inPath])
         @if($isActive) aria-current="page" @endif
         @if(!empty($item->anchor_title)) title="{{ $item->anchor_title }}" @endif
         @if(($item->browserNav ?? 0) == 1) target="_blank" rel="noopener" @endif
      >{{ $item->title }}</a>
    </li>
  @endif

  @if(!empty($item->shallower))
    @for($i = 0; $i < $item->level_diff; $i++)
        </ul>
      </details>
    </li>
    @endfor
  @endif
@endforeach
