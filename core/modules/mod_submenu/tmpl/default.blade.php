{{--
  mod_submenu — component sub-navigation tabs

  Renders the component submenu as a horizontal tab bar.
  Supports primary submenu and optional sub-submenu levels.

  Variables: $list, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $hide = Request::getInt('hidemainmenu');

  $subsubItems = [];
  if (App::has('subsubmenu')) {
      $subsubItems = App::get('subsubmenu')->getItems();
  }
  if (!is_array($subsubItems)) {
      $subsubItems = [];
  }
@endphp

<div role="tablist" class="tabs tabs-border flex-nowrap overflow-x-auto">
  @foreach ($list as $item)
    @php
      $label  = $item[0];
      $link   = $item[1] ?? '';
      $active = isset($item[2]) && $item[2] == 1;
    @endphp
    @if ($hide)
      <span class="tab {{ $active ? 'tab-active font-medium' : 'opacity-50' }}">
        {!! $label !!}
      </span>
    @elseif (strlen($link))
      <a href="{{ str_replace('&amp;', '&', $link) }}"
         class="tab {{ $active ? 'tab-active font-medium' : '' }}">
        {!! $label !!}
      </a>
    @else
      <span class="tab">{!! $label !!}</span>
    @endif
  @endforeach
</div>

@if (count($subsubItems))
  <div role="tablist" class="tabs tabs-border tabs-xs mt-1 flex-nowrap overflow-x-auto">
    @foreach ($subsubItems as $item)
      @php
        $label  = $item[0];
        $link   = $item[1] ?? '';
        $active = isset($item[2]) && $item[2] == 1;
      @endphp
      @if ($hide)
        <span class="tab {{ $active ? 'tab-active font-medium' : 'opacity-50' }}">
          {!! $label !!}
        </span>
      @elseif (strlen($link))
        <a href="{{ str_replace('&amp;', '&', $link) }}"
           class="tab {{ $active ? 'tab-active font-medium' : '' }}">
          {!! $label !!}
        </a>
      @else
        <span class="tab">{!! $label !!}</span>
      @endif
    @endforeach
  </div>
@endif
