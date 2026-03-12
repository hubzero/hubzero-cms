{{--
  com_media — Folder card for thumbs (grid) view

  Variables: $currentFolder (array: name, path), $option

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
use Hubzero\Facades\User;

$tmpl  = Request::getCmd('tmpl', '');
$t     = $tmpl ? '&tmpl=' . $tmpl : '';
$token = Session::getFormToken();

$name = $currentFolder['name'];
if (strlen($name) > 10) {
    $name = substr($name, 0, 10) . ' ... ';
}

$folderPath  = ltrim($currentFolder['path'], '/');
$dataFolder  = e('/' . $folderPath);
$folderIcon  = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);
$escapedName = $currentFolder['name'];

$folderUrl = Route::url(
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
                href="{{ $folderUrl }}">
                <span class="media-preview-shim"></span><!--
                --><img src="{{ $folderIcon }}"
                    alt="{{ $escapedName }}"
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
                                . '&folder=' . urlencode($currentFolder['path']), false
                            );
                            @endphp
                            <a class="icon-info media-opt-info"
                                href="{{ $infoUrl }}">
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
