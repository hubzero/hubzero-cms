{{--
  Groups Media — Upload and browse view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;


  $__view->css('media.css')->js('media.blade.js');

  $baseUrl = Route::url(
      'index.php?option=' . $option
      . '&tmpl=component&controller=' . $controller
      . '&gidNumber=' . $group->get('gidNumber'), false
  );
  $listUrl = Route::url(
      'index.php?option=' . $option
      . '&tmpl=component&controller=' . $controller
      . '&gidNumber=' . $group->get('gidNumber')
      . '&task=list'
      . ($dir ? '&dir=' . $dir : '')
      . '&t=' . Date::toUnix(), false
  );
@endphp

<div id="attachments">
  <form action="{{ $baseUrl }}&task=upload"
        id="adminForm"
        method="post"
        enctype="multipart/form-data">
    <fieldset>
      <div class="flex flex-wrap gap-4">
        <div>
          <div class="input-wrap">
            <label for="upload" class="sr-only">{{ Lang::txt('COM_GROUPS_MEDIA_ACTION_UPLOAD') }}</label>
            <input type="file" name="upload" id="upload" />
          </div>
        </div>
        <div>
          <div class="input-wrap">
            <label for="foldername" class="sr-only">{{ Lang::txt('COM_GROUPS_MEDIA_CREATE_DIRECTORY') }}</label>
            <input type="text"
                   name="foldername"
                   id="foldername"
                   placeholder="{{ Lang::txt('COM_GROUPS_MEDIA_CREATE_DIRECTORY') }}" />
          </div>
        </div>
        <div>
          <div class="input-wrap">
            <input type="submit" value="{{ Lang::txt('COM_GROUPS_MEDIA_ACTION_UPLOAD') }}" />
          </div>
        </div>
      </div>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="task" value="upload" />
      <input type="hidden" name="gidNumber" value="{{ $group->get('gidNumber') }}" />
      <input type="hidden" name="dir" id="currentdir" value="{{ urlencode($dir) }}" />
      <input type="hidden" name="tmpl" value="component" />
      {!! Html::input('token') !!}
    </fieldset>

    @if($__view->getError())
      <p class="error">{{ $__view->getError() }}</p>
    @endif

    <div id="themanager" class="manager">
      <div class="input-wrap">
        <label for="dir">
          {{ Lang::txt('COM_GROUPS_MEDIA_DIRECTORY') }}
          {!! $dirPath !!}
        </label>
      </div>
      <iframe src="{!! $listUrl !!}" name="filer" id="filer" width="98%" height="400" title="{{ Lang::txt('COM_GROUPS_MEDIA_DIRECTORY') }}"></iframe>
    </div>
  </form>
</div>
