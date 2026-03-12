{{--
  Resource Media — File/folder listing (rendered in iframe, tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('media.blade.js');
@endphp

<div id="attachments">
  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        id="filelist"
        name="filelist">

    @if(count($folders) == 0 && count($docs) == 0)
      <p>{{ Lang::txt('COM_RESOURCES_NO_FILES_FOUND') }}</p>
    @else
      <table>
        <tbody>
          @php
            $folderKeys = array_keys($folders);
            $listdirClean = ($listdir == '/') ? '' : $listdir;
            $subdirVal = $subdir ?? '';
          @endphp
          @foreach($folders as $folderName => $folderBasename)
            @php
              $numFiles = 0;
              if (is_dir($folderName)) {
                  $d = @dir($folderName);
                  while (false !== ($entry = $d->read())) {
                      if (substr($entry, 0, 1) != '.') {
                          $numFiles++;
                      }
                  }
                  $d->close();
              }
              $p    = strpos($folderName, $listdirClean);
              $p    = intval($p) + strlen($listdirClean);
              $name = substr($folderName, $p);

              $delFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefolder&delFolder=' . DIRECTORY_SEPARATOR . $folderBasename
                  . '&listdir=' . $listdirClean
                  . '&tmpl=component&subdir=' . $subdirVal
                  . '&' . Session::getFormToken() . '=1', false
              );
            @endphp
            <tr>
              <td>
                <span class="icon folder">
                  <span>{{ $name }}</span>
                </span>
              </td>
              <td width="100%"></td>
              <td>
                <a class="delete-folder state trash"
                   href="{{ $delFolderUrl }}"
                   target="filer"
                   data-confirm="{{ Lang::txt('Are you sure you want to delete the folder "%s"?', $folderName) }}"
                   data-files="{{ $numFiles }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  <span>{{ Lang::txt('JACTION_DELETE') }}</span>
                </a>
              </td>
            </tr>
          @endforeach

          @foreach($docs as $docName => $docBasename)
            @php
              $subdird = ($subdir && $subdir != DIRECTORY_SEPARATOR)
                  ? $subdir . DIRECTORY_SEPARATOR
                  : DIRECTORY_SEPARATOR;
              $delFileUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefile&delFile=' . $docBasename
                  . '&listdir=' . $listdirClean
                  . '&tmpl=component&subdir=' . ($subdir ?? '')
                  . '&' . Session::getFormToken() . '=1', false
              );
            @endphp
            <tr>
              <td>
                <input type="radio"
                       name="slctdfile"
                       value="{{ $listdirClean . $subdird . $docBasename }}" />
              </td>
              <td width="100%">
                {{ $docBasename }}
              </td>
              <td>
                <a class="delete-file state trash"
                   href="{{ $delFileUrl }}"
                   target="filer"
                   data-confirm="{{ Lang::txt('Are you sure you want to delete the file "%s"?', $docBasename) }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  <span>{{ Lang::txt('JACTION_DELETE') }}</span>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </form>
</div>
