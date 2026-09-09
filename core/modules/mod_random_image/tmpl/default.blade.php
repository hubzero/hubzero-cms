{{--
  Random Image module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="random-image{{ $moduleclass_sfx }}">
  @if ($link)
    <a href="{{ $link }}">
  @endif
  {!! Html::asset('image', $image->folder . '/' . $image->name, $image->name,
      ['width' => $image->width, 'height' => $image->height]) !!}
  @if ($link)
    </a>
  @endif
</div>
