{{--
  com_media — Image card for thumbs (grid) view

  Variables: $currentImg (array: name, path, size), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

$currentImg['path'] = ltrim($currentImg['path'], '/');
$ext  = Filesystem::extension($currentImg['name']);
$name = Filesystem::name($currentImg['name']);
if (strlen($name) > 10) {
    $name = substr($name, 0, 10) . ' ... ';
}
$name .= '.' . $ext;

$tmpl  = Request::getCmd('tmpl', '');
$t     = $tmpl ? '&tmpl=' . $tmpl : '';
$token = Session::getFormToken();

$href = Route::url(
    'index.php?option=' . $option
    . '&task=download&' . $token . '=1'
    . '&file=' . urlencode($currentImg['path']), false
);

$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize($currentImg['size']);
$imgAlt   = Lang::txt('COM_MEDIA_IMAGE_TITLE', $currentImg['name'], $fileSize);

$params = new \Hubzero\Config\Registry();
Event::trigger('onContentBeforeDisplay', ['com_media.file', &$currentImg, &$params]);

$imgUrl = COM_MEDIA_BASEURL . $currentImg['path'];
$imgExt = Filesystem::extension($currentImg['name']);
@endphp
<div class="media-item media-item-thumb">
    <div class="media-preview">
        <div class="media-preview-inner">
            <a href="{{ $imgUrl }}"
                class="media-thumb doc-item img-preview {{ $imgExt }}"
                title="{{ $currentImg['name'] }}">
                <span class="media-preview-shim"></span><!--
                --><img src="{{ $imgUrl }}"
                    alt="{{ $imgAlt }}"
                    width="160" />
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
                                . '&controller=medialist&task=info' . $t
                                . '&' . $token . '=1'
                                . '&file=' . urlencode($currentImg['path']), false
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
                                . '&controller=medialist&task=path' . $t
                                . '&' . $token . '=1'
                                . '&file=' . urlencode($currentImg['path']), false
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
                                . '&task=delete' . $t
                                . '&' . $token . '=1'
                                . '&rm=' . urlencode($currentImg['path']), false
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
@php Event::trigger('onContentAfterDisplay', ['com_media.file', &$currentImg, &$params]); @endphp
