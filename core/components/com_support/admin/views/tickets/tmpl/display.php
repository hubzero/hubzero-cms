<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS'), 'support');
Toolbar::preferences('com_support', '550');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('tickets');

Html::behavior('tooltip');

$this
    ->css()
    ->js('tickets.js');
?>

<div class="panel" id="panes">
    <div class="panel-row">

        <?php
        $saveorderingUrl = Route::url(
            'index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . Session::getFormToken()
                . '=1',
        );
        ?>
        <div class="pane pane-queries" id="queries" data-update="<?= $saveorderingUrl ?>">
            <div class="pane-inner">

                <ul id="watch-list">
                    <li id="folder_watching" class="open">
                        <span class="icon-watch folder"><?php echo Lang::txt('COM_SUPPORT_QUERIES_WATCHING'); ?></span>
                        <ul id="queries_watching" class="wqueries">
                            <li<?php echo intval($this->filters['show']) == -1 ? ' class="active"' : ''; ?>>
                                <?php
                                $linkUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&show=-1&limitstart=0'
                                        . (intval($this->filters['show']) != -1 ? '&search=' : ''),
                                );
                                ?>
                                <a class="query" href="<?= $linkUrl ?>">
                                    <?php
                                    $watchingOpenText = $this->escape(Lang::txt('COM_SUPPORT_QUERIES_WATCHING_OPEN'));
                                    ?>
                                    <?= $watchingOpenText ?> <span><?= $this->watch['open'] ?></span>
                                </a>
                            </li>
                            <li<?php echo intval($this->filters['show']) == -2 ? ' class="active"' : ''; ?>>
                                <?php
                                $linkUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&show=-2&limitstart=0'
                                        . (intval($this->filters['show']) != -2 ? '&search=' : ''),
                                );
                                ?>
                                <a class="query" href="<?= $linkUrl ?>">
                                    <?php
                                    $watchingClosedText = $this->escape(
                                        Lang::txt('COM_SUPPORT_QUERIES_WATCHING_CLOSED'),
                                    );
                                    ?>
                                    <?= $watchingClosedText ?> <span><?= $this->watch['closed'] ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul id="query-list">
                    <?php if (count($this->folders) > 0) { ?>
                        <?php foreach ($this->folders as $folder) { ?>
                            <li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
                                <?php
                                $dataId = $this->escape($folder->id);
                                ?>
                                <span class="icon-folder folder" id="<?= $this->escape($folder->id) ?>-title"
                                    data-id="<?= $dataId ?>"><?= $this->escape($folder->title) ?></span>
                                <span class="folder-options">
                                    <?php
                                    $removefolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                            . '&controller=queries&task=removefolder&id=' . $folder->id . '&'
                                            . Session::getFormToken() . '=1',
                                    );
                                    ?>
                                    <a class="delete" href="<?= $removefolderUrl ?>"
                                        title="<?= Lang::txt('JACTION_DELETE') ?>">
                                        <?php echo Lang::txt('JACTION_DELETE'); ?>
                                    </a>
                                    <?php
                                    $editfolderUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=queries&task=editfolder&id='
                                            . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1',
                                    );
                                    $dataHrefUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=queries&task=savefolder&'
                                            . Session::getFormToken() . '=1&fields[id]=' . $folder->id,
                                    );
                                    ?>
                                    <a class="edit editfolder" data-id="<?= $this->escape($folder->id) ?>"
                                        href="<?= $editfolderUrl ?>" data-href="<?= $dataHrefUrl ?>"
                                        title="<?= Lang::txt('JACTION_EDIT') ?>">
                                        <?php echo Lang::txt('JACTION_EDIT'); ?>
                                    </a>
                                </span>
                                <ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
                                    <?php foreach ($folder->queries as $query) { ?>
                                        <?php
                                        $classAttr = intval($this->filters['show']) == $query->id
                                            ? ' class="active"'
                                            : '';
                                        ?>
                                        <li id="query_<?= $this->escape($query->id) ?>" <?= $classAttr ?>>
                                            <?php
                                            $linkUrl = Route::url(
                                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                    . '&show=' . $query->id
                                                    . (
                                                        intval($this->filters['show']) != $query->id
                                                            ? '&search=&limitstart=0'
                                                            : ''
                                                    ),
                                            );
                                            ?>
                                            <a class="query" href="<?= $linkUrl ?>">
                                                <?= $this->escape(stripslashes($query->title)) ?>
                                                    <span><?= $query->count ?></span>
                                            </a>
                                            <span class="query-options">
                                                <?php
                                                $removeUrl = Route::url(
                                                    'index.php?option=' . $this->option
                                                        . '&controller=queries&task=remove&id=' . $query->id . '&'
                                                        . Session::getFormToken() . '=1',
                                                );
                                                $confirmText = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                                                ?>
                                                <a class="delete" href="<?= $removeUrl ?>"
                                                    title="<?= Lang::txt('JACTION_DELETE') ?>"
                                                    data-confirm="<?= $confirmText ?>">
                                                    <?php echo Lang::txt('JACTION_DELETE'); ?>
                                                </a>
                                                <?php
                                                $editUrl = Route::url(
                                                    'index.php?option=' . $this->option
                                                        . '&controller=queries&task=edit&id=' . $query->id
                                                        . '&tmpl=component&' . Session::getFormToken() . '=1',
                                                );
                                                ?>
                                                <a class="modal edit" href="<?= $editUrl ?>"
                                                    title="<?= Lang::txt('JACTION_EDIT') ?>"
                                                    rel="{handler: 'iframe', size: {x: 570, y: 550}}">
                                                    <?php echo Lang::txt('JACTION_EDIT'); ?>
                                                </a>
                                            </span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

                <ul class="controls">
                    <li>
                        <?php
                        $addUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=queries&task=add&tmpl=component&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="icon-list modal" id="new-query" href="<?= $addUrl ?>"
                            rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                            title="<?= Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY') ?>">
                            <?php echo Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $savefolderUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=queries&task=savefolder&'
                                . Session::getFormToken() . '=1',
                        );
                        $addfolderUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=queries&task=addfolder&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="icon-folder" id="new-folder" href="<?= $addfolderUrl ?>"
                            data-href="<?= $savefolderUrl ?>" title="<?= Lang::txt('COM_SUPPORT_ADD_FOLDER') ?>"
                            data-ptompt="<?= Lang::txt('COM_SUPPORT_FOLDER_NAME') ?>">
                            <?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>
                        </a>
                    </li>
                    <?php /* <li>
                        <a class="icon-batch" id="new-batch" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tickets&task=batch&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>">
                            <?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>
                        </a>
                    </li> */ ?>
                </ul>

            </div>
        </div><!-- / .pane -->
        <div class="pane pane-list">
            <div class="pane-inner" id="tickets">

                <form
                    action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>"
                    method="post" name="adminForm" id="ticketForm">
                    <div class="list-options">
                        <?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
                        <ul class="sort-options">
                            <li>
                                <span class="sort-header"><?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?></span>
                                <ul>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'created'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('created','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'status'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('status','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'severity'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('severity','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'summary'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('summary','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'group'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('group','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $classAttr = $this->filters['sort'] == 'owner'
                                            ? ' class="active ' . strtolower($this->filters['sortdir']) . '"'
                                            : '';
                                        ?>
                                        <a<?= $classAttr ?>
                                            href="javascript:tableOrdering('owner','<?= $direction ?>','');"
                                            title="<?= Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <fieldset id="filter-bar">
                            <label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
                            <input type="text" name="search" id="filter_search" class="filter"
                                value="<?= $this->escape($this->filters['search']) ?>"
                                placeholder="<?= Lang::txt('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER') ?>" />

                            <input type="hidden" name="filter_order"
                                value="<?= $this->escape($this->filters['sort']) ?>" />
                            <input type="hidden" name="filter_order_Dir"
                                value="<?= $this->escape($this->filters['sortdir']) ?>" />
                            <input type="hidden" name="show" value="<?= $this->escape($this->filters['show']) ?>" />

                            <button type="submit"><?php echo Lang::txt('COM_SUPPORT_GO'); ?></button>
                        </fieldset>
                    </div>

                    <ul id="tktlist">
                    <?php
                    $k = 0;
                    $database = App::get('db');

                    $st = new \Components\Support\Models\Tags();

                // Collect all the IDs
                    $ids = [];
                    if ($this->rows) {
                        foreach ($this->rows as $row) {
                            $ids[] = $row->id;
                        }
                    }

                // Pull out the last activity date for all the IDs
                    $lastactivities = [];
                    if (count($ids)) {
                        $alltags = $st->checkTags($ids);
                    }

                    $tid = Request::getInt('ticket', 0);

                    $tsformat = $database->getDateFormat();
                    $statuses = [];

                    if (count($this->rows) > 0) {
                        $i = 0;
                        foreach ($this->rows as $row) {
                            if ($tid && $row->get('id') != $tid) {
                                continue;
                            }

                            $lastcomment = $row
                            ->comments()
                            ->order('created', 'desc')
                            ->row()
                            ->get('created');

                            $tags = '';
                            if (isset($alltags[$row->get('id')])) {
                                $tags = $row->tags('linkedlist');
                            }

                            if (!in_array($row->status->get('id'), $statuses)) {
                                $statuses[] = $row->status->get('id');
                                $this->css(
                                    '#tktlist li .status-' . $row->status->get('id') . ' { border-left-color: #'
                                        . $row->status->get('color') . '; }',
                                );
                            }
                            ?>
                        <li class="<?= !$row->isOpen() ? 'closed' : 'open' ?>" data-id="<?= $row->get('id') ?>"
                            id="ticket-<?= $row->get('id') ?>">
                            <div class="ticket-wrap status-<?php echo $row->status->get('id'); ?>">
                                <p>
                                    <input type="checkbox" name="id[]" id="cb<?= $i ?>" value="<?= $row->get('id') ?>"
                                        class="checkbox-toggle" />
                                    <span class="ticket-id">
                                        # <?php echo $row->get('id'); ?>
                                    </span>
                                    <?php
                                    $titleText = Lang::txt('COM_SUPPORT_TICKET_DETAILS') . ' :: ' . '<strong>'
                                        . Lang::txt('COM_SUPPORT_COL_STATUS') . ':</strong> '
                                        . $row->status->get('title')
                                        . (!$row->isOpen() ? ' (' . $this->escape($row->get('resolved')) . ')' : '');
                                    ?>
                                    <span class="<?= $row->status->get('alias') ?> status hasTip"
                                        title="<?= $titleText ?>">
                                            <?php echo $row->status->get('title');
                                            echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
                                    </span>
                                        <?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
                                        <span class="ticket-activity">
                                            <?php
                                            $timeText = Date::of($lastcomment)->relative();
                                            ?>
                                            <time datetime="<?= $lastcomment ?>"><?= $timeText ?></time>
                                        </span>
                                        <?php } ?>
                                        <?php
                                        if (
                                            $row->get('target_date')
                                            && $row->get('target_date') != '0000-00-00 00:00:00'
                                        ) { ?>
                                            <?php
                                            $titleText = Lang::txt(
                                                'Target date: %s',
                                                Date::of($row->get('target_date'))->toLocal(
                                                    Lang::txt('DATE_FORMAT_HZ1'),
                                                ),
                                            );
                                            ?>
                                        <span class="ticket-target_date hasTip" title="<?= $titleText ?>">
                                            <?php
                                            $datetime = Date::of($row->get('target_date'))->format('Y-m-d\TH:i:s\Z');
                                            $timeText = Date::of($row->get('target_date'))->toLocal(
                                                Lang::txt('DATE_FORMAT_HZ1'),
                                            );
                                            ?>
                                            <time datetime="<?= $datetime ?>"><?= $timeText ?></time>
                                        </span>
                                        <?php } ?>
                                </p>
                                <p>
                                    <span class="ticket-author">
                                            <?php echo $this->escape($row->get('name'));
                                            echo ($row->get('login')) ? ' (<a href="' . Route::url('index.php?option=com_members&task=edit&id=' . $this->escape($row->get('login'))) . '">' . $this->escape($row->get('login')) . '</a>)' : ''; ?>
                                    </span>
                                    <span class="ticket-datetime">
                                        @ <time
                                            datetime="<?= $row->get('created') ?>"><?= $row->get('created') ?></time>
                                    </span>
                                </p>
                                <p>
                                    <?php
                                    $editUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                                            . '&task=edit&id=' . $row->get('id'),
                                    );
                                    ?>
                                    <a class="ticket-content" href="<?= $editUrl ?>">
                                            <?= $this->escape(
                                                $row->get('summary', Lang::txt('COM_SUPPORT_TICKET_NO_CONTENT')),
                                            ) ?>
                                    </a>
                                </p>
                                    <?php if ($tags || $row->isOwned() || $row->get('group_id')) { ?>
                                    <p class="ticket-details">
                                        <?php if ($tags) { ?>
                                            <span class="ticket-tags">
                                                <?php echo $tags; ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($row->get('group_id')) { ?>
                                            <span class="ticket-group">
                                                <?php
                                                $gname = Lang::txt('COM_SUPPORT_UNKNOWN');
                                                if ($group = \Hubzero\User\Group::getInstance($row->get('group_id'))) {
                                                    $gname = $group->get('cn');
                                                }
                                                echo $this->escape($gname);
                                                ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($row->isOwned()) { ?>
                                            <?php
                                            $titleText = Lang::txt('COM_SUPPORT_TICKET_ASSIGNED_TO')
                                                . '::<img border=&quot;1&quot; src=&quot;' . $row->assignee->picture()
                                                . '&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; '
                                                . 'width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; '
                                                . 'margin-right: 0.5em;&quot; />'
                                                . $this->escape(stripslashes($row->assignee->get('username')))
                                                . '<br />'
                                                . $this->escape(
                                                    stripslashes(
                                                        $row->assignee->get(
                                                            'organization',
                                                            Lang::txt('COM_SUPPORT_USER_ORG_UNKNOWN'),
                                                        ),
                                                    ),
                                                );
                                            ?>
                                            <span class="ticket-owner hasTip" title="<?= $titleText ?>">
                                                <?php echo $this->escape(stripslashes($row->assignee->get('name'))); ?>
                                            </span>
                                        <?php } ?>
                                    </p>
                                    <?php } ?>
                                <p class="tkt-severity">
                                    <?php
                                    $extraClasses = $this->escape($row->get('severity', 'normal'));
                                    $titleText = '<strong>' . Lang::txt('COM_SUPPORT_TICKET_PRIORITY')
                                        . ':</strong>&nbsp;' . $this->escape($row->get('severity', 'normal'));
                                    ?>
                                    <span class="ticket-severity <?= $extraClasses ?> hasTip" title="<?= $titleText ?>">
                                        <span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
                                    </span>
                                </p>
                            </div>
                        </li>
                            <?php
                            $i++;
                            $k = 1 - $k;
                        }
                    } else {
                        ?>
                        <li>
                            <p class="no-records"><?php echo Lang::txt('No tickets found.'); ?></p>
                        </li>
                        <?php
                    }
                    ?>
                    </ul>

                    <?php
                    // Initiate paging
                    echo $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit'],
                    );
                    ?>

                    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
                    <input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />

                    <?php echo Html::input('token'); ?>
                </form>

            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->

        <div class="pane pane-item">
            <div class="pane-inner" id="ticket">
                <p class="instructions"><?php echo Lang::txt('Select a ticket from the list to view details.'); ?></p>
            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->

    </div><!-- / .panel-row -->
</div><!-- / .panel -->
