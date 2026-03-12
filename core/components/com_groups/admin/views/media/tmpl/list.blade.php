{{--
  Groups Media — File browser list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;


  $__view->css('media.css')->js('media.blade.js');

  $baseUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&gidNumber=' . $group->get('gidNumber'), false
  );
  $token = Session::getFormToken();
@endphp

<div id="attachments">
  <form action="{{ $baseUrl }}" method="post" id="filelist">

    @if(count($folders) == 0 && count($docs) == 0)
      <p>{{ Lang::txt('COM_GROUPS_NO_FILES_FOUND') }}</p>
    @else
      <table>
        <tbody>
          @foreach($folders as $fullPath => $folder)
            @php
              $numFiles  = count(Filesystem::files($fullPath));
              $relPath   = substr($fullPath, strlen($path));
              $folderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&dir=' . urlencode($relPath)
                  . '&gidNumber=' . $group->get('gidNumber')
                  . '&tmpl=component&' . $token . '=1', false
              );
              $deleteFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefolder&dir=' . urlencode($dir)
                  . '&folder=' . urlencode($folder)
                  . '&gidNumber=' . $group->get('gidNumber')
                  . '&tmpl=component&' . $token . '=1', false
              );
            @endphp
            <tr>
              <td width="100%">
                <a class="icon-folder folder" target="media" href="{{ $folderUrl }}">
                  {{ trim($relPath, DS) }}
                </a>
              </td>
              <td>
                <a class="icon-delete delete deletefolder"
                   target="media"
                   href="{{ $deleteFolderUrl }}"
                   data-folder="{{ basename($folder) }}"
                   data-files="{{ $numFiles }}"
                   data-confirm="{{ Lang::txt('COM_GROUPS_MEDIA_DELETE_FOLDER', basename($folder)) }}"
                   data-notempty="{{ Lang::txt('COM_GROUPS_MEDIA_DIRECTORY_NOT_EMPTY') }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  <span>{{ Lang::txt('JACTION_DELETE') }}</span>
                </a>
              </td>
            </tr>
          @endforeach

          @foreach($docs as $fullPath => $doc)
            @php
              $ext         = Filesystem::extension($doc);
              $relPath     = substr($fullPath, strlen($path));
              $downloadUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&gidNumber=' . $group->get('gidNumber')
                  . '&task=download&file=' . urlencode(substr($fullPath, strlen(PATH_ROOT)))
                  . '&' . $token . '=1', false
              );
              $deleteFileUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefile&file=' . urlencode($relPath)
                  . '&gidNumber=' . $group->get('gidNumber')
                  . '&tmpl=component&' . $token . '=1', false
              );
            @endphp
            <tr>
              <td width="100%">
                <a download="download"
                   href="{{ $downloadUrl }}"
                   class="icon-file file {{ $ext }}">
                  {{ trim($relPath, DS) }}
                </a>
              </td>
              <td>
                <a class="icon-delete delete deletefile"
                   target="media"
                   href="{{ $deleteFileUrl }}"
                   data-file="{{ basename($doc) }}"
                   data-confirm="{{ Lang::txt('COM_GROUPS_MEDIA_DELETE_FILE', basename($doc)) }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  <span>{{ Lang::txt('JACTION_DELETE') }}</span>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    {!! Html::input('token') !!}
  </form>

  @if($__view->getError())
    <p class="error">{!! implode('<br />', $__view->getErrors()) !!}</p>
  @endif
</div>
