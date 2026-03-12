{{--
  Courses: Students — Admin add view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();

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

@php
  Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('JACTION_CREATE') . ' ' . Lang::txt('COM_COURSES_STUDENTS'), 'courses');
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
          label="{{ Lang::txt('COM_COURSES_FIELD_USER') }}"
          inputId="acmembers"
          hint="{{ Lang::txt('COM_COURSES_FIELD_USER_HINT') }}"
      >
        @php
          $mc = Event::trigger('hubzero.onGetMultiEntry', [
              ['members', 'fields[user_id]', 'acmembers', '', '']
          ]);
        @endphp
        @if(count($mc) > 0)
          {!! $mc[0] !!}
        @else
          <input type="text"
                 name="fields[user_id]"
                 id="acmembers"
                 class="input input-bordered input-sm w-full"
                 value="" />
        @endif
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_OFFERING') }}"
          inputId="offering_id"
      >
        <select name="fields[offering_id]" id="offering_id" class="select select-bordered select-sm w-full">
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
                          @selected($offeringItem->get('id') == $offering->get('id'))>
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
          inputId="section_id"
      >
        <select name="fields[section_id]" id="section_id" class="select select-bordered select-sm w-full">
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
          inputId="enrolled"
      >
        {!! Html::input('calendar', 'fields[enrolled]', $row->get('enrolled'), ['id' => 'enrolled']) !!}
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
        </tbody>
      </table>
    </div>
  @endslot

  <input type="hidden" name="offering" value="{{ $offering->get('id') }}" />
  <input type="hidden" name="fields[role_id]" value="{{ $row->get('role_id', $role_id) }}" />
  <input type="hidden" name="fields[course_id]" value="{{ $course->get('id') }}" />
  <input type="hidden" name="fields[student]" value="1" />
</x-admin-edit>

<template id="offering-data">
    {
        "data": {!! json_encode($sectionData) !!}
    }
</template>
