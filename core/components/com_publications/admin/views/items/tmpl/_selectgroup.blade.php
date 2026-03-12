{{--
  Publications group owner select partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php use Hubzero\Facades\Lang; @endphp
<select name="group_owner"
        id="group_owner"
        class="select select-bordered select-sm w-full"
        {{ (!$groups || $groupOwner) ? 'disabled' : '' }}>
  <option value="">{{ Lang::txt('Select group ...') }}</option>
  @if($groups)
    @foreach($groups as $group)
      <option value="{{ $group->gidNumber }}" {{ $value == $group->gidNumber ? 'selected' : '' }}>
        {{ \Hubzero\Utility\Str::truncate(e($group->description), 60) }}
      </option>
    @endforeach
  @endif
</select>
