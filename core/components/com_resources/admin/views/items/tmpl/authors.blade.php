{{--
  Resource Authors — Widget (embedded in resource edit)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('authors.blade.js');
  $authIDs = [];
@endphp

<label for="authid">{{ Lang::txt('COM_RESOURCES_AUTHID') }}</label>
<input type="text" name="authid" id="authid" class="input input-bordered input-sm w-full mb-2" value="" />

<div class="flex gap-2 mb-2">
  <div class="flex-1">
    <select name="authrole" id="authrole" class="select select-bordered select-sm w-full" aria-label="{{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }}">
      <option value="">{{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }}</option>
      @if($roles)
        @foreach($roles as $role)
          <option value="{{ $role->alias }}">{{ $role->title }}</option>
        @endforeach
      @endif
    </select>
  </div>
  <div>
    <button type="button"
            name="addel"
            id="addel"
            class="btn btn-sm btn-primary"
            data-action="add-author">
      {{ Lang::txt('Add') }}
    </button>
  </div>
</div>

<ul id="author-list">
  @if($authnames)
    @foreach($authnames as $authname)
      @php
        if ($authname->name) {
            $name = $authname->name;
        } else {
            $name = $authname->givenName . ' ';
            if ($authname->middleName != null) {
                $name .= $authname->middleName . ' ';
            }
            $name .= $authname->surname;
        }
        $authIDs[] = $authname->authorid;
        $org = $authname->organization
            ? e($authname->organization)
            : ($attribs->get($authname->authorid, '') ?? '');
      @endphp
      <li id="author_{{ $authname->authorid }}">
        <span class="handle">{{ Lang::txt('COM_RESOURCES_AUTHOR_DRAG') }}</span>
        <a class="state trash"
           data-parent="author_{{ $authname->authorid }}"
           href="#"
           data-action="remove-author">
          <span>{{ Lang::txt('JACTION_DELETE') }}</span>
        </a>
        {{ $name }} ({{ $authname->authorid }})
        <br />{{ Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION') }}:
        <input type="text"
               name="{{ $authname->authorid }}_organization"
               aria-label="{{ Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION') }} — {{ $name }}"
               value="{{ $org }}" />

        <select name="{{ $authname->authorid }}_role" aria-label="{{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }} — {{ $name }}">
          <option value="" @selected($authname->role == '')>
            {{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }}
          </option>
          @if($roles)
            @foreach($roles as $role)
              <option value="{{ $role->alias }}" @selected($authname->role == $role->alias)>
                {{ $role->title }}
              </option>
            @endforeach
          @endif
        </select>
        <input type="hidden"
               name="{{ $authname->authorid }}_name"
               value="{{ $name }}" />
      </li>
    @endforeach
  @endif
</ul>

<input type="hidden" name="old_authors" id="old_authors" value="{{ implode(',', $authIDs) }}" />
<input type="hidden" name="new_authors" id="new_authors" value="{{ implode(',', $authIDs) }}" />
