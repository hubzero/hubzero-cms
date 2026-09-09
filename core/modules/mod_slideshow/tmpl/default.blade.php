{{--
  mod_slideshow -- image slideshow fallback display

  Variables: $noflash_file, $noflash_link, $width, $height

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div id="xflash-$container">
  @if ($noflash_link)
    <a href="{{ $noflash_link }}">
  @endif
      <img src="{{ $noflash_file }}"
           width="{{ $width }}"
           height="{{ $height }}"
           id="noflashimg"
           class="rounded-box"
           alt="" />
  @if ($noflash_link)
    </a>
  @endif
</div>
