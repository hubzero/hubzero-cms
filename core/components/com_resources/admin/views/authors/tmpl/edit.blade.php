{{--
  Resource Author — Admin edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Resources\Helpers\Permissions::getActions('contributor');
  $text  = Lang::txt('JACTION_EDIT');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_AUTHORS') }}: {{ $text }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="overflow-x-auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_RESOURCES_COL_RESOURCE') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_COL_NAME') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_COL_ORGANIZATION') }}</th>
              <th>{{ Lang::txt('COM_RESOURCES_COL_ROLE') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $i => $row)
              <tr>
                <td>
                  <input type="text"
                         name="fields[{{ $i }}][subid]"
                         class="input input-bordered input-sm w-20"
                         maxlength="250"
                         value="{{ $row->subid }}" />
                  <input type="hidden" name="fields[{{ $i }}][ordering]" value="{{ $row->ordering }}" />
                  <input type="hidden" name="fields[{{ $i }}][subtable]" value="{{ $row->subtable }}" />
                  <input type="hidden" name="fields[{{ $i }}][authorid]" value="{{ $authorid }}" />
                  <input type="hidden" name="fields[{{ $i }}][id]" value="{{ $row->id }}" />
                </td>
                <td>
                  <input type="text"
                         name="fields[{{ $i }}][name]"
                         class="input input-bordered input-sm w-full"
                         maxlength="250"
                         value="{{ $row->name }}" />
                </td>
                <td>
                  <input type="text"
                         name="fields[{{ $i }}][organization]"
                         class="input input-bordered input-sm w-full"
                         maxlength="250"
                         value="{{ $row->organization }}" />
                </td>
                <td>
                  <select name="fields[{{ $i }}][role]" class="select select-bordered select-sm w-full">
                    <option value="" @selected($row->role == '')>{{ Lang::txt('COM_RESOURCES_ROLE_AUTHOR') }}</option>
                    @if($roles)
                      @foreach($roles as $role)
                        <option value="{{ $role->alias }}" @selected($row->role == $role->alias)>
                          {{ $role->title }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_FIELDSET_AUTHOR') }}">
        <div class="admin-field">
          <label for="field-authorid" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_ID') }}</label>
          <input type="text"
                 name="authorid"
                 id="field-authorid"
                 class="input input-bordered w-full"
                 value="{{ $authorid }}" />
        </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="id" value="{{ $authorid }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
