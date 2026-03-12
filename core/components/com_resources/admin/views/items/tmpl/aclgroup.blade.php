{{--
  Resource ACL Group — Single group row template (AJAX)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<li id="aclgroup_{{ $id }}">
  <a class="state trash"
     data-parent="aclgroup_{{ $id }}"
     href="#"
     data-action="remove-aclgroup">
    <span>{{ Lang::txt('JACTION_DELETE') }}</span>
  </a>
  {{ $name }} ({{ $id }})
  <input type="hidden" class="aclgroupid" name="{{ $id }}aclgroupid" value="{{ $id }}" />
  <input type="hidden" name="{{ $id }}_name" value="{{ $name }}" />
</li>
