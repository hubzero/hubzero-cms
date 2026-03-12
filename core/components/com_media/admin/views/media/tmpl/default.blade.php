{{--
  com_media — Main two-panel media manager layout

  Variables: $folder, $folders (array), $folderTree, $folders_id, $layout, $option

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
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

Toolbar::title(Lang::txt('COM_MEDIA'));
if (User::authorise('core.admin', 'com_media')) {
    Toolbar::preferences($option);
    Toolbar::spacer();
}
Toolbar::help('media');

$tmpl  = Request::getCmd('tmpl', '');
$token = Session::getFormToken();

$__view->css();
$__view->js('jquery.treeview.js', 'system');
$__view->js();
@endphp

@if ($tmpl === 'component')
    <h2 class="modal-title">{{ Lang::txt('COM_MEDIA') }}</h2>
@endif
<div class="media-container">
    <div class="media-panels">
        <div class="panel panel-tree">
            <div id="media-tree_tree">
                @include('com_media::admin.views.media.tmpl.default_folders')
            </div>
        </div><!-- / .panel-tree -->
        <div class="panel panel-files">
            @php
            $formAction = Route::url(
                'index.php?option=' . $option
                . '&controller=media&tmpl=' . $tmpl
                . '&' . $token . '=1',
                true,
                true, false
            );
            @endphp
            <form action="{{ $formAction }}"
                name="adminForm" id="upload-form"
                method="post" enctype="multipart/form-data">
                <div class="media-header">
                    <div class="media-breadcrumbs-block">
                        @php
                        $rootUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=medialist&tmpl=' . $tmpl
                            . '&' . $token . '=1&folder=/', false
                        );
                        $folderIcon = Html::asset(
                            'image', 'assets/filetypes/folder.svg', '', null, true, true
                        );
                        @endphp
                        <a href="{!! $rootUrl !!}"
                            data-folder="/"
                            class="media-breadcrumbs has-next-button folder-link"
                            id="path_root">
                            <img src="{{ $folderIcon }}"
                                alt="{{ COM_MEDIA_BASEURL }}" />
                        </a>
                        <span id="media-breadcrumbs">
                            @php
                            $crumbFolder = trim($folder, '/');
                            $trail = explode('/', $crumbFolder);
                            $fld   = '';
                            @endphp
                            @foreach ($trail as $crumb)
                                @if ($crumb === ($folders[0]['name'] ?? ''))
                                    @continue
                                @endif
                                @php
                                $fld      .= '/' . $crumb;
                                $crumbUrl  = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=medialist&tmpl=' . $tmpl
                                    . '&' . $token . '=1&folder=' . $fld, false
                                );
                                @endphp
                                <span class="icon-chevron-right dir-separator">/</span>
                                <a href="{!! $crumbUrl !!}"
                                    data-folder="{{ $fld }}"
                                    class="media-breadcrumbs folder has-next-button"
                                    id="path_{{ $crumb }}">
                                    {{ $crumb }}
                                </a>
                            @endforeach
                        </span>
                    </div>
                    <div class="media-header-buttons">
                        @php
                        $thumbsCls = 'icon-th media-files-view thumbs-view';
                        if (!$layout || $layout === 'thumbs') {
                            $thumbsCls .= ' active';
                        }
                        $thumbsUrl = Route::url(
                            'index.php?option=' . $option
                            . '&layout=thumbs&tmpl=' . $tmpl
                            . '&' . $token . '=1', false
                        );
                        $thumbsTitle = Lang::txt('COM_MEDIA_THUMBNAIL_VIEW');
                        @endphp
                        <span class="media-btn-tip" data-tip="{{ $thumbsTitle }}">
                            <a class="{{ $thumbsCls }}"
                                data-view="thumbs"
                                href="{{ $thumbsUrl }}"
                                aria-label="{{ $thumbsTitle }}">
                                {{ $thumbsTitle }}
                            </a>
                        </span>
                        @php
                        $listCls = 'icon-align-justify media-files-view listing-view';
                        if ($layout === 'list') {
                            $listCls .= ' active';
                        }
                        $listUrl = Route::url(
                            'index.php?option=' . $option
                            . '&layout=list&tmpl=' . $tmpl
                            . '&' . $token . '=1', false
                        );
                        $listTitle = Lang::txt('COM_MEDIA_DETAIL_VIEW');
                        @endphp
                        <span class="media-btn-tip" data-tip="{{ $listTitle }}">
                            <a class="{{ $listCls }}"
                                data-view="list"
                                href="{{ $listUrl }}"
                                aria-label="{{ $listTitle }}">
                                {{ $listTitle }}
                            </a>
                        </span>
                        @if (User::authorise('core.create', $option))
                            @php
                            $newFolderCls = 'icon-folder-new media-files-action media-folder-new';
                            $newFolderUrl = Route::url(
                                'index.php?option=' . $option
                                . '&task=new&tmpl=' . $tmpl
                                . '&' . $token . '=1', false
                            );
                            $folderPrompt = Lang::txt('COM_MEDIA_FOLDER_NAME');
                            $createLabel  = Lang::txt('COM_MEDIA_CREATE_FOLDER');
                            @endphp
                            <span class="media-btn-tip" data-tip="{{ $createLabel }}">
                                <a class="{{ $newFolderCls }}"
                                    href="{!! $newFolderUrl !!}"
                                    data-prompt="{{ $folderPrompt }}"
                                    aria-label="{{ $createLabel }}">
                                    {{ $createLabel }}
                                </a>
                            </span>
                        @endif
                        @if (User::authorise('core.create', $option))
                            @php
                            $__view->js('jquery.fileuploader.js', 'system');
                            $uploadAction = Route::url(
                                'index.php?option=' . $option
                                . '&controller=media&task=upload&tmpl=' . $tmpl
                                . '&' . $token . '=1', false
                            );
                            $uploadList = Route::url(
                                'index.php?option=' . $option
                                . '&controller=medialist&task=display&tmpl=' . $tmpl
                                . '&' . $token . '=1', false
                            );
                            $uploadInstr    = Lang::txt('COM_MEDIA_UPLOAD_INSTRUCTIONS');
                            $uploadInstrBtn = Lang::txt('COM_MEDIA_UPLOAD_INSTRUCTIONS_BTN');
                            @endphp
                            <div id="ajax-uploader"
                                data-action="{!! $uploadAction !!}"
                                data-list="{!! $uploadList !!}"
                                data-instructions="{{ $uploadInstr }}"
                                data-instructions-btn="{{ $uploadInstrBtn }}">
                                <noscript>
                                    <div class="input-wrap">
                                        <label for="upload">
                                            {{ Lang::txt('COM_MEDIA_UPLOAD_FILE') }}:
                                        </label>
                                        <input type="file" name="upload" id="upload" />
                                    </div>
                                </noscript>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="media-view">
                    @php
                    $itemsListUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=medialist&task=display&tmpl=' . $tmpl
                        . '&' . $token . '=1', false
                    );
                    $children = \Components\Media\Admin\Helpers\MediaHelper::getChildren(
                        COM_MEDIA_BASE, ''
                    );
                    @endphp
                    <div class="media-items" id="media-items"
                        data-tmpl="{{ $tmpl }}"
                        data-list="{!! $itemsListUrl !!}">
                        @include('com_media::admin.views.medialist.tmpl.default', [
                            'folder'   => $folder,
                            'children' => $children,
                            'layout'   => $layout,
                            'option'   => $option,
                        ])
                    </div>
                </div>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="token" value="{{ $token }}" />
                <input type="hidden" name="folder" id="folder"
                    value="{{ $folder }}" />
                <input type="hidden" name="layout" id="layout"
                    value="{{ $layout }}" />
                <input type="hidden" name="tmpl" id="tmpl"
                    value="{{ $tmpl }}" />
                @php
                if ($field = Request::getCmd('e_name')) {
                    echo '<input type="hidden" name="e_name" id="e_name"'
                        . ' value="' . e($field) . '" />';
                }
                if ($field = Request::getCmd('fieldid')) {
                    echo '<input type="hidden" name="fieldid" id="fieldid"'
                        . ' value="' . e($field) . '" />';
                }
                @endphp
                {!! Html::input('token') !!}
            </form>
        </div><!-- / .panel-files -->
    </div><!-- / .media-panels -->
</div>
