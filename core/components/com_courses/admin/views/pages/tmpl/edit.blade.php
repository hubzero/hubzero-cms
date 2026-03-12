{{--
  Course Pages — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_PAGES') . ': ' . $text,
      'courses'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('page');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
    <x-form-section title="{{ Lang::txt('JDETAILS') }}">
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
               value="{{ $row->get('title') }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_ALIAS') }}"
          inputId="field-url"
          hint="{{ Lang::txt('COM_COURSES_FIELD_ALIAS_HINT') }}"
      >
        <input type="text"
               name="fields[url]"
               id="field-url"
               class="input input-bordered input-sm w-full"
               value="{{ $row->get('url') }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_CONTENT') }}"
          inputId="field-content"
      >
        {!! $__view->editor(
            'fields[content]',
            e($row->content('raw')),
            50,
            30,
            'field-content'
        ) !!}
      </x-form-field>
    </x-form-section>

  @slot('sidebar')
    @if($row->get('id'))
      <div class="bg-base-200/50 rounded-box p-4 mb-4">
        <table class="meta-table">
          <tbody>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_TYPE') }}</th>
              <td>
                @if($row->get('course_id'))
                  @if($row->get('offering_id'))
                    {{ Lang::txt('COM_COURSES_PAGES_OFFERING') }}
                  @else
                    {{ Lang::txt('COM_COURSES_PAGES_COURSE') }}
                  @endif
                @else
                  {{ Lang::txt('COM_COURSES_PAGES_USER_GUIDE') }}
                @endif
              </td>
            </tr>
            @if($row->get('course_id'))
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_COURSE_ID') }}</th>
                <td>{{ $row->get('course_id') }}</td>
              </tr>
            @endif
            @if($row->get('offering_id'))
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_OFFERING_ID') }}</th>
                <td>{{ $row->get('offering_id') }}</td>
              </tr>
            @endif
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_ID') }}</th>
              <td>{{ $row->get('id') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PUBLISHING') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_ACTIVE') }}"
          inputId="field-active"
      >
        <select name="fields[active]" id="field-active" class="select select-bordered select-sm w-full">
          <option value="1" @selected($row->get('active'))>
            {{ Lang::txt('JYES') }}
          </option>
          <option value="0" @selected(!$row->get('active'))>
            {{ Lang::txt('JNO') }}
          </option>
        </select>
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_IMAGE') }}">
      @if(!$row->get('id'))
        <div class="alert alert-warning">
          {{ Lang::txt('COM_COURSES_UPLOAD_ADDED_LATER') }}
        </div>
      @else
        @php
          $filesUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=pages&task=files&tmpl=component&listdir='
              . $row->get('offering_id') . '&course=' . $course->get('id'), false
          );
        @endphp
        <iframe width="100%"
                height="300"
                name="filelist"
                id="filelist"
                title="{{ Lang::txt('COM_COURSES_FILES') }}"
                class="border border-base-300 rounded-box"
                src="{{ $filesUrl }}"></iframe>
      @endif
    </x-form-section>
  @endslot

  <input type="hidden" name="course" value="{{ $course->get('id') }}" />
  <input type="hidden" name="offering" value="{{ $offering->get('id') }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[course_id]" value="{{ $course->get('id') }}" />
  <input type="hidden" name="fields[offering_id]" value="{{ $row->get('offering_id') }}" />
</x-admin-edit>
