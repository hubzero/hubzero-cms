<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl) :
    $t .= '&tmpl=' . $tmpl;
endif;

// Get a shortened name
$name = $this->currentFolder['name'];
if ($tmpl == 'component' && strlen($name) > 10) :
    $name = substr($name, 0, 10) . ' ... ';
endif;

// Last modified time
$this->currentFolder['modified'] = filemtime(
    COM_MEDIA_BASE . $this->currentFolder['path']
);
$modified = Date::of($this->currentFolder['modified']);

$token = Session::getFormToken();
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
    'index.php?option=' . $this->option
    . '&controller=medialist' . $t
    . '&' . $token . '=1&folder=/' . $folderPath
);

$tdWidth = ($tmpl == 'component'
    && !User::authorise('core.delete', 'com_media'))
    ? '70' : '60';
?>
    <tr class="media-item media-item-list">
        <td width="<?php echo $tdWidth; ?>%">
            <a class="folder-item"
                data-folder="<?php echo $dataFolder; ?>"
                href="<?php echo $folderUrl; ?>">
                <span class="media-icon">
                    <img src="<?php echo $folderIcon; ?>"
                        alt="<?php echo $escapedName; ?>" />
                </span>
                <span class="media-name">
                    <?php echo $this->escape($name); ?>
                </span>
            </a>
        </td>
        <td>
            <!-- Nothing here -->
        </td>
    <?php if ($tmpl != 'component') : ?>
        <td>
            <span class="media-type"><?php echo Lang::txt('Folder'); ?></span>
        </td>
        <td>
            <time class="media-modified"
                datetime="<?php echo $modified->format('Y-m-d\TH:i:s\Z'); ?>">
                <?php echo $modified->toSql(); ?>
            </time>
        </td>
    <?php endif; ?>
    <?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')) : ?>
        <td>
            <div class="media-preview-inner">
                <span class="media-options-btn"></span>
                <div class="media-options">
                    <ul>
                        <?php if ($tmpl != 'component') : ?>
                            <li>
                                <?php
                                $infoUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=medialist&task=info' . $t
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
                                    . '&task=delete' . $t
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
            </div>
        </td>
    <?php endif; ?>
    </tr>
