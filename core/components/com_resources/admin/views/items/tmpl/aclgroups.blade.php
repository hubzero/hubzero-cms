{{--
  Resource ACL Groups — Widget (embedded in resource edit)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('acl.blade.js');
  $aclgroupIDs = [];
@endphp

<div class="flex gap-2 mb-2">
  <div class="flex-1">
    <label for="aclgroupid" class="label text-sm">Group ID or name:</label>
    <input type="text" name="aclgroupid" id="aclgroupid" class="input input-bordered input-sm w-full" value="" />
  </div>
  <div class="self-end">
    <button type="button"
            name="addaclgroup"
            id="addaclgroup"
            class="btn btn-sm btn-primary"
            data-action="add-aclgroup">
      Add
    </button>
  </div>
</div>

<ul id="aclgroup-list">
  @if($aclgroupnames)
    @foreach($aclgroupnames as $aclgroupname)
      @php
        $name = $aclgroupname->name ?? '';
        $aclgroupIDs[] = $aclgroupname->group_id;
      @endphp
      <li id="aclgroup_{{ $aclgroupname->group_id }}">
        <a class="state trash"
           data-parent="aclgroup_{{ $aclgroupname->group_id }}"
           href="#"
           data-action="remove-aclgroup">
          <span>{{ Lang::txt('JACTION_DELETE') }}</span>
        </a>
        {{ $name }} ({{ $aclgroupname->group_id }})
        <input type="hidden"
               name="{{ $aclgroupname->group_id }}_name"
               value="{{ $name }}" />
      </li>
    @endforeach
  @endif
</ul>

<input type="hidden" name="old_aclgroups" id="old_aclgroups" value="{{ implode(',', $aclgroupIDs) }}" />
<input type="hidden" name="new_aclgroups" id="new_aclgroups" value="{{ implode(',', $aclgroupIDs) }}" />
