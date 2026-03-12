{{--
  Resource ACL Users — Widget (embedded in resource edit)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('acl.blade.js');
  $acluserIDs = [];
@endphp

<div class="flex gap-2 mb-2">
  <div class="flex-1">
    <label for="acluserid" class="label text-sm">User ID, name, or username:</label>
    <input type="text" name="acluserid" id="acluserid" class="input input-bordered input-sm w-full" value="" />
  </div>
  <div class="self-end">
    <button type="button"
            name="addacluser"
            id="addacluser"
            class="btn btn-sm btn-primary"
            data-action="add-acluser">
      Add
    </button>
  </div>
</div>

<ul id="acluser-list">
  @if($aclusernames)
    @foreach($aclusernames as $aclusername)
      @php
        if ($aclusername->name) {
            $name = $aclusername->name;
        } else {
            $name = $aclusername->givenName . ' ';
            if ($aclusername->middleName != null) {
                $name .= $aclusername->middleName . ' ';
            }
            $name .= $aclusername->surname;
        }
        $acluserIDs[] = $aclusername->user_id;
      @endphp
      <li id="acluser_{{ $aclusername->user_id }}">
        <a class="state trash"
           data-parent="acluser_{{ $aclusername->user_id }}"
           href="#"
           data-action="remove-acluser">
          <span>{{ Lang::txt('JACTION_DELETE') }}</span>
        </a>
        {{ $name }} ({{ $aclusername->user_id }})
        <input type="hidden"
               name="{{ $aclusername->user_id }}_name"
               value="{{ $name }}" />
      </li>
    @endforeach
  @endif
</ul>

<input type="hidden" name="old_aclusers" id="old_aclusers" value="{{ implode(',', $acluserIDs) }}" />
<input type="hidden" name="new_aclusers" id="new_aclusers" value="{{ implode(',', $acluserIDs) }}" />
