{{--
  Courses — Certificates admin upload/edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $text = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
  $canDo = \Components\Courses\Helpers\Permissions::getActions();

  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_CERTIFICATE') . ': ' . $text,
      'courses'
  );
  Toolbar::cancel();
@endphp

@if(!empty($errors))
  <div class="alert alert-error mb-4">
    {!! implode('<br />', $errors) !!}
  </div>
@endif

<form action="{{ $routeUrl }}"
      method="post"
      name="adminForm"
      id="item-form"
      enctype="multipart/form-data">

  <div class="grid grid-cols-12 gap-6">
    <div class="col-span-7">
      <x-form-section title="{{ Lang::txt('COM_COURSES_UPLOAD') }}">
        <x-form-field label="{{ Lang::txt('COM_COURSES_UPLOAD') }}" inputId="upload" required>
          <input type="file"
                 name="upload"
                 id="upload"
                 class="file-input file-input-bordered file-input-sm w-full"
                 required />
        </x-form-field>
        <div class="mt-2">
          <button type="submit" class="btn btn-sm btn-primary">
            {{ Lang::txt('COM_COURSES_UPLOAD') }}
          </button>
        </div>
      </x-form-section>
    </div>
    <div class="col-span-5">
      <div class="bg-base-200/50 rounded-box p-4">
        <p>{{ Lang::txt('COM_COURSES_CERTIFICATE_HELP') }}</p>
      </div>
    </div>
  </div>

  <input type="hidden" name="certificate" value="{{ $row->get('id') }}" />
  <input type="hidden" name="course" value="{{ $row->get('course_id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="upload" />

  {!! Html::input('token') !!}
</form>
