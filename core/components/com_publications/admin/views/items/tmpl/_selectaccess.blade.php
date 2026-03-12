{{--
  Publications access level select partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php $accessLevels = explode(',', $as); @endphp
<select name="access" id="access" class="select select-bordered select-sm w-full">
  @foreach($accessLevels as $idx => $level)
    <option value="{{ $idx }}" {{ $value == $idx ? 'selected' : '' }}>
      {{ trim($level) }}
    </option>
  @endforeach
</select>
