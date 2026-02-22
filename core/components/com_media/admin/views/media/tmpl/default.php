<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEDIA'));
if (User::authorise('core.admin', 'com_media')) {
    Toolbar::preferences($this->option);
    Toolbar::spacer();
}
Toolbar::help('media');

$base = COM_MEDIA_BASE;
if (DIRECTORY_SEPARATOR == '\\') {
    $base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_MEDIA_BASE);
}

$style = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');
$tmpl  = Request::getCmd('tmpl', '');

Html::behavior('framework', true);
Html::behavior('modal');
Html::behavior('tooltip');

$this->css();
$this->js('jquery.treeview.js', 'system');
$this->js();

$token = Session::getFormToken();
?>
<?php if (Request::getCmd('tmpl') == 'component') : ?>
    <h2 class="modal-title"><?php echo Lang::txt('COM_MEDIA'); ?></h2>
<?php endif; ?>
<div class="media-container modal">
    <div class="media-panels">
        <div class="panel panel-tree">
            <div id="media-tree_tree">
                <?php echo $this->loadTemplate('folders'); ?>
            </div>
        </div><!-- / .panel-tree -->
        <div class="panel panel-files">
            <?php
            $formAction = Route::url(
                'index.php?option=' . $this->option
                . '&controller=media&tmpl=' . $tmpl
                . '&' . $token . '=1',
                true,
                true
            );
            ?>
            <form action="<?php echo $formAction; ?>"
                name="adminForm" id="upload-form"
                method="post" enctype="multipart/form-data">
                <div class="media-header">
                    <div class="media-breadcrumbs-block">
                        <?php
                        $rootUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=medialist&tmpl=' . $tmpl
                            . '&' . $token . '=1&folder=/'
                        );
                        $folderIcon = Html::asset(
                            'image',
                            'assets/filetypes/folder.svg',
                            '',
                            null,
                            true,
                            true
                        );
                        ?>
                        <a href="<?php echo $rootUrl; ?>"
                            data-folder="/"
                            class="media-breadcrumbs has-next-button folder-link"
                            id="path_root">
                            <img src="<?php echo $folderIcon; ?>"
                                alt="<?php echo COM_MEDIA_BASEURL; ?>" />
                        </a>
                        <span id="media-breadcrumbs">
                            <?php
                            $folder = trim($this->folder, '/');
                            $trail = explode('/', $folder);
                            $fld = '';

                            foreach ($trail as $crumb) :
                                // Skip the root directory
                                if ($crumb == $this->folders[0]['name']) :
                                    continue;
                                endif;

                                $fld .= '/' . $crumb;
                                $crumbUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=medialist&tmpl=' . $tmpl
                                    . '&' . $token . '=1&folder=' . $fld
                                );
                                ?>
                                <span class="icon-chevron-right dir-separator">/</span>
                                <a href="<?php echo $crumbUrl; ?>"
                                    data-folder="<?php echo $fld; ?>"
                                    class="media-breadcrumbs folder has-next-button"
                                    id="path_<?php echo $crumb; ?>">
                                    <?php echo $crumb; ?>
                                </a>
                                <?php
                            endforeach;
                            ?>
                        </span>
                    </div>
                    <div class="media-header-buttons">
                        <?php
                        $thumbsCls = 'icon-th media-files-view thumbs-view hasTip';
                        if (!$this->layout || $this->layout == 'thumbs') {
                            $thumbsCls .= ' active';
                        }
                        $thumbsUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&layout=thumbs&tmpl=' . $tmpl
                            . '&' . $token . '=1'
                        );
                        $thumbsTitle = Lang::txt('COM_MEDIA_THUMBNAIL_VIEW');
                        ?>
                        <a class="<?php echo $thumbsCls; ?>"
                            data-view="thumbs"
                            href="<?php echo $thumbsUrl; ?>"
                            title="<?php echo $thumbsTitle; ?>">
                            <?php echo $thumbsTitle; ?>
                        </a>
                        <?php
                        $listCls = 'icon-align-justify media-files-view hasTip listing-view';
                        if ($this->layout == 'list') {
                            $listCls .= ' active';
                        }
                        $listUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&layout=list&tmpl=' . $tmpl
                            . '&' . $token . '=1'
                        );
                        $listTitle = Lang::txt('COM_MEDIA_DETAIL_VIEW');
                        ?>
                        <a class="<?php echo $listCls; ?>"
                            data-view="list"
                            href="<?php echo $listUrl; ?>"
                            title="<?php echo $listTitle; ?>">
                            <?php echo $listTitle; ?>
                        </a>
                        <?php if (User::authorise('core.create', $this->option)) : ?>
                            <?php
                            $newFolderCls = 'icon-folder-new media-files-action'
                                . ' media-folder-new hasTip';
                            if ($this->layout == 'list') {
                                $newFolderCls .= ' active';
                            }
                            $newFolderUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&task=new&tmpl=' . $tmpl
                                . '&' . $token . '=1'
                            );
                            $folderPrompt = Lang::txt('COM_MEDIA_FOLDER_NAME');
                            $createLabel = Lang::txt('COM_MEDIA_CREATE_FOLDER');
                            ?>
                            <a class="<?php echo $newFolderCls; ?>"
                                href="<?php echo $newFolderUrl; ?>"
                                data-prompt="<?php echo $folderPrompt; ?>"
                                title="<?php echo $createLabel; ?>">
                                <?php echo $createLabel; ?>
                            </a>
                        <?php endif; ?>
                        <?php if (User::authorise('core.create', $this->option)) : ?>
                            <?php
                            $this->js('jquery.fileuploader.js', 'system');
                            $uploadAction = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=media&task=upload&tmpl=' . $tmpl
                                . '&' . $token . '=1'
                            );
                            $uploadList = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=medialist&task=display&tmpl=' . $tmpl
                                . '&' . $token . '=1'
                            );
                            $uploadInstr = Lang::txt('COM_MEDIA_UPLOAD_INSTRUCTIONS');
                            $uploadBtn = Lang::txt('COM_MEDIA_UPLOAD_INSTRUCTIONS_BTN');
                            ?>
                            <div id="ajax-uploader"
                                data-action="<?php echo $uploadAction; ?>"
                                data-list="<?php echo $uploadList; ?>"
                                data-instructions="<?php echo $uploadInstr; ?>"
                                data-instructions-btn="<?php echo $uploadBtn; ?>">
                                <noscript>
                                    <div class="input-wrap">
                                        <label for="upload">
                                            <?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?>:
                                        </label>
                                        <input type="file" name="upload" id="upload" />
                                    </div>
                                </noscript>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="media-view">
                    <?php
                    $itemsListUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=medialist&task=display&tmpl=' . $tmpl
                        . '&' . $token . '=1'
                    );
                    ?>
                    <div class="media-items" id="media-items"
                        data-tmpl="<?php echo $this->escape($tmpl); ?>"
                        data-list="<?php echo $itemsListUrl; ?>">
                        <?php
                        $children = \Components\Media\Admin\Helpers\MediaHelper::getChildren(COM_MEDIA_BASE, '');

                        $this->view('default', 'medialist')
                            ->set('folder', $this->folder)
                            ->set('children', $children)
                            ->set('layout', $this->layout)
                            ->display();
                        ?>
                    </div>
                </div>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="option"
                    value="<?php echo $this->option; ?>" />
                <input type="hidden" name="token"
                    value="<?php echo $token; ?>" />
                <input type="hidden" name="folder" id="folder"
                    value="<?php echo $this->escape($this->folder); ?>" />
                <input type="hidden" name="layout" id="layout"
                    value="<?php echo $this->escape($this->layout); ?>" />
                <input type="hidden" name="tmpl" id="tmpl"
                    value="<?php echo $this->escape($tmpl); ?>" />
                <?php if ($field = Request::getCmd('e_name')) : ?>
                    <input type="hidden" name="e_name" id="e_name"
                        value="<?php echo $this->escape($field); ?>" />
                <?php endif; ?>
                <?php if ($field = Request::getCmd('fieldid')) : ?>
                    <input type="hidden" name="fieldid" id="fieldid"
                        value="<?php echo $this->escape($field); ?>" />
                <?php endif; ?>
                <?php echo Html::input('token'); ?>
            </form>
        </div><!-- / .panel-files -->
    </div><!-- / .media-panels -->
</div>
