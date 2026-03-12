{{--
  com_media — Thumbs (grid) view of folder contents

  Variables: $folder, $children (array of dir/file/img items), $active (bool), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

$tmpl  = Request::getCmd('tmpl', '');
$t     = $tmpl ? '&tmpl=' . $tmpl : '';
$token = Session::getFormToken();

$folders = [];
$files   = [];
foreach ($children as $child) {
    if ($child['type'] === 'dir') {
        $folders[] = $child;
    } elseif ($child['type'] === 'file' || $child['type'] === 'img') {
        $files[] = $child;
    }
}

$cls        = !empty($active) ? ' active' : '';
$formAction = Route::url('index.php?option=' . $option . '&folder=' . $folder, false);
@endphp
<div class="media-files media-thumbs{{ $cls }}" id="media-thumbs">
    <form action="{{ $formAction }}"
        method="post" id="media-form-thumbs" name="media-form-thumbs">
        <div class="manager">
            {{-- Folders first --}}
            @foreach ($folders as $currentFolder)
                @php
                $folderName  = $currentFolder['name'];
                if (strlen($folderName) > 10) {
                    $folderName = substr($folderName, 0, 10) . ' ... ';
                }
                $folderPath  = ltrim($currentFolder['path'], '/');
                $dataFolder  = '/' . $folderPath;
                $folderIcon  = Html::asset(
                    'image', 'assets/filetypes/folder.svg', '', null, true, true
                );
                $folderEscName = $currentFolder['name'];
                $folderUrl   = Route::url(
                    'index.php?option=com_media&controller=medialist'
                    . '&tmpl=' . $tmpl
                    . '&' . $token . '=1&folder=/' . $folderPath, false
                );
                @endphp
                <div class="media-item media-item-thumb">
                    <div class="media-preview">
                        <div class="media-preview-inner">
                            <a class="media-thumb folder-item"
                                data-folder="{{ $dataFolder }}"
                                href="{!! $folderUrl !!}">
                                <span class="media-preview-shim"></span><!--
                                --><img src="{{ $folderIcon }}"
                                    alt="{{ $folderEscName }}"
                                    width="80" />
                            </a>
                            <span class="media-options-btn"></span>
                        </div>
                    </div>
                    <div class="media-info">
                        <div class="media-name">{{ $folderName }}</div>
                        @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                            <div class="media-options">
                                <ul>
                                    @if ($tmpl !== 'component')
                                        <li>
                                            @php
                                            $infoUrl = Route::url(
                                                'index.php?option=' . $option
                                                . '&controller=medialist&task=info'
                                                . '&tmpl=' . $tmpl
                                                . '&' . $token . '=1'
                                                . '&folder=' . urlencode($currentFolder['path']), false
                                            );
                                            @endphp
                                            <a class="icon-info media-opt-info"
                                                href="{!! $infoUrl !!}">
                                                {{ Lang::txt('Info') }}
                                            </a>
                                        </li>
                                    @endif
                                    @if (User::authorise('core.delete', 'com_media'))
                                        <li><span class="separator"></span></li>
                                        <li>
                                            @php
                                            $deleteUrl = Route::url(
                                                'index.php?option=' . $option
                                                . '&task=delete&tmpl=' . $tmpl
                                                . '&' . $token . '=1'
                                                . '&rm=' . urlencode($currentFolder['path']), false
                                            );
                                            @endphp
                                            <a class="icon-trash media-opt-delete"
                                                href="{!! $deleteUrl !!}">
                                                {{ Lang::txt('JACTION_DELETE') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Files --}}
            @foreach ($files as $currentFile)
                @if ($currentFile['type'] === 'file')
                    @if ($tmpl === 'component')
                        @continue
                    @endif
                    @php
                    $currentDoc = $currentFile;
                    $currentDoc['path'] = ltrim($currentDoc['path'], '/');
                    $ext  = Filesystem::extension($currentDoc['name']);
                    $icon = Html::asset(
                        'image', 'assets/filetypes/' . $ext . '.svg', '', null, true, true
                    );
                    if (!$icon) {
                        $icon = Html::asset(
                            'image', 'assets/filetypes/file.svg', '', null, true, true
                        );
                    }
                    $docName = Filesystem::name($currentDoc['name']);
                    if (strlen($docName) > 10) {
                        $docName = substr($docName, 0, 10) . ' ... ';
                    }
                    $docName .= '.' . $ext;
                    $dlHref  = Route::url(
                        'index.php?option=' . $option
                        . '&task=download&' . $token . '=1'
                        . '&file=' . urlencode($currentDoc['path']), false
                    );
                    $fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
                        $currentDoc['size']
                    );
                    $imgAlt  = e(Lang::txt(
                        'COM_MEDIA_IMAGE_TITLE', $currentDoc['name'], $fileSize
                    ));
                    $params  = new \Hubzero\Config\Registry();
                    Event::trigger(
                        'onContentBeforeDisplay', ['com_media.file', &$currentDoc, &$params]
                    );
                    $docUrl = COM_MEDIA_BASEURL . $currentDoc['path'];
                    $docExt = Filesystem::extension($currentDoc['name']);
                    @endphp
                    <div class="media-item media-item-thumb">
                        <div class="media-preview">
                            <div class="media-preview-inner">
                                <a href="{{ $docUrl }}"
                                    class="media-thumb doc-item {{ $docExt }}"
                                    title="{{ $currentDoc['name'] }}">
                                    <span class="media-preview-shim"></span><!--
                                    --><img src="{{ $icon }}"
                                        alt="{{ $imgAlt }}"
                                        width="80" />
                                </a>
                                <span class="media-options-btn"></span>
                            </div>
                        </div>
                        <div class="media-info">
                            <div class="media-name">{{ $docName }}</div>
                            @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                                <div class="media-options">
                                    <ul>
                                        @if ($tmpl !== 'component')
                                            <li>
                                                @php
                                                $infoUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=medialist&task=info'
                                                    . '&tmpl=' . $tmpl
                                                    . '&' . $token . '=1'
                                                    . '&file=' . urlencode($currentDoc['path']), false
                                                );
                                                @endphp
                                                <a class="icon-info media-opt-info"
                                                    href="{!! $infoUrl !!}">
                                                    {{ Lang::txt('COM_MEDIA_FILE_INFO') }}
                                                </a>
                                            </li>
                                            <li><span class="separator"></span></li>
                                            <li>
                                                <a download
                                                    class="icon-download media-opt-download"
                                                    href="{!! $dlHref !!}">
                                                    {{ Lang::txt('COM_MEDIA_DOWNLOAD') }}
                                                </a>
                                            </li>
                                            <li>
                                                @php
                                                $pathUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=medialist&task=path'
                                                    . '&tmpl=' . $tmpl
                                                    . '&' . $token . '=1'
                                                    . '&file=' . urlencode($currentDoc['path']), false
                                                );
                                                @endphp
                                                <a class="icon-link media-opt-path"
                                                    href="{!! $pathUrl !!}">
                                                    {{ Lang::txt('COM_MEDIA_FILE_LINK') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (User::authorise('core.delete', 'com_media'))
                                            <li><span class="separator"></span></li>
                                            <li>
                                                @php
                                                $deleteUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&task=delete&tmpl=' . $tmpl
                                                    . '&' . $token . '=1'
                                                    . '&rm=' . urlencode($currentDoc['path']), false
                                                );
                                                @endphp
                                                <a class="icon-trash media-opt-delete"
                                                    href="{!! $deleteUrl !!}">
                                                    {{ Lang::txt('JACTION_DELETE') }}
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    @php
                    Event::trigger(
                        'onContentAfterDisplay', ['com_media.file', &$currentDoc, &$params]
                    );
                    @endphp
                @elseif ($currentFile['type'] === 'img')
                    @php
                    $currentImg = $currentFile;
                    $currentImg['path'] = ltrim($currentImg['path'], '/');
                    $imgFileExt  = Filesystem::extension($currentImg['name']);
                    $imgFileName = Filesystem::name($currentImg['name']);
                    if (strlen($imgFileName) > 10) {
                        $imgFileName = substr($imgFileName, 0, 10) . ' ... ';
                    }
                    $imgFileName .= '.' . $imgFileExt;
                    $imgDlHref = Route::url(
                        'index.php?option=' . $option
                        . '&task=download&' . $token . '=1'
                        . '&file=' . urlencode($currentImg['path']), false
                    );
                    $imgFileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
                        $currentImg['size']
                    );
                    $imgFileAlt = Lang::txt(
                        'COM_MEDIA_IMAGE_TITLE', $currentImg['name'], $imgFileSize
                    );
                    $imgParams  = new \Hubzero\Config\Registry();
                    Event::trigger(
                        'onContentBeforeDisplay', ['com_media.file', &$currentImg, &$imgParams]
                    );
                    $imgSrc = COM_MEDIA_BASEURL . $currentImg['path'];
                    @endphp
                    <div class="media-item media-item-thumb">
                        <div class="media-preview">
                            <div class="media-preview-inner">
                                <a href="{{ $imgSrc }}"
                                    class="media-thumb doc-item img-preview {{ $imgFileExt }}"
                                    title="{{ $currentImg['name'] }}">
                                    <span class="media-preview-shim"></span><!--
                                    --><img src="{{ $imgSrc }}"
                                        alt="{{ $imgFileAlt }}"
                                        width="160" />
                                </a>
                                <span class="media-options-btn"></span>
                            </div>
                        </div>
                        <div class="media-info">
                            <div class="media-name">{{ $imgFileName }}</div>
                            @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                                <div class="media-options">
                                    <ul>
                                        @if ($tmpl !== 'component')
                                            <li>
                                                @php
                                                $infoUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=medialist&task=info' . $t
                                                    . '&' . $token . '=1'
                                                    . '&file=' . urlencode($currentImg['path']), false
                                                );
                                                @endphp
                                                <a class="icon-info media-opt-info"
                                                    href="{!! $infoUrl !!}">
                                                    {{ Lang::txt('COM_MEDIA_FILE_INFO') }}
                                                </a>
                                            </li>
                                            <li><span class="separator"></span></li>
                                            <li>
                                                <a download
                                                    class="icon-download media-opt-download"
                                                    href="{!! $imgDlHref !!}">
                                                    {{ Lang::txt('COM_MEDIA_DOWNLOAD') }}
                                                </a>
                                            </li>
                                            <li>
                                                @php
                                                $pathUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=medialist&task=path' . $t
                                                    . '&' . $token . '=1'
                                                    . '&file=' . urlencode($currentImg['path']), false
                                                );
                                                @endphp
                                                <a class="icon-link media-opt-path"
                                                    href="{!! $pathUrl !!}">
                                                    {{ Lang::txt('COM_MEDIA_FILE_LINK') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (User::authorise('core.delete', 'com_media'))
                                            <li><span class="separator"></span></li>
                                            <li>
                                                @php
                                                $deleteUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&task=delete' . $t
                                                    . '&' . $token . '=1'
                                                    . '&rm=' . urlencode($currentImg['path']), false
                                                );
                                                @endphp
                                                <a class="icon-trash media-opt-delete"
                                                    href="{!! $deleteUrl !!}">
                                                    {{ Lang::txt('JACTION_DELETE') }}
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    @php
                    Event::trigger(
                        'onContentAfterDisplay', ['com_media.file', &$currentImg, &$imgParams]
                    );
                    @endphp
                @endif
            @endforeach

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="username" value="" />
            <input type="hidden" name="password" value="" />
            {!! Html::input('token') !!}
            <input type="hidden" name="folder"
                value="{{ $folder }}" />
            <input type="hidden" name="option"
                value="{{ $option }}" />
            <input type="hidden" name="tmpl"
                value="{{ $tmpl }}" />
        </div>
    </form>
</div>
