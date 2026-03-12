{{--
  Publications license select partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php $selectedId = isset($selected) ? $selected->id : 1; @endphp
<select name="license_type"
        id="license_type"
        class="select select-bordered select-sm w-full">
  @foreach($licenses as $license)
    <option value="{{ $license->id }}" {{ $selectedId == $license->id ? 'selected' : '' }}>
      {{ trim(e($license->name)) }}
    </option>
  @endforeach
</select>
