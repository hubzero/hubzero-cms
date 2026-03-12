{{--
  Course Pages — File upload (tmpl=component, rendered in iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $uploadPath = '/' . trim($config->get('uploadpath', '/site/courses'), '/')
      . '/' . ($course_id ? $course_id . '/' : '') . 'pagefiles'
      . ($listdir ? '/' . $listdir : '');

  $uploadActionUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller . '&task=upload&course='
      . $course_id . '&listdir=' . $listdir . '&no_html=1&'
      . Session::getFormToken() . '=1', false
  );

  $listUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=list&tmpl=component&listdir=' . $listdir
      . '&course=' . $course_id, false
  );

  $listBaseUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=list&tmpl=component&course=' . $course_id, false
  );
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      name="adminForm"
      id="adminForm"
      method="post"
      enctype="multipart/form-data">
  <fieldset>
    <legend class="upload-path">
      <span>{{ Lang::txt('Path') . ': ' . $uploadPath }}</span>
    </legend>
    <div id="ajax-uploader-before">&nbsp;</div>
    <div id="ajax-uploader"
         data-action="{{ $uploadActionUrl }}"
         data-instructions="{{ Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP') }}">
      <table>
        <tbody>
          <tr>
            <td>
              <input type="file" name="upload" id="upload" />
            </td>
            <td>
              <input type="submit" value="{{ Lang::txt('Upload') }}" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="themanager" class="manager">
      <iframe src="{{ $listUrl }}"
              name="imgManager"
              id="imgManager"
              title="{{ Lang::txt('COM_COURSES_FILES') }}"
              width="98%"
              height="150"
              data-dir="{{ $listBaseUrl }}"></iframe>
    </div>

    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="listdir" id="listdir" value="{{ $listdir }}" />
    <input type="hidden" name="course" value="{{ $course_id }}" />
    <input type="hidden" name="task" value="upload" />
  </fieldset>
  {!! Html::input('token') !!}
</form>
