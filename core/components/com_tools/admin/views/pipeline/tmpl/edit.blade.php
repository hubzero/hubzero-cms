{{--
  Tools Pipeline — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . $text, 'tools');
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('tool');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="admin-field">
      <label for="field-title" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TITLE') }}</label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             value="{{ $row->title }}" />
    </div>

    <div class="admin-field">
      <label for="field-ticketid" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TICKETID') }}</label>
      <input type="text"
             name="fields[ticketid]"
             id="field-ticketid"
             class="input input-bordered w-full"
             value="{{ $row->ticketid }}" />
    </div>

    <div class="admin-field">
      <label for="field-state" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_STATE') }}</label>
      <select name="fields[state]" id="field-state" class="select select-bordered w-full">
        <option value="0" @selected($row->state == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($row->state == 1)>{{ Lang::txt('COM_TOOLS_REGISTERED') }}</option>
        <option value="2" @selected($row->state == 2)>{{ Lang::txt('COM_TOOLS_CREATED') }}</option>
        <option value="3" @selected($row->state == 3)>{{ Lang::txt('COM_TOOLS_UPLOADED') }}</option>
        <option value="4" @selected($row->state == 4)>{{ Lang::txt('COM_TOOLS_INSTALLED') }}</option>
        <option value="5" @selected($row->state == 5)>{{ Lang::txt('COM_TOOLS_UPDATED') }}</option>
        <option value="6" @selected($row->state == 6)>{{ Lang::txt('COM_TOOLS_APPROVED') }}</option>
        <option value="7" @selected($row->state == 7)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="8" @selected($row->state == 8)>{{ Lang::txt('COM_TOOLS_RETIRED') }}</option>
        <option value="9" @selected($row->state == 9)>{{ Lang::txt('COM_TOOLS_ABANDONED') }}</option>
      </select>
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_ID') }}</td>
          <td>{{ $row->id }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_NAME') }}</td>
          <td>{{ $row->toolname }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_REGISTERED') }}</td>
          <td>{{ $row->registered }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_REGISTERED_BY') }}</td>
          <td>{{ $row->registered_by }}</td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  <input type="hidden" name="id"         value="{{ $row->id }}" />
  <input type="hidden" name="task"       value="save" />
</x-admin-edit>
