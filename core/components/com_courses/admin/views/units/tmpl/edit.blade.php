{{--
  Courses: Units — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
  $__view->js();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_UNITS') . ': ' . $text }}"
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
          label="{{ Lang::txt('COM_COURSES_FIELD_OFFERING') }}"
          inputId="offering_id"
      >
        @php
          $model = \Components\Courses\Models\Courses::getInstance();
        @endphp
        <select name="fields[offering_id]" id="offering_id" class="select select-bordered select-sm w-full">
          <option value="-1">{{ Lang::txt('COM_COURSES_SELECT') }}</option>
          @if($model->courses()->total() > 0)
            @foreach($model->courses() as $course)
              <optgroup label="{{ $course->get('alias') }}">
                @foreach($course->offerings() as $offeringItem)
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
          label="{{ Lang::txt('COM_COURSES_FIELD_ALIAS') }}"
          inputId="field-alias"
          hint="{{ Lang::txt('COM_COURSES_FIELD_ALIAS_HINT') }}"
      >
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered input-sm w-full"
               value="{{ $row->get('alias') }}" />
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
               value="{{ $row->get('title') }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_DESCRIPTION') }}"
          inputId="field-description"
      >
        <textarea name="fields[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="10">{{ $row->get('description') }}</textarea>
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_ASSETS') }}">
      @if($row->get('id'))
        @php
          $assetsUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=assets&tmpl=component&scope=unit&scope_id=' . $row->get('id')
              . '&course_id=' . $offering->get('course_id'), false
          );
        @endphp
        <iframe height="400"
                name="assets"
                id="assets"
                title="{{ Lang::txt('COM_COURSES_ASSETS') }}"
                class="w-full border border-base-300 rounded-box"
                src="{{ $assetsUrl }}"></iframe>
      @else
        <div class="alert alert-info">
          {{ Lang::txt('COM_COURSES_ENTRY_MUST_BE_SAVED_BEFORE_ASSETS') }}
        </div>
      @endif
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
            <th>{{ Lang::txt('COM_COURSES_FIELD_ORDERING') }}</th>
            <td>{{ $row->get('ordering') }}</td>
          </tr>
          @if($row->get('created'))
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_CREATED') }}</th>
              <td>
                <time datetime="{{ $row->get('created') }}">
                  {{ Date::of($row->get('created'))->toLocal() }}
                </time>
              </td>
            </tr>
          @endif
          @if($row->get('created_by'))
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_CREATOR') }}</th>
              <td>{{ User::getInstance($row->get('created_by'))->get('name') }}</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PUBLISHING') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_STATE') }}"
          inputId="field-state"
      >
        <select name="fields[state]" id="field-state" class="select select-bordered select-sm w-full">
          <option value="0" @selected($row->get('state') == 0)>
            {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
          </option>
          <option value="1" @selected($row->get('state') == 1)>
            {{ Lang::txt('COM_COURSES_PUBLISHED') }}
          </option>
          <option value="2" @selected($row->get('state') == 2)>
            {{ Lang::txt('COM_COURSES_TRASHED') }}
          </option>
        </select>
      </x-form-field>
    </x-form-section>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="offering" value="{{ $offering->get('id') }}" />
</x-admin-edit>
