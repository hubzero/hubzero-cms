{{--
  Courses: Students — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $profile = User::getInstance($row->get('user_id'));

  // Determine the student role ID
  $role_id = 0;
  $roles = $offering->roles();
  foreach ($roles as $role) {
      if ($role->alias == 'student') {
          $role_id = $role->id;
          break;
      }
  }

  // Build offering/section data for dynamic section dropdown
  $model = \Components\Courses\Models\Courses::getInstance();
  $sectionData = [];
  $j = 0;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('JACTION_EDIT') . ' ' . Lang::txt('COM_COURSES_STUDENT') }}"
    icon="courses"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
    <x-form-section title="{{ Lang::txt('JDETAILS') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_OFFERING') }}"
          inputId="field-offering_id"
          required
      >
        <select name="fields[offering_id]"
                id="field-offering_id"
                class="select select-bordered select-sm w-full"
                required>
          <option value="-1">{{ Lang::txt('COM_COURSES_NONE') }}</option>
          @if($model->courses()->total() > 0)
            @foreach($model->courses() as $courseItem)
              <optgroup label="{{ $courseItem->get('alias') }}">
                @foreach($courseItem->offerings() as $offeringItem)
                  @php
                    foreach ($offeringItem->sections() as $section) {
                        $sectionData[$j++] = [
                            $offeringItem->get('id'),
                            $section->get('id'),
                            $section->get('title'),
                        ];
                    }
                  @endphp
                  <option value="{{ $offeringItem->get('id') }}"
                          @selected($offeringItem->get('id') == $row->get('offering_id'))>
                    {{ $offeringItem->get('alias') }}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          @endif
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_SECTION') }}"
          inputId="field-section_id"
      >
        <select name="fields[section_id]" id="field-section_id" class="select select-bordered select-sm w-full">
          <option value="-1">{{ Lang::txt('COM_COURSES_SELECT') }}</option>
          @foreach($offering->sections() as $section)
            <option value="{{ $section->get('id') }}"
                    @selected($section->get('id') == $row->get('section_id'))>
              {{ $section->get('title') }}
            </option>
          @endforeach
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_ENROLLED') }}"
          inputId="field-enrolled"
      >
        {!! Html::input('calendar', 'fields[enrolled]', $row->get('enrolled'), ['id' => 'field-enrolled']) !!}
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_SERIAL_NUM') }}"
          inputId="field-token"
      >
        <input type="text"
               name="fields[token]"
               id="field-token"
               class="input input-bordered input-sm w-full"
               value="{{ $row->get('token') }}" />
      </x-form-field>
    </x-form-section>

  @slot('sidebar')
    <div class="bg-base-200/50 rounded-box p-4 mb-4">
      <table class="meta-table">
        <tbody>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_ID') }}</th>
            <td>{{ $row->get('id') }}</td>
          </tr>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_USER_ID') }}</th>
            <td>{{ $row->get('user_id') }}</td>
          </tr>
          @if($profile)
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_NAME') }}</th>
              <td>{{ $profile->get('name') }}</td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_USERNAME') }}</th>
              <td>{{ $profile->get('username') }}</td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_EMAIL') }}</th>
              <td>{{ $profile->get('email') }}</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  @endslot

  <input type="hidden" name="offering" value="{{ $row->get('offering_id') }}" />
  <input type="hidden" name="section" value="{{ $row->get('section_id') }}" />
  <input type="hidden" name="fields[role_id]" value="{{ $row->get('role_id') }}" />
  <input type="hidden" name="fields[user_id]" value="{{ $row->get('user_id') }}" />
</x-admin-edit>

<template id="offering-data">
    {
        "data": {!! json_encode($sectionData) !!}
    }
</template>
