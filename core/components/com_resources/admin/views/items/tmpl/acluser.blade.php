{{--
  Resource ACL User — Single user row template (AJAX)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<li id="acluser_{{ $id }}">
  <a class="state trash"
     data-parent="acluser_{{ $id }}"
     href="#"
     data-action="remove-acluser">
    <span>{{ Lang::txt('JACTION_DELETE') }}</span>
  </a>
  {{ $name }} ({{ $id }})
  <input type="hidden" class="acluserid" name="{{ $id }}acluserid" value="{{ $id }}" />
  <input type="hidden" name="{{ $id }}_name" value="{{ $name }}" />
</li>
