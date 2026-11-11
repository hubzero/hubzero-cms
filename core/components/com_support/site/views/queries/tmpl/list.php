<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{folders: mixed, show: mixed} $this
 */

if ($this->getError()) { ?>
    <li class="error">Error: <?php echo $this->getError(); ?></li>
<?php }
if (count($this->folders) > 0) { ?>
    <?php foreach ($this->folders as $folder) { ?>
        <li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
            <span class="icon-folder folder" id="<?= $this->escape($folder->id) ?>-title"
                data-id="<?= $this->escape($folder->id) ?>"><?= $this->escape($folder->title) ?></span>
            <span class="folder-options">
                <?php
                $removefolderUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id,
                );
                ?>
                <button type="submit" form="hz-post-action" formaction="<?= $removefolderUrl ?>" class="delete"
                    title="<?= Lang::txt('JACTION_DELETE') ?>"
                    data-confirm="<?= Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE') ?>">
                    <?php echo Lang::txt('JACTION_DELETE'); ?>
                </button>
                <?php
                $editfolderUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id
                        . '&tmpl=component&' . Session::getFormToken() . '=1',
                );
                $savefolderUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=queries&task=savefolder&'
                        . Session::getFormToken() . '=1&fields[id]=' . $folder->id,
                );
                ?>
                <a class="edit editfolder" data-id="<?= $this->escape($folder->id) ?>" href="<?= $editfolderUrl ?>"
                    data-href="<?= $savefolderUrl ?>" title="<?= Lang::txt('JACTION_EDIT') ?>">
                    <?php echo Lang::txt('JACTION_EDIT'); ?>
                </a>
            </span>
            <ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
                <?php
                foreach ($folder->queries()->order('ordering', 'asc')->rows() as $query) { ?>
                    <li id="query_<?= $this->escape($query->id) ?>"
                        <?= $this->show == $query->id ? ' class="active"' : '' ?>>
                        <?php
                        $displayUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=tickets&task=display&show=' . $query->id
                                . (intval($this->show) != $query->id ? '&search=' : ''),
                        );
                        ?>
                        <a class="aquery" href="<?= $displayUrl ?>">
                            <?php
                            $spanText = Components\Support\Models\Ticket::countWithQuery($query, []);
                            ?>
                            <?= $this->escape(stripslashes($query->title)) ?> <span><?= $spanText ?></span>
                        </a>
                        <span class="query-options">
                            <?php
                            $removeUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=queries&task=remove&id='
                                    . $query->id,
                            );
                            ?>
                            <button type="submit" form="hz-post-action" formaction="<?= $removeUrl ?>" class="delete"
                                title="<?= Lang::txt('JACTION_DELETE') ?>"
                                data-confirm="<?= Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE') ?>">
                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                            </button>
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id
                                    . '&tmpl=component&' . Session::getFormToken() . '=1',
                            );
                            ?>
                            <a class="modal edit" href="<?= $editUrl ?>" title="<?= Lang::txt('JACTION_EDIT') ?>"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}">
                                <?php echo Lang::txt('JACTION_EDIT'); ?>
                            </a>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
<?php }
