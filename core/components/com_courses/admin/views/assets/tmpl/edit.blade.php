{{--
  Courses: Assets — Admin iframe edit view (tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js();

  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $id = $row->get('id') ?: ('tmp' . time() . rand(0, 10000));

  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );

  $mediaUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=media&tmpl=component&listdir=' . $id
      . '&course=' . e($course_id), false
  );

  $types = [
      'video' => Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO'),
      'file'  => Lang::txt('COM_COURSES_ASSET_TYPE_FILE'),
      'form'  => Lang::txt('COM_COURSES_ASSET_TYPE_FORM'),
      'text'  => Lang::txt('COM_COURSES_ASSET_TYPE_TEXT'),
      'url'   => Lang::txt('COM_COURSES_ASSET_TYPE_URL'),
  ];

  $subtypes = [
      'video'    => Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO'),
      'embedded' => Lang::txt('COM_COURSES_ASSET_TYPE_EMBEDDED'),
      'file'     => Lang::txt('COM_COURSES_ASSET_TYPE_FILE'),
      'exam'     => Lang::txt('COM_COURSES_ASSET_TYPE_EXAM'),
      'quiz'     => Lang::txt('COM_COURSES_ASSET_TYPE_QUIZ'),
      'homework' => Lang::txt('COM_COURSES_ASSET_TYPE_HOMEWORK'),
      'note'     => Lang::txt('COM_COURSES_ASSET_TYPE_NOTE'),
      'wiki'     => Lang::txt('COM_COURSES_ASSET_TYPE_WIKI'),
  ];
@endphp

@if($errors = $__view->getErrors())
  <div class="alert alert-error mb-2">
    {!! implode('<br />', $errors) !!}
  </div>
@endif

<form action="{{ $routeUrl }}"
      method="post"
      name="adminForm"
      id="component-form"
      enctype="multipart/form-data"
      class="p-2">

  {{-- Iframe toolbar --}}
  <div class="flex items-center justify-between bg-base-200 rounded-box px-3 py-2 mb-4">
    <span class="font-medium text-sm">{{ $text }}</span>
    <div class="flex gap-2">
      <button type="button" id="btn-save" class="btn btn-sm btn-primary">
        {{ Lang::txt('COM_COURSES_SAVE') }}
      </button>
      <button type="button" id="btn-cancel" class="btn btn-sm btn-ghost">
        {{ Lang::txt('JCANCEL') }}
      </button>
    </div>
  </div>

  {{-- Details --}}
  <fieldset>
    <legend class="font-semibold text-sm mb-2">{{ Lang::txt('JDETAILS') }}</legend>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <div class="form-control w-full">
        <label class="label text-base-content" for="field-type">
          <span class="label-text">{{ Lang::txt('COM_COURSES_FIELD_TYPE') }}</span>
        </label>
        <select name="fields[type]" id="field-type" class="select select-bordered select-sm w-full">
          @foreach($types as $val => $label)
            <option value="{{ $val }}" @selected($row->get('type') == $val)>
              {{ $label }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-control w-full">
        <label class="label text-base-content" for="field-subtype">
          <span class="label-text">{{ Lang::txt('COM_COURSES_FIELD_SUBTYPE') }}</span>
        </label>
        <select name="fields[subtype]" id="field-subtype" class="select select-bordered select-sm w-full">
          @foreach($subtypes as $val => $label)
            <option value="{{ $val }}" @selected($row->get('subtype') == $val)>
              {{ $label }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-control w-full mb-4">
      <label class="label text-base-content" for="field-state">
        <span class="label-text">{{ Lang::txt('COM_COURSES_FIELD_STATE') }}</span>
      </label>
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
    </div>

    <div class="form-control w-full mb-4">
      <label class="label text-base-content" for="field-title">
        <span class="label-text">
          {{ Lang::txt('COM_COURSES_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </span>
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered input-sm w-full"
             required
             value="{{ $row->get('title') }}" />
    </div>

    <div class="form-control w-full mb-4">
      <label class="label text-base-content" for="field-url">
        <span class="label-text">{{ Lang::txt('COM_COURSES_FIELD_URL') }}</span>
      </label>
      <input type="text"
             name="fields[url]"
             id="field-url"
             class="input input-bordered input-sm w-full"
             value="{{ $row->get('url') }}" />
    </div>

    <div class="form-control w-full mb-4">
      <label class="label text-base-content" for="field-content">
        <span class="label-text">{{ Lang::txt('COM_COURSES_FIELD_CONTENT') }}</span>
      </label>
      <textarea name="fields[content]"
                id="field-content"
                class="textarea textarea-bordered w-full"
                rows="4">{{ $row->get('content') }}</textarea>
    </div>

    <iframe width="100%"
            height="225"
            name="filelist"
            id="filelist"
            title="{{ Lang::txt('COM_COURSES_FILES') }}"
            class="border border-base-300 rounded-box"
            src="{{ $mediaUrl }}"></iframe>
  </fieldset>

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[course_id]" value="{{ $course_id }}" />
  <input type="hidden" name="fields[scope]" value="{{ $scope }}" />
  <input type="hidden" name="fields[scope_id]" value="{{ $scope_id }}" />
  <input type="hidden" name="fields[lid]" value="{{ $id }}" />
  <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />

  {!! Html::input('token') !!}
</form>
