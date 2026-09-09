{{--
  Media file list (loads inside iframe). Minimal layout — no <x-page-container>.

  Variables from controller:
    $group      — Group object
    $authorized — bool: user can manage
    $path       — string: absolute filesystem path
    $relpath    — string: relative path
    $folders    — array: folder names
    $files      — array: file names

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  Html::behavior('modal');

  $__view->css()
         ->css('jquery.fancybox.css', 'system')
         ->css('media.css')
         ->js()
         ->js('groups.medialist')
         ->js('groups.ckeditor-insert')
         ->js('jquery.fileuploader', 'system')
         ->js('jquery.contextMenu', 'system')
         ->css('jquery.contextMenu.css', 'system');

  $cn            = $group->get('cn');
  $baseURI       = Route::url('index.php?option=com_groups&cn=' . $cn);
  $formToken     = Session::getFormToken();
  $type          = Request::getWord('type', '', 'get');
  $ckeditor      = Request::getString('CKEditor', '', 'get');
  $ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');

  $mimeTypes = \Hubzero\Filesystem\Util\MimeType::getExtensionToMimeTypeMap();
@endphp

<div class="upload-filelist-toolbar">
  <div class="toolbar cf">
    @php
      $folder   = '';
      $segments = explode('/', ltrim($relpath, DS));
    @endphp
    <ul class="path">
      @if($group->get('type') == 3)
        <li>
          <a data-folder="/" href="#" role="button">
            {{ Lang::txt('COM_GROUPS_MEDIA_PATH_SLASH_ROOT') }}
          </a>
        </li>
      @endif
      @foreach($segments as $segment)
        @php $folder .= DS . $segment; @endphp
        <li class="divider">{{ Lang::txt('COM_GROUPS_MEDIA_PATH_SLASH') }}</li>
        <li>
          <a data-folder="{{ e($folder) }}" href="#" role="button">{{ e($segment) }}</a>
        </li>
      @endforeach
    </ul>
    <div class="buttons"></div>
  </div>
  <div class="filelist-headers">
    <ul>
      <li>
        <div class="name">{{ Lang::txt('COM_GROUPS_MEDIA_NAME') }}</div>
        <div class="modified">{{ Lang::txt('COM_GROUPS_MEDIA_MODIFIED') }}</div>
      </li>
    </ul>
  </div>
</div>

<div class="upload-filelist">
  <ul>
    @foreach($folders as $folderItem)
      @php
        $folderItem = ltrim($folderItem, '/');
        $dataFolder = ($relpath !== '/') ? $relpath . '/' . $folderItem : $relpath . $folderItem;

        $moveFolderPath   = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=movefolder&folder=' . $dataFolder
            . '&tmpl=component&' . $formToken . '=1');
        $renameFolderPath = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=renamefolder&folder=' . $dataFolder
            . '&tmpl=component&' . $formToken . '=1');
        $deleteFolderPath = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=deletefolder&folder=' . $dataFolder
            . '&tmpl=component&' . $formToken . '=1');
      @endphp
      <li class="folder">
        <div class="name">
          <a href="#" role="button"
             data-action-delete="{{ $deleteFolderPath }}"
             data-action-rename="{{ $renameFolderPath }}"
             data-action-move="{{ $moveFolderPath }}"
             data-folder="{{ e($dataFolder) }}">{{ e($folderItem) }}</a>
        </div>
        <div class="modified">--</div>
      </li>
    @endforeach

    @foreach($files as $file)
      @php
        $file = ltrim($file, DS);
        $filePath    = $path . DS . $file;
        $relFilePath = $relpath . DS . $file;

        $fileInfo   = @pathInfo($filePath);
        $filesize   = @filesize($filePath);
        $dimensions = @getimagesize($filePath);
        $modified   = @filemtime($filePath);

        $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : Filesystem::extension($file);
        if (!$extension) {
            $fileContent = file_get_contents($filePath);
            $mimeType = \Hubzero\Filesystem\Util\MimeType::detectByContent($fileContent);
            $extension = array_search($mimeType, $mimeTypes);
        }

        $formattedFilesize   = \Hubzero\Utility\Number::formatBytes($filesize);
        $formattedDimensions = $dimensions ? $dimensions[0] . 'px &times; ' . $dimensions[1] . 'px' : '';
        $formattedModified   = $modified ? Date::of($modified)->toLocal('m/d/Y g:ia') : '--';

        $isImage   = in_array($extension, ['jpg','jpeg','jpe','png','gif','bmp','tiff','tif']);
        $isArchive = in_array($extension, ['zip', 'tar', 'gz']);

        $downloadPath = $baseURI . DS . 'File:' . $relFilePath;
        $movePath     = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=movefile&file=' . $relFilePath
            . '&format=raw&' . $formToken . '=1');
        $renamePath   = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=renamefile&file=' . $relFilePath
            . '&format=raw&' . $formToken . '=1');
        $extractPath  = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=extractfile&file=' . $relFilePath
            . '&format=raw&' . $formToken . '=1');
        $deletePath   = Route::url('index.php?option=com_groups&cn=' . $cn
            . '&controller=media&task=deletefile&file=' . $relFilePath
            . '&format=raw&' . $formToken . '=1');
      @endphp
      <li class="file file-{{ strtolower($extension) }}">
        <div class="name">
          <a href="#" role="button">{{ $file }}</a>
        </div>
        <div class="modified">{{ $formattedModified }}</div>
      </li>
      <li class="file-details cf">
        <div class="right">
          <div class="title">{{ Lang::txt('COM_GROUPS_MEDIA_FILE_PREVIEW') }}</div>
          <div class="preview">
            @if($isImage)
              <img src="{{ rtrim(Request::base(true), '/') }}/core/components/com_groups/site/assets/img/loading.gif"
                   data-src="{{ $downloadPath }}"
                   alt="{{ e($file) }}" />
            @else
              <p><strong>{{ Lang::txt('COM_GROUPS_MEDIA_FILE_PREVIEW_NOT_AVAILABLE') }}</strong></p>
            @endif
          </div>
        </div>
        <div class="left">
          <div class="title">{{ Lang::txt('COM_GROUPS_MEDIA_FILE_DETAILS') }}</div>
          <ul>
            <li>
              <strong>{{ Lang::txt('COM_GROUPS_MEDIA_FILE_NAME') }}:</strong> {{ $file }}
            </li>
            <li>
              <strong>{{ Lang::txt('COM_GROUPS_MEDIA_FILE_SIZE') }}:</strong> {{ $formattedFilesize }}
            </li>
            @if($isImage)
              <li>
                <strong>{{ Lang::txt('COM_GROUPS_MEDIA_FILE_DIMENSIONS') }}:</strong> {!! $formattedDimensions !!}
              </li>
            @endif
            <li class="path">
              <strong>{{ Lang::txt('COM_GROUPS_MEDIA_FILE_PATH') }}:</strong>
              <span>{{ $downloadPath }}</span>
            </li>
            <li>
              @if($authorized && $ckeditor !== '')
                <a href="#" role="button"
                   class="btn btn-secondary icon-add"
                   data-ckeditor-insert="{{ $downloadPath }}">
                  {{ Lang::txt('COM_GROUPS_MEDIA_INSERT_FILE') }}
                </a>
              @endif
              <a href="{{ $downloadPath }}"
                 class="btn btn-secondary icon-download action-download">
                {{ Lang::txt('COM_GROUPS_MEDIA_DOWNLOAD') }}
              </a>
              @if($authorized)
                @if($group->published == 1)
                  <a href="{{ $renamePath }}"
                     class="btn btn-secondary icon-edit action-rename">
                    {{ Lang::txt('COM_GROUPS_MEDIA_RENAME') }}
                  </a>
                  <a href="{{ $movePath }}"
                     class="btn btn-secondary icon-move action-move">
                    {{ Lang::txt('COM_GROUPS_MEDIA_MOVE') }}
                  </a>
                @endif
                @if($isArchive)
                  <a href="{{ $extractPath }}"
                     class="btn btn-secondary icon-extract action-extract">
                    {{ Lang::txt('COM_GROUPS_MEDIA_EXTRACT') }}
                  </a>
                @endif
                @if($group->published == 1)
                  <a data-file="{{ $relFilePath }}"
                     href="{{ $deletePath }}"
                     class="btn btn-secondary icon-delete action-delete">
                    {{ Lang::txt('COM_GROUPS_MEDIA_DELETE') }}
                  </a>
                @endif
              @endif
            </li>
          </ul>
        </div>
      </li>
    @endforeach

    @if(count($folders) == 0 && count($files) == 0)
      <li><em>{{ Lang::txt('COM_GROUPS_MEDIA_NO_FILES') }}</em></li>
    @endif
  </ul>
</div>
