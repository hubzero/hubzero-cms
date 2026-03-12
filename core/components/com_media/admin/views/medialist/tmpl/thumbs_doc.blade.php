{{--
  com_media — Document/file card for thumbs (grid) view

  Variables: $currentDoc (array: name, path, size), $option

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

$currentDoc['path'] = ltrim($currentDoc['path'], '/');
$ext  = Filesystem::extension($currentDoc['name']);
$icon = Html::asset('image', 'assets/filetypes/' . $ext . '.svg', '', null, true, true);
if (!$icon) {
    $icon = Html::asset('image', 'assets/filetypes/file.svg', '', null, true, true);
}

$tmpl  = Request::getCmd('tmpl', '');
$t     = $tmpl ? '&tmpl=' . $tmpl : '';
$token = Session::getFormToken();

$name = Filesystem::name($currentDoc['name']);
if (strlen($name) > 10) {
    $name = substr($name, 0, 10) . ' ... ';
}
$name .= '.' . $ext;

$href = Route::url(
    'index.php?option=' . $option
    . '&task=download&' . $token . '=1'
    . '&file=' . urlencode($currentDoc['path']), false
);

$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize($currentDoc['size']);
$imgAlt   = e(Lang::txt('COM_MEDIA_IMAGE_TITLE', $currentDoc['name'], $fileSize));

$params = new \Hubzero\Config\Registry();
Event::trigger('onContentBeforeDisplay', ['com_media.file', &$currentDoc, &$params]);

$docUrl  = COM_MEDIA_BASEURL . $currentDoc['path'];
$docExt  = Filesystem::extension($currentDoc['name']);
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
        <div class="media-name">
            {{ $name }}
        </div>
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
                                href="{{ $infoUrl }}">
                                {{ Lang::txt('COM_MEDIA_FILE_INFO') }}
                            </a>
                        </li>
                        <li><span class="separator"></span></li>
                        <li>
                            <a download
                                class="icon-download media-opt-download"
                                href="{{ $href }}">
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
                                href="{{ $pathUrl }}">
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
                                href="{{ $deleteUrl }}">
                                {{ Lang::txt('JACTION_DELETE') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>
@php Event::trigger('onContentAfterDisplay', ['com_media.file', &$currentDoc, &$params]); @endphp
