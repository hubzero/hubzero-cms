<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
    <li class="error">Error: <?php echo $this->getError(); ?></li>
<?php }
if (count($this->folders) > 0) { ?>
    <?php foreach ($this->folders as $folder) { ?>
        <?php
        $folderId    = $this->escape($folder->id);
        $folderTitle = $this->escape($folder->title);
        $tok         = Session::getFormToken();
        $delFolderUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . $tok . '=1'
        );
        $editFolderUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . $tok . '=1'
        );
        $saveFolderUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=queries&task=savefolder&' . $tok . '=1&fields[id]=' . $folder->id
        );
        ?>
        <li id="folder_<?php echo $folderId; ?>" class="open">
            <span
                class="icon-folder folder"
                id="<?php echo $folderId; ?>-title"
                data-id="<?php echo $folderId; ?>"
            ><?php echo $folderTitle; ?></span>
            <span class="folder-options">
                <a
                    class="delete"
                    href="<?php echo $delFolderUrl; ?>"
                    title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                >
                    <?php echo Lang::txt('JACTION_DELETE'); ?>
                </a>
                <a
                    class="edit editfolder"
                    data-id="<?php echo $folderId; ?>"
                    href="<?php echo $editFolderUrl; ?>"
                    data-href="<?php echo $saveFolderUrl; ?>"
                    title="<?php echo Lang::txt('JACTION_EDIT'); ?>"
                >
                    <?php echo Lang::txt('JACTION_EDIT'); ?>
                </a>
            </span>
            <ul id="queries_<?php echo $folderId; ?>" class="queries">
                <?php
                foreach ($folder->queries()->order('ordering', 'asc')->rows() as $query) {
                    $isActive = ($this->show == $query->id);
                    $qUrl     = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=tickets&task=display&show=' . $query->id
                        . (!$isActive ? '&search=' : '')
                    );
                    $delQueryUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=queries&task=remove&id=' . $query->id . '&' . $tok . '=1'
                    );
                    $editQueryUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=queries&task=edit&id=' . $query->id
                        . '&tmpl=component&' . $tok . '=1'
                    );
                    $ticketCount = \Components\Support\Models\Ticket::countWithQuery($query, array());
                    ?>
                    <li
                        id="query_<?php echo $this->escape($query->id); ?>"
                        <?php if ($isActive) {
                            echo ' class="active"';
                        }?>
                    >
                        <a class="aquery" href="<?php echo $qUrl; ?>">
                            <?php echo $this->escape(stripslashes($query->title)); ?>
                            <span><?php echo $ticketCount; ?></span>
                        </a>
                        <span class="query-options">
                            <a
                                class="delete"
                                href="<?php echo $delQueryUrl; ?>"
                                title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                            >
                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                            </a>
                            <a
                                class="modal edit"
                                href="<?php echo $editQueryUrl; ?>"
                                title="<?php echo Lang::txt('JACTION_EDIT'); ?>"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                            >
                                <?php echo Lang::txt('JACTION_EDIT'); ?>
                            </a>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
<?php }
