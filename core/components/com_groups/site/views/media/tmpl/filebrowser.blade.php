{{--
  Media file browser (loads inside iframe for CKEditor integration).
  Minimal layout — no <x-page-container>.

  Variables from controller:
    $option        — string: component option
    $group         — Group object
    $authorized    — bool: user can manage
    $activeFolder  — string: currently selected folder
    $folderTree    — string: HTML folder tree
    $folderList    — string: HTML folder list
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  Html::behavior('modal');

  $__view->css()
         ->css('media.css')
         ->js()
         ->js('groups.mediabrowser')
         ->js('jquery.fileuploader', 'system')
         ->js('jquery.contextMenu', 'system')
         ->css('jquery.contextMenu.css', 'system');

  $type          = Request::getWord('type', '', 'get');
  $ckeditor      = Request::getString('CKEditor', '', 'get');
  $ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');
  $ckeditorQuery = '&type=' . $type . '&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;

  $cn = $group->get('cn');
  $formToken = Session::getFormToken();
@endphp

<div class="upload-browser cf">
  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <div class="upload-browser-col left">
    <div class="toolbar cf">
      <div class="title">{{ Lang::txt('COM_GROUPS_MEDIA_GROUP_FILES') }}</div>
      @if($authorized)
        <div class="buttons">
          <a href="{{ Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=media&task=addfolder&tmpl=component') }}"
             class="icon-add action-addfolder"></a>
        </div>
      @endif
    </div>
    <div class="foldertree" data-activefolder="{{ $activeFolder }}">
      {!! $folderTree !!}
    </div>
    <div class="foldertree-list">
      {!! $folderList !!}
    </div>
    <form action="{{ Route::url('index.php?option=' . $option) }}" method="post"
          enctype="multipart/form-data" class="upload-browser-uploader">
      <fieldset>
        @php
          $uploadUrl = Route::url('index.php?option=com_groups&cn=' . $cn
              . '&controller=media&task=ajaxupload&no_html=1&' . $formToken . '=1');
        @endphp
        <div id="ajax-uploader"
             data-instructions="{{ Lang::txt('Click or drop file') }}"
             data-action="{{ $uploadUrl }}">
          <noscript>
            <p><input type="file" name="upload" id="upload" /></p>
            <p><input type="submit" value="{{ Lang::txt('UPLOAD') }}" /></p>
          </noscript>
        </div>
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="media" />
        <input type="hidden" name="task" value="upload" />
        <input type="hidden" name="listdir" id="listdir"
               value="{{ $group->get('gidNumber') }}" />
        <input type="hidden" name="tmpl" value="component" />
        {!! Html::input('token') !!}
      </fieldset>
    </form>
  </div>
  <div class="upload-browser-col right">
    @php
      $listUrl = Route::url('index.php?option=com_groups&cn=' . $cn
          . '&controller=media&task=listfiles&tmpl=component&type=' . $ckeditorQuery);
    @endphp
    <iframe class="upload-browser-filelist-iframe" src="{{ $listUrl }}"></iframe>
  </div>
</div>
