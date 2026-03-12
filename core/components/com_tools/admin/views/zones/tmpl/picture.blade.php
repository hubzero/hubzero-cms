{{--
  Tool Zone Picture — Component (iframe) upload view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;

  $path = $zone->logo('path');
  $file = $zone->get('picture');

  $removeUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&tmpl=component&task=removefile&id=' . $zone->get('id') . '&' . Session::getFormToken() . '=1', false);
@endphp

<div id="media" class="p-4">
  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post" enctype="multipart/form-data" name="filelist" id="filelist">

    <div class="mb-4">
      <p class="font-medium mb-2">
        {{ Lang::txt('COM_TOOLS_UPLOAD') }}
        <span class="text-sm text-muted-foreground">{{ Lang::txt('COM_TOOLS_WILL_REPLACE_EXISTING_IMAGE') }}</span>
      </p>
      <div class="flex gap-2 items-center">
        <input type="file" name="upload" id="upload" class="file-input file-input-bordered file-input-sm" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_TOOLS_UPLOAD') }}</button>
      </div>
    </div>

    @if($__view->getError())
      <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
    @endif

    <div>
      <p class="font-medium mb-2">{{ Lang::txt('COM_TOOLS_FIELDSET_IMAGE') }}</p>
      @if($file && file_exists($path . '/' . $file))
        @php
          $imgSize = filesize($path . '/' . $file);
          [$imgW, $imgH] = getimagesize($path . '/' . $file);
          $imgSrc  = substr($path, strlen(PATH_ROOT)) . '/' . $file;
        @endphp
        <div class="flex gap-4 items-start">
          <img src="{{ $imgSrc }}" alt="{{ Lang::txt('COM_TOOLS_FIELDSET_IMAGE') }}"
               id="conimage" class="rounded border border-base-300 max-w-32" />
          <table class="text-sm">
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_FILE') }}</td><td>{{ $file }}</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_SIZE') }}</td><td>{{ \Hubzero\Utility\Number::formatBytes($imgSize) }}</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_WIDTH') }}</td><td>{{ $imgW }} px</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_HEIGHT') }}</td><td>{{ $imgH }} px</td></tr>
            <tr><td colspan="2">
              <a href="{{ $removeUrl }}" class="link link-error text-xs">[ {{ Lang::txt('JDELETE') }} ]</a>
            </td></tr>
          </table>
        </div>
        <input type="hidden" name="currentfile" value="{{ $file }}" />
      @else
        <p class="text-muted-foreground">{{ Lang::txt('COM_TOOLS_IMAGE_NONE') }}</p>
        <input type="hidden" name="currentfile" value="" />
      @endif
    </div>

    <input type="hidden" name="option"     value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="tmpl"       value="component" />
    <input type="hidden" name="id"         value="{{ $zone->get('id') }}" />
    <input type="hidden" name="task"       value="upload" />
    {!! Html::input('token') !!}
  </form>
</div>
