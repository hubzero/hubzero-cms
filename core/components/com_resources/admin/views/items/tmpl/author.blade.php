{{--
  Resource Author — Single author row template (AJAX)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<li id="author_{{ $id }}">
  <span class="handle">{{ Lang::txt('COM_RESOURCES_AUTHOR_DRAG') }}</span>
  <a class="state trash"
     data-parent="author_{{ $id }}"
     href="#"
     data-action="remove-author">
    <span>{{ Lang::txt('JACTION_DELETE') }}</span>
  </a>
  {{ $name }} ({{ $id }})
  <br />{{ Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION') }}:
  <input type="text"
         name="{{ $id }}_organization"
         value="{{ $org }}" />

  <select name="{{ $id }}_role">
    <option value="" @selected(empty($role))>
      {{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }}
    </option>
    @if($roles)
      @foreach($roles as $r)
        <option value="{{ $r->alias }}" @selected(isset($role) && $role == $r->alias)>
          {{ $r->title }}
        </option>
      @endforeach
    @endif
  </select>
  <input type="hidden" class="authid" name="{{ $id }}authid" value="{{ $id }}" />
  <input type="hidden" name="{{ $id }}_name" value="{{ $name }}" />
</li>
