{{--
  Resource Media — Admin upload/manage form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('media.blade.js');

  $srcUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=list&tmpl=component&listdir=' . $listdir
      . '&subdir=' . $subdir, false
  );
  $dataDirUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=media&task=list&tmpl=component', false
  );
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      name="adminForm"
      id="adminForm"
      method="post"
      enctype="multipart/form-data">

  <p>{{ Lang::txt('COM_RESOURCES_MEDIA_PATH', str_replace(PATH_ROOT, 'ROOT', $path)) }}</p>

  <fieldset>
    <label>
      {{ Lang::txt('COM_RESOURCES_MEDIA_DIRECTORY') }}
      {!! $dirPath !!}
    </label>

    <div id="themanager" class="manager">
      <iframe src="{{ $srcUrl }}"
              name="imgManager"
              id="imgManager"
              width="98%"
              height="180"
              data-dir="{{ $dataDirUrl }}"></iframe>
    </div>
  </fieldset>

  <fieldset>
    <table>
      <tbody>
        <tr>
          <td>
            <label for="upload">{{ Lang::txt('COM_RESOURCES_MEDIA_UPLOAD') }}</label>
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
              {{ Lang::txt('COM_RESOURCES_MEDIA_UPLOAD_UNPACK') }}
            </label>
          </td>
        </tr>
        <tr>
          <td>
            <label for="foldername">
              {{ Lang::txt('COM_RESOURCES_MEDIA_CREATE_DIRECTORY') }}
            </label>
          </td>
          <td>
            <input type="text"
                   name="foldername"
                   id="foldername"
                   class="input input-bordered input-sm" />
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <button type="submit" class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_RESOURCES_MEDIA_ACTION_UPLOAD') }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="listdir" id="listdir" value="{{ $listdir }}" />
    <input type="hidden" name="task" value="upload" />
  </fieldset>

  {!! Html::input('token') !!}
</form>
