<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Get a shortened name
$name = $this->currentFolder['name'];
if (strlen($name) > 10) :
    $name = substr($name, 0, 10) . ' ... ';
endif;

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl) :
    $t .= '&tmpl=' . $tmpl;
endif;

// Folder link
$token = Session::getFormToken();
$href = Route::url(
    'index.php?option=' . $this->option
    . '&task=download&' . $token . '=1'
    . '&folder=' . urlencode('/' . $this->currentFolder['path'])
);

$folderPath = ltrim($this->currentFolder['path'], '/');
$dataFolder = $this->escape('/' . $folderPath);
$folderIcon = Html::asset(
    'image',
    'assets/filetypes/folder.svg',
    '',
    null,
    true,
    true
);
$escapedName = $this->escape($this->currentFolder['name']);

$folderUrl = Route::url(
    'index.php?option=com_media&controller=medialist'
    . '&tmpl=' . $tmpl
    . '&' . $token . '=1&folder=/' . $folderPath
);
?>
        <div class="media-item media-item-thumb">
            <div class="media-preview">
                <div class="media-preview-inner">
                    <a class="media-thumb folder-item"
                        data-folder="<?php echo $dataFolder; ?>"
                        href="<?php echo $folderUrl; ?>">
                        <span class="media-preview-shim"></span><!--
                        --><img src="<?php echo $folderIcon; ?>"
                            alt="<?php echo $escapedName; ?>"
                            width="80" />
                    </a>
                    <span class="media-options-btn"></span>
                </div>
            </div>
            <div class="media-info">
                <div class="media-name">
                    <?php echo $this->escape($name); ?>
                </div>
                <?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')) : ?>
                    <div class="media-options">
                        <ul>
                            <?php if ($tmpl != 'component') : ?>
                                <li>
                                    <?php
                                    $infoUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=medialist&task=info'
                                        . '&tmpl=' . $tmpl
                                        . '&' . $token . '=1'
                                        . '&folder=' . urlencode($this->currentFolder['path'])
                                    );
                                    ?>
                                    <a class="icon-info media-opt-info"
                                        href="<?php echo $infoUrl; ?>">
                                        <?php echo Lang::txt('Info'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (User::authorise('core.delete', 'com_media')) : ?>
                                <li>
                                    <span class="separator"></span>
                                </li>
                                <li>
                                    <?php
                                    $deleteUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&task=delete&tmpl=' . $tmpl
                                        . '&' . $token . '=1'
                                        . '&rm=' . urlencode($this->currentFolder['path'])
                                    );
                                    $deleteLabel = Lang::txt('JACTION_DELETE');
                                    ?>
                                    <a class="icon-trash media-opt-delete"
                                        href="<?php echo $deleteUrl; ?>">
                                        <?php echo $deleteLabel; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
