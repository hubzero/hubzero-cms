{{--
  Courses — Media upload/manage form (tmpl=component, rendered in iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('media.blade.js');

  $uploadPath = DS . trim($config->get('uploadpath', '/site/courses'), DS)
      . DS . $course_id . DS . $listdir;

  $srcUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=list&tmpl=component&listdir=' . $listdir
      . '&subdir=' . $subdir
      . '&course=' . $course_id, false
  );
  $dataDirUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=list&tmpl=component&course=' . $course_id, false
  );
  $uploadUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=upload&course=' . $course_id
      . '&listdir=' . $listdir
      . '&no_html=1&' . Session::getFormToken() . '=1', false
  );
@endphp

@if(!empty($errors))
  <div class="alert alert-error mb-2">
    {!! implode('<br />', $errors) !!}
  </div>
@endif

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      name="adminForm"
      id="adminForm"
      method="post"
      enctype="multipart/form-data">

  <fieldset class="p-2">
    <legend class="font-semibold text-sm mb-1">
      {{ Lang::txt('COM_COURSES_FILES') }} &mdash;
      <span class="text-xs opacity-70">{{ $uploadPath }}</span>
      {!! $dirPath !!}
    </legend>

    <div id="themanager" class="manager mb-3">
      <iframe src="{{ $srcUrl }}"
              name="imgManager"
              id="imgManager"
              title="{{ Lang::txt('COM_COURSES_MEDIA') }}"
              width="98%"
              height="150"
              data-dir="{{ $dataDirUrl }}"></iframe>
    </div>

    <table>
      <tbody>
        <tr>
          <td>
            <label for="upload">{{ Lang::txt('COM_COURSES_UPLOAD') }}</label>
          </td>
          <td>
            <input type="file"
                   name="upload"
                   id="upload"
                   class="file-input file-input-bordered file-input-sm" />
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <label class="cursor-pointer flex items-center gap-2">
              <input type="checkbox"
                     name="batch"
                     id="batch"
                     value="1"
                     class="checkbox checkbox-sm" />
              {{ Lang::txt('Unpack (.zip, .tar, etc)') }}
            </label>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <button type="submit" class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_COURSES_UPLOAD') }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="listdir" id="listdir" value="{{ $listdir }}" />
    <input type="hidden" name="course" value="{{ $course_id }}" />
    <input type="hidden" name="task" value="upload" />
  </fieldset>

  {!! Html::input('token') !!}
</form>
