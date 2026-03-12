{{--
  com_media — List (table) view of folder contents

  Variables: $folder, $children (array of dir/file/img items), $active (bool), $option

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
<div class="media-files media-list{{ $cls }}" id="media-list">
    <form action="{{ $formAction }}"
        method="post" id="media-form-list" name="media-form-list">
        <div class="manager">
            <table>
                <thead>
                    <tr>
                        <th scope="col">
                            {{ Lang::txt('COM_MEDIA_LIST_HEADER_NAME') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_MEDIA_LIST_HEADER_SIZE') }}
                        </th>
                        @if ($tmpl !== 'component')
                            <th scope="col">
                                {{ Lang::txt('COM_MEDIA_LIST_HEADER_TYPE') }}
                            </th>
                            <th scope="col">
                                {{ Lang::txt('COM_MEDIA_LIST_HEADER_MODIFIED') }}
                            </th>
                        @endif
                        @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                            <th scope="col"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{-- Folders first --}}
                    @foreach ($folders as $currentFolder)
                        @php
                        $folderPath  = ltrim($currentFolder['path'], '/');
                        $dataFolder  = '/' . $folderPath;
                        $folderIcon  = Html::asset(
                            'image', 'assets/filetypes/folder.svg', '', null, true, true
                        );
                        $folderEscName = $currentFolder['name'];
                        $folderName  = $tmpl === 'component' && strlen($currentFolder['name']) > 10
                            ? substr($currentFolder['name'], 0, 10) . ' ... '
                            : $currentFolder['name'];
                        $modified    = Date::of(filemtime(COM_MEDIA_BASE . $currentFolder['path']));
                        $folderUrl   = Route::url(
                            'index.php?option=' . $option
                            . '&controller=medialist' . $t
                            . '&' . $token . '=1&folder=/' . $folderPath, false
                        );
                        $tdWidth = ($tmpl === 'component'
                            && !User::authorise('core.delete', 'com_media')) ? '70' : '60';
                        @endphp
                        <tr class="media-item media-item-list">
                            <td width="{{ $tdWidth }}%">
                                <a class="folder-item"
                                    data-folder="{{ $dataFolder }}"
                                    href="{!! $folderUrl !!}">
                                    <span class="media-icon">
                                        <img src="{{ $folderIcon }}"
                                            alt="{{ $folderEscName }}" />
                                    </span>
                                    <span class="media-name">
                                        {{ $folderName }}
                                    </span>
                                </a>
                            </td>
                            <td><!-- Nothing here --></td>
                            @if ($tmpl !== 'component')
                                <td>
                                    <span class="media-type">
                                        {{ Lang::txt('Folder') }}
                                    </span>
                                </td>
                                <td>
                                    <time class="media-modified"
                                        datetime="{{ $modified->format('Y-m-d\TH:i:s\Z') }}">
                                        {{ $modified->toSql() }}
                                    </time>
                                </td>
                            @endif
                            @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                                <td>
                                    <div class="media-preview-inner">
                                        <span class="media-options-btn"></span>
                                        <div class="media-options">
                                            <ul>
                                                @if ($tmpl !== 'component')
                                                    <li>
                                                        @php
                                                        $infoUrl = Route::url(
                                                            'index.php?option=' . $option
                                                            . '&controller=medialist&task=info' . $t
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
                                                            . '&task=delete' . $t
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
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Files --}}
                    @foreach ($files as $currentDoc)
                        @if ($tmpl === 'component' && $currentDoc['type'] !== 'img')
                            @continue
                        @endif
                        @php
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
                        if ($tmpl === 'component' && strlen($docName) > 10) {
                            $docName = substr($docName, 0, 10) . ' ... ';
                        }
                        $docName .= '.' . $ext;
                        $dlHref  = Route::url(
                            'index.php?option=' . $option
                            . '&task=download&' . $token . '=1'
                            . '&file=' . urlencode($currentDoc['path']), false
                        );
                        $docModified = Date::of(filemtime(COM_MEDIA_BASE . $currentDoc['path']));
                        $fileSize    = \Components\Media\Admin\Helpers\MediaHelper::parseSize(
                            $currentDoc['size']
                        );
                        $imgAlt  = e(Lang::txt(
                            'COM_MEDIA_IMAGE_TITLE', $currentDoc['name'], $fileSize
                        ));
                        $params  = new \Hubzero\Config\Registry();
                        Event::trigger(
                            'onContentBeforeDisplay', ['com_media.file', &$currentDoc, &$params]
                        );
                        $docUrl  = COM_MEDIA_BASEURL . $currentDoc['path'];
                        @endphp
                        <tr class="media-item media-item-list">
                            <td width="50%">
                                <a class="doc-item"
                                    href="{{ $docUrl }}"
                                    title="{{ $currentDoc['name'] }}">
                                    <span class="media-icon">
                                        <img src="{{ $icon }}" alt="{{ $imgAlt }}" />
                                    </span>
                                    <span class="media-name">{{ $docName }}</span>
                                </a>
                            </td>
                            <td>
                                <span class="media-size">{{ $fileSize }}</span>
                            </td>
                            @if ($tmpl !== 'component')
                                <td>
                                    <span class="media-type">{{ strtoupper($ext) }}</span>
                                </td>
                                <td>
                                    <time class="media-modified"
                                        datetime="{{ $docModified->format('Y-m-d\TH:i:s\Z') }}">
                                        {{ $docModified->toSql() }}
                                    </time>
                                </td>
                            @endif
                            @if ($tmpl !== 'component' || User::authorise('core.delete', 'com_media'))
                                <td>
                                    <div class="media-preview-inner">
                                        <span class="media-options-btn"></span>
                                        <div class="media-options">
                                            <ul>
                                                @if ($tmpl !== 'component')
                                                    <li>
                                                        @php
                                                        $infoUrl = Route::url(
                                                            'index.php?option=' . $option
                                                            . '&controller=medialist&task=info' . $t
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
                                                            . '&controller=medialist&task=path' . $t
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
                                                            . '&task=delete' . $t
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
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @php
                        Event::trigger(
                            'onContentAfterDisplay', ['com_media.file', &$currentDoc, &$params]
                        );
                        @endphp
                    @endforeach
                </tbody>
            </table>

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
