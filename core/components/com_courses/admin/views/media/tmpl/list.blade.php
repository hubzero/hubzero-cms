{{--
  Courses — Media file/folder listing (rendered in iframe, tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('media.blade.js');

  $listdirClean = ($listdir == '/') ? '' : $listdir;
  $subdirVal    = $subdir ?? '';
@endphp

<div id="attachments" class="p-2">
  <form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
        method="post"
        id="filelist"
        name="filelist">

    @if(count($folders) == 0 && count($docs) == 0)
      <p class="text-sm opacity-60">{{ Lang::txt('COM_COURSES_NO_FILE_FOUNDS') }}</p>
    @else
      <table class="table table-sm w-full">
        <tbody>
          @foreach($folders as $folderPath => $folderName)
            @php
              $numFiles = 0;
              if (is_dir($folderPath)) {
                  $d = @dir($folderPath);
                  while (false !== ($entry = $d->read())) {
                      if (substr($entry, 0, 1) != '.') {
                          $numFiles++;
                      }
                  }
                  $d->close();
              }

              $delFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefolder&delFolder='
                  . DS . $folderName
                  . '&listdir=' . $listdirClean
                  . '&tmpl=component&subdir=' . $subdirVal
                  . '&course=' . $course_id
                  . '&' . Session::getFormToken() . '=1', false
              );
              $confirmMsg = Lang::txt(
                  'Are you sure you want to delete the folder "%s"?',
                  $folderName
              );
              $clearMsg = Lang::txt('COM_COURSES_CLEAR_FOLDER')
                  . ' ' . Lang::txt('COM_COURSES_FILES');
            @endphp
            <tr>
              <td class="w-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-warning" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                </svg>
              </td>
              <td class="w-full">{{ $folderName }}</td>
              <td>
                <a class="delete-folder btn btn-xs btn-ghost text-error"
                   href="{{ $delFolderUrl }}"
                   data-files="{{ $numFiles }}"
                   data-confirm="{{ $confirmMsg }}"
                   data-notempty="{{ $clearMsg }}"
                   title="{{ Lang::txt('COM_COURSES_DELETE') }}">
                  {{ Lang::txt('COM_COURSES_DELETE') }}
                </a>
              </td>
            </tr>
          @endforeach

          @foreach($docs as $docPath => $docName)
            @php
              $delFileUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=deletefile&delFile=' . $docName
                  . '&listdir=' . $listdirClean
                  . '&tmpl=component&subdir=' . $subdirVal
                  . '&course=' . $course_id
                  . '&' . Session::getFormToken() . '=1', false
              );
              $confirmMsg = Lang::txt(
                  'Are you sure you want to delete the file "%s"?',
                  $docName
              );
            @endphp
            <tr>
              <td class="w-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-muted-foreground" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                </svg>
              </td>
              <td class="w-full">{{ $docName }}</td>
              <td>
                <a class="delete-file btn btn-xs btn-ghost text-error"
                   href="{{ $delFileUrl }}"
                   data-confirm="{{ $confirmMsg }}"
                   title="{{ Lang::txt('COM_COURSES_DELETE') }}">
                  {{ Lang::txt('COM_COURSES_DELETE') }}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </form>

  @if(!empty($errors))
    <div class="alert alert-error mt-2">
      {!! implode('<br />', $errors) !!}
    </div>
  @endif
</div>
