{{--
  Courses: Roles — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
  $__view->js();
@endphp

@php
  Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ROLES') . ': ' . $text, 'courses');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('courses');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
    <x-form-section title="{{ Lang::txt('JDETAILS') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_OFFERING') }}"
          inputId="field-offering_id"
      >
        <select name="fields[offering_id]" id="field-offering_id" class="select select-bordered select-sm w-full">
          <option value="0" @selected(0 == $row->offering_id)>
            {{ Lang::txt('COM_COURSES_NONE') }}
          </option>
          @foreach($courses as $course)
            <optgroup label="{{ $course->get('alias') }}">
              @foreach($course->offerings() as $offering)
                <option value="{{ $offering->get('id') }}" @selected($offering->get('id') == $row->offering_id)>
                  {{ $offering->get('title') }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_TITLE') }}"
          inputId="field-title"
          required
      >
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               required
               value="{{ $row->title }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_ALIAS') }}"
          inputId="field-alias"
          hint="{{ Lang::txt('COM_COURSES_FIELD_ALIAS_HINT') }}"
      >
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered input-sm w-full"
               value="{{ $row->alias }}" />
      </x-form-field>
    </x-form-section>

  @slot('sidebar')
    <div class="bg-base-200/50 rounded-box p-4 mb-4">
      <table class="meta-table">
        <tbody>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_OFFERING') }}</th>
            <td>{{ $row->offering_id }}</td>
          </tr>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_ID') }}</th>
            <td>{{ $row->id }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
</x-admin-edit>
