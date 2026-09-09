{{--
  Articles News item sub-template — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php $item_heading = $params->get('item_heading', 'h4'); @endphp
@if ($params->get('item_title'))
  <{{ $item_heading }} class="font-semibold">
    @if ($params->get('link_titles') && $item->link != '')
      <a href="{{ $item->link }}" class="link link-hover">{{ $item->title }}</a>
    @else
      {{ $item->title }}
    @endif
  </{{ $item_heading }}>
@endif

@if (!$params->get('intro_only'))
  {!! $item->afterDisplayTitle !!}
@endif

{!! $item->beforeDisplayContent !!}
{!! $item->introtext !!}

@if (isset($item->link) && $item->readmore != 0 && $params->get('readmore'))
  <a class="link link-primary text-sm" href="{{ $item->link }}">{{ $item->linkText }}</a>
@endif
