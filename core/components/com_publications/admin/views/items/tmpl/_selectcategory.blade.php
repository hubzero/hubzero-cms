{{--
  Publications category select partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<select name="{{ $name }}"
        id="{{ $name }}"
        class="select select-bordered select-sm {{ $attributes ?? '' }}"
        {{ isset($submitOnChange) && $submitOnChange ? 'data-submit-on-change' : '' }}>
  @if(isset($showNone) && $showNone !== '')
    <option value="" {{ (!$value || $value == '') ? 'selected' : '' }}>
      {{ $showNone }}
    </option>
  @endif
  @foreach($categories as $cat)
    <option value="{{ $cat->id }}"
            {{ ($value && ($cat->id == $value || $cat->name == $value)) ? 'selected' : '' }}>
      {{ $cat->name }}
    </option>
  @endforeach
</select>
