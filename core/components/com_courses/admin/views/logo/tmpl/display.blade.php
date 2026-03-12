{{--
  Courses — Logo upload/display (tmpl=component, rendered in iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
  $hasFile = ($file && file_exists($path . DS . $file));

  if ($hasFile) {
      $fileSize = filesize($path . DS . $file);
      list($imgWidth, $imgHeight) = getimagesize($path . DS . $file);
      $imgSrc = rtrim(Request::root(true), '/')
          . substr($path, strlen(PATH_ROOT))
          . DS . $file;
      $removeUrl = Route::url(
          'index.php?option=' . $option
          . '&controller=' . $controller
          . '&tmpl=component&task=remove&type=' . $type
          . '&file=' . $file . '&id=' . $id
          . '&' . Session::getFormToken() . '=1', false
      );
  }
@endphp

<div id="media" class="p-2">
  <form action="{{ $routeUrl }}"
        method="post"
        enctype="multipart/form-data"
        name="filelist"
        id="filelist">

    <fieldset class="mb-4">
      <legend class="font-semibold text-sm mb-1">
        {{ Lang::txt('COM_COURSES_UPLOAD') }}
        <span class="text-xs opacity-60">{{ Lang::txt('WILL_REPLACE_EXISTING_IMAGE') }}</span>
      </legend>

      <div class="flex items-center gap-2">
        <input type="file"
               name="upload"
               id="upload"
               class="file-input file-input-bordered file-input-sm" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_COURSES_UPLOAD') }}
        </button>
      </div>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="tmpl" value="component" />
      <input type="hidden" name="id" value="{{ $id }}" />
      <input type="hidden" name="type" value="{{ $type }}" />
      <input type="hidden" name="task" value="upload" />
    </fieldset>

    @if(!empty($errors))
      <div class="alert alert-error mb-2">
        {!! implode('<br />', $errors) !!}
      </div>
    @endif

    <fieldset>
      <legend class="font-semibold text-sm mb-1">
        {{ Lang::txt('COM_COURSES_LOGO') }}
      </legend>

      @if($hasFile)
        <div class="flex gap-4">
          <div class="shrink-0">
            <img src="{{ $imgSrc }}"
                 alt="{{ Lang::txt('COM_COURSES_LOGO') }}"
                 id="conimage"
                 class="max-w-xs rounded" />
          </div>
          <div class="text-sm space-y-1">
            <p><strong>{{ Lang::txt('COM_COURSES_FILE') }}:</strong> {{ $file }}</p>
            <p><strong>{{ Lang::txt('COM_COURSES_PICTURE_SIZE') }}:</strong> {{ \Hubzero\Utility\Number::formatBytes($fileSize) }}</p>
            <p><strong>{{ Lang::txt('COM_COURSES_PICTURE_WIDTH') }}:</strong> {{ $imgWidth }} px</p>
            <p><strong>{{ Lang::txt('COM_COURSES_PICTURE_HEIGHT') }}:</strong> {{ $imgHeight }} px</p>
            <p>
              <input type="hidden" name="currentfile" value="{{ $file }}" />
              <a href="{{ $removeUrl }}" class="btn btn-xs btn-error btn-outline">
                {{ Lang::txt('COM_COURSES_DELETE') }}
              </a>
            </p>
          </div>
        </div>
      @else
        <p class="text-sm opacity-60">
          {{ Lang::txt('COM_COURSES_LOGO_NONE') }}
        </p>
        <input type="hidden" name="currentfile" value="" />
      @endif
    </fieldset>

    {!! Html::input('token') !!}
  </form>
</div>
