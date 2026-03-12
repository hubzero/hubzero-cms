{{--
  Blog media manager — file/folder listing (rendered inside iframe).

  Variables from controller (listTask):
    $archive  — Archive model instance
    $docs     — Array of file paths
    $folders  — Array of folder paths
    $option   — Component option string
    $controller — Controller name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $__view->js('media-list');

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
  $filespace = $archive->filespace();

  // Base URL for delete actions — raw ampersands to avoid double-encoding
  // when the URL passes through e() in data attributes and then JS dataset
  $rawBase = rtrim(Request::base(true), '/')
      . '/index.php?option=' . $option
      . '&controller=' . $controller
      . '&scope=' . urlencode($archive->get('scope'))
      . '&id=' . $archive->get('scope_id')
      . '&tmpl=component'
      . '&' . Session::getFormToken() . '=1';
@endphp

<div id="attachments">
  <form action="{{ $formAction }}" method="post" id="filelist">

    @if(count($folders) == 0 && count($docs) == 0)
      <div class="text-center py-6 px-3">
        <p class="text-base-content/50 text-sm">
          {{ Lang::txt('COM_BLOG_NO_FILES_FOUND') }}
        </p>
        <p class="text-base-content/35 text-xs mt-1">
          Upload files below to attach them to your post
        </p>
      </div>
    @else
      <ul class="list-none m-0 p-0 divide-y divide-base-200">
        <li class="text-xs text-base-content/40 px-2 py-1.5 text-center">
          Select a file to insert, copy, or delete
        </li>
        @foreach($folders as $k => $folder)
          @php
            $numFiles = count(Filesystem::files(PATH_APP . DS . $folder));
            $deleteFolderUrl = $rawBase
                . '&task=deletefolder'
                . '&folder=' . urlencode(basename($folder));
            $confirmMsg = Lang::txt(
                'Are you sure you want to delete folder "%s"?',
                basename($folder)
            );
          @endphp
          <li class="flex items-center gap-2 py-1.5 px-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 shrink-0 text-warning" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
            </svg>
            <span class="text-sm truncate flex-1 min-w-0">{{ $k }}</span>
            <a class="btn btn-ghost btn-xs btn-square text-error shrink-0"
               href="{{ $deleteFolderUrl }}"
               target="filer"
               data-confirm="{{ $confirmMsg }}"
               title="{{ Lang::txt('COM_BLOG_DELETE') }}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor"
                   class="w-3.5 h-3.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18 18 6M6 6l12 12" />
              </svg>
            </a>
          </li>
        @endforeach

        @foreach($docs as $doc)
          @php
            $filename = basename($doc);
            $ext = Filesystem::extension($doc);
            $filePath = $filespace . DS . $filename;
            $fileSize = is_file($filePath) ? filesize($filePath) : 0;
            $fileMtime = is_file($filePath) ? filemtime($filePath) : 0;
            $imageExts = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
            $isImage = in_array(strtolower($ext), $imageExts);
            $mdRef = $isImage
                ? '[[Image(' . $filename . ')]]'
                : '[[File(' . $filename . ')]]';
            $sizeStr = $fileSize < 1024
                ? $fileSize . ' B'
                : ($fileSize < 1048576
                    ? round($fileSize / 1024, 1) . ' KB'
                    : round($fileSize / 1048576, 1) . ' MB');
            $dateStr = date('M j, Y', $fileMtime);
            $deleteFileUrl = $rawBase
                . '&task=deletefile'
                . '&file=' . urlencode($filename);
            $confirmFileMsg = Lang::txt(
                'Are you sure you want to delete file "%s"?',
                $filename
            );
          @endphp
          <li class="file-row flex items-center gap-2 py-1.5 px-2
                     cursor-pointer hover:bg-base-200 rounded transition-colors"
              data-filename="{{ $filename }}"
              data-ext="{{ strtoupper($ext) }}"
              data-size="{{ $sizeStr }}"
              data-date="{{ $dateStr }}"
              data-ref="{{ $mdRef }}"
              data-is-image="{{ $isImage ? '1' : '0' }}"
              data-delete-url="{{ $deleteFileUrl }}"
              data-confirm="{{ $confirmFileMsg }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 shrink-0 text-base-content/40" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span class="text-sm truncate flex-1 min-w-0">{{ $filename }}</span>
          </li>
        @endforeach
      </ul>
    @endif

    {!! Html::input('token') !!}
  </form>

  @if($__view->getError())
    <div class="alert alert-error text-sm" role="alert">
      {!! implode('<br />', $__view->getErrors()) !!}
    </div>
  @endif
</div>
