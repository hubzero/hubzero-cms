<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$toolbarTitle = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS');
Toolbar::title($toolbarTitle, 'support');
Toolbar::preferences('com_support', '550');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('tickets');

Html::behavior('tooltip');

$this->css()
    ->js('tickets.js');
?>

<div class="panel" id="panes">
    <div class="panel-row">

        <?php
        $saveOrderingUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1'
        );
        ?>
        <div
            class="pane pane-queries"
            id="queries"
            data-update="<?php echo $saveOrderingUrl; ?>"
        >
            <div class="pane-inner">

                <ul id="watch-list">
                    <li id="folder_watching" class="open">
                        <span class="icon-watch folder">
                            <?php echo Lang::txt('COM_SUPPORT_QUERIES_WATCHING'); ?>
                        </span>
                        <ul id="queries_watching" class="wqueries">
                            <?php
                            $watchOpenParam = (intval($this->filters['show']) != -1)
                                ? '&search=' : '';
                            $watchOpenUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&show=-1&limitstart=0' . $watchOpenParam
                            );
                            ?>
                            <li<?php if (intval($this->filters['show']) == -1) {
                                echo ' class="active"';
                               } ?>>
                                <a class="query" href="<?php echo $watchOpenUrl; ?>">
                                    <?php echo $this->escape(Lang::txt('COM_SUPPORT_QUERIES_WATCHING_OPEN')); ?>
                                    <span><?php echo $this->watch['open']; ?></span>
                                </a>
                            </li>
                            <?php
                            $watchClosedParam = (intval($this->filters['show']) != -2)
                                ? '&search=' : '';
                            $watchClosedUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&show=-2&limitstart=0' . $watchClosedParam
                            );
                            ?>
                            <li<?php if (intval($this->filters['show']) == -2) {
                                echo ' class="active"';
                               } ?>>
                                <a class="query" href="<?php echo $watchClosedUrl; ?>">
                                    <?php echo $this->escape(Lang::txt('COM_SUPPORT_QUERIES_WATCHING_CLOSED')); ?>
                                    <span><?php echo $this->watch['closed']; ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul id="query-list">
                    <?php if (count($this->folders) > 0) { ?>
                        <?php foreach ($this->folders as $folder) { ?>
                            <li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
                                <span
                                    class="icon-folder folder"
                                    id="<?php echo $this->escape($folder->id); ?>-title"
                                    data-id="<?php echo $this->escape($folder->id); ?>"
                                ><?php echo $this->escape($folder->title); ?></span>
                                <span class="folder-options">
                                    <?php
                                    $deleteFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=removefolder&id=' . $folder->id
                                        . '&' . Session::getFormToken() . '=1'
                                    );
                                    $editFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=editfolder&id=' . $folder->id
                                        . '&tmpl=component&' . Session::getFormToken() . '=1'
                                    );
                                    $saveFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=savefolder&'
                                        . Session::getFormToken() . '=1&fields[id]=' . $folder->id
                                    );
                                    ?>
                                    <a
                                        class="delete"
                                        href="<?php echo $deleteFolderUrl; ?>"
                                        title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                                    ><?php echo Lang::txt('JACTION_DELETE'); ?></a>
                                    <a
                                        class="edit editfolder"
                                        data-id="<?php echo $this->escape($folder->id); ?>"
                                        href="<?php echo $editFolderUrl; ?>"
                                        data-href="<?php echo $saveFolderUrl; ?>"
                                        title="<?php echo Lang::txt('JACTION_EDIT'); ?>"
                                    ><?php echo Lang::txt('JACTION_EDIT'); ?></a>
                                </span>
                                <ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
                                    <?php foreach ($folder->queries as $query) { ?>
                                        <li
                                            id="query_<?php echo $this->escape($query->id); ?>"
                                            <?php if (intval($this->filters['show']) == $query->id) {
                                                echo ' class="active"';
                                            } ?>
                                        >
                                            <?php
                                            $querySearchParam = (intval($this->filters['show']) != $query->id)
                                                ? '&search=&limitstart=0' : '';
                                            $queryUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&controller=' . $this->controller
                                                . '&show=' . $query->id . $querySearchParam
                                            );
                                            $deleteQueryUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&controller=queries&task=remove&id=' . $query->id
                                                . '&' . Session::getFormToken() . '=1'
                                            );
                                            $editQueryUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&controller=queries&task=edit&id=' . $query->id
                                                . '&tmpl=component&' . Session::getFormToken() . '=1'
                                            );
                                            $deleteConfirm = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                                            ?>
                                            <a class="query" href="<?php echo $queryUrl; ?>">
                                                <?php echo $this->escape(stripslashes($query->title)); ?>
                                                <span><?php echo $query->count; ?></span>
                                            </a>
                                            <span class="query-options">
                                                <a
                                                    class="delete"
                                                    href="<?php echo $deleteQueryUrl; ?>"
                                                    title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                                                    data-confirm="<?php echo $deleteConfirm; ?>"
                                                ><?php echo Lang::txt('JACTION_DELETE'); ?></a>
                                                <a
                                                    class="modal edit"
                                                    href="<?php echo $editQueryUrl; ?>"
                                                    title="<?php echo Lang::txt('JACTION_EDIT'); ?>"
                                                    rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                                                ><?php echo Lang::txt('JACTION_EDIT'); ?></a>
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
                        $newQueryUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=queries&task=add&tmpl=component&'
                            . Session::getFormToken() . '=1'
                        );
                        $addFolderUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=queries&task=addfolder&' . Session::getFormToken() . '=1'
                        );
                        $saveFolderRootUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1'
                        );
                        ?>
                        <a
                            class="icon-list modal"
                            id="new-query"
                            href="<?php echo $newQueryUrl; ?>"
                            rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                            title="<?php echo Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>"
                        ><?php echo Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?></a>
                    </li>
                    <li>
                        <a
                            class="icon-folder"
                            id="new-folder"
                            href="<?php echo $addFolderUrl; ?>"
                            data-href="<?php echo $saveFolderRootUrl; ?>"
                            title="<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>"
                            data-ptompt="<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>"
                        ><?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?></a>
                    </li>
                    <?php /*
                    <li>
                        <a class="icon-batch" id="new-batch"
                            href="<?php echo Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=tickets&task=batch&' . Session::getFormToken() . '=1'
                            ); ?>"
                            title="<?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>"
                        >
                            <?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>
                        </a>
                    </li>
                    */ ?>
                </ul>

            </div>
        </div><!-- / .pane -->
        <div class="pane pane-list">
            <div class="pane-inner" id="tickets">

                <?php
                $ticketFormAction = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                );
                ?>
                <form
                    action="<?php echo $ticketFormAction; ?>"
                    method="post"
                    name="adminForm"
                    id="ticketForm"
                >
                    <div class="list-options">
                        <?php
                        $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc';
                        $sortDir = strtolower($this->filters['sortdir']);
                        $sortClickLabel = Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT');
                        ?>
                        <ul class="sort-options">
                            <li>
                                <span class="sort-header">
                                    <?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?>
                                </span>
                                <ul>
                                    <li>
                                        <?php
                                        $createdSortClass = ($this->filters['sort'] == 'created')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $createdSortClass; ?>
                                            href="javascript:tableOrdering('created','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?></a>
                                    </li>
                                    <li>
                                        <?php
                                        $statusSortClass = ($this->filters['sort'] == 'status')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $statusSortClass; ?>
                                            href="javascript:tableOrdering('status','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?></a>
                                    </li>
                                    <li>
                                        <?php
                                        $severitySortClass = ($this->filters['sort'] == 'severity')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $severitySortClass; ?>
                                            href="javascript:tableOrdering('severity','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?></a>
                                    </li>
                                    <li>
                                        <?php
                                        $summarySortClass = ($this->filters['sort'] == 'summary')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $summarySortClass; ?>
                                            href="javascript:tableOrdering('summary','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?></a>
                                    </li>
                                    <li>
                                        <?php
                                        $groupSortClass = ($this->filters['sort'] == 'group')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $groupSortClass; ?>
                                            href="javascript:tableOrdering('group','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?></a>
                                    </li>
                                    <li>
                                        <?php
                                        $ownerSortClass = ($this->filters['sort'] == 'owner')
                                            ? ' class="active ' . $sortDir . '"' : '';
                                        ?>
                                        <a<?php echo $ownerSortClass; ?>
                                            href="javascript:tableOrdering('owner','<?php echo $direction; ?>','');"
                                            title="<?php echo $sortClickLabel; ?>"
                                        ><?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?></a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <fieldset id="filter-bar">
                            <label for="filter_search">
                                <?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:
                            </label>
                            <input
                                type="text"
                                name="search"
                                id="filter_search"
                                class="filter"
                                value="<?php echo $this->escape($this->filters['search']); ?>"
                                placeholder="<?php echo Lang::txt('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER'); ?>"
                            />

                            <input
                                type="hidden"
                                name="filter_order"
                                value="<?php echo $this->escape($this->filters['sort']); ?>"
                            />
                            <input
                                type="hidden"
                                name="filter_order_Dir"
                                value="<?php echo $this->escape($this->filters['sortdir']); ?>"
                            />
                            <input
                                type="hidden"
                                name="show"
                                value="<?php echo $this->escape($this->filters['show']); ?>"
                            />

                            <button type="submit"><?php echo Lang::txt('COM_SUPPORT_GO'); ?></button>
                        </fieldset>
                    </div>

                    <ul id="tktlist">
                    <?php
                    $k = 0;
                    $database = App::get('db');

                    $st = new \Components\Support\Models\Tags();

                // Collect all the IDs
                    $ids = array();
                    if ($this->rows) {
                        foreach ($this->rows as $row) {
                            $ids[] = $row->id;
                        }
                    }

                // Pull out the last activity date for all the IDs
                    $lastactivities = array();
                    if (count($ids)) {
                        $alltags = $st->checkTags($ids);
                    }

                    $tid = Request::getInt('ticket', 0);

                    $tsformat = $database->getDateFormat();
                    $statuses = array();

                    if (count($this->rows) > 0) {
                        $i = 0;
                        foreach ($this->rows as $row) {
                            if ($tid && $row->get('id') != $tid) {
                                continue;
                            }

                            $lastcomment = $row->comments()
                            ->order('created', 'desc')
                            ->row()
                            ->get('created');

                            $tags = '';
                            if (isset($alltags[$row->get('id')])) {
                                $tags = $row->tags('linkedlist');
                            }

                            if (!in_array($row->status->get('id'), $statuses)) {
                                $statuses[] = $row->status->get('id');
                                $statusCss = '#tktlist li .status-' . $row->status->get('id')
                                    . ' { border-left-color: #' . $row->status->get('color') . '; }';
                                $this->css($statusCss);
                            }
                            ?>
                        <li
                            class="<?php echo !$row->isOpen() ? 'closed' : 'open'; ?>"
                            data-id="<?php echo $row->get('id'); ?>"
                            id="ticket-<?php echo $row->get('id'); ?>"
                        >
                            <div class="ticket-wrap status-<?php echo $row->status->get('id'); ?>">
                                <p>
                                    <input
                                        type="checkbox"
                                        name="id[]"
                                        id="cb<?php echo $i; ?>"
                                        value="<?php echo $row->get('id'); ?>"
                                        class="checkbox-toggle"
                                    />
                                    <span class="ticket-id">
                                        # <?php echo $row->get('id'); ?>
                                    </span>
                                    <?php
                                    $statusTitle = Lang::txt('COM_SUPPORT_TICKET_DETAILS') . ' :: '
                                        . '<strong>' . Lang::txt('COM_SUPPORT_COL_STATUS') . ':</strong> '
                                        . $row->status->get('title');
                                    if (!$row->isOpen()) {
                                        $statusTitle .= ' (' . $this->escape($row->get('resolved')) . ')';
                                    }
                                    ?>
                                    <span
                                        class="<?php echo $row->status->get('alias'); ?> status hasTip"
                                        title="<?php echo $statusTitle; ?>"
                                    >
                                            <?php
                                            echo $row->status->get('title');
                                            if (!$row->isOpen()) {
                                                echo ' (' . $this->escape($row->get('resolved')) . ')';
                                            }
                                            ?>
                                    </span>
                                        <?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
                                        <span class="ticket-activity">
                                            <time datetime="<?php echo $lastcomment; ?>">
                                                <?php echo Date::of($lastcomment)->relative(); ?>
                                            </time>
                                        </span>
                                        <?php } ?>
                                        <?php
                                        $hasTargetDate = $row->get('target_date')
                                            && $row->get('target_date') != '0000-00-00 00:00:00';
                                        if ($hasTargetDate) {
                                            ?>
                                            <?php
                                            $targetDateFormatted = Date::of($row->get('target_date'))
                                            ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                                            $targetDateTitle = Lang::txt(
                                                'Target date: %s',
                                                $targetDateFormatted
                                            );
                                            $targetDatetime = Date::of($row->get('target_date'))
                                            ->format('Y-m-d\TH:i:s\Z');
                                            ?>
                                        <span
                                            class="ticket-target_date hasTip"
                                            title="<?php echo $targetDateTitle; ?>"
                                        >
                                            <time datetime="<?php echo $targetDatetime; ?>">
                                                <?php echo $targetDateFormatted; ?>
                                            </time>
                                        </span>
                                        <?php } ?>
                                </p>
                                <p>
                                    <span class="ticket-author">
                                            <?php
                                            echo $row->get('name');
                                            if ($row->get('login')) {
                                                $memberUrl = Route::url(
                                                    'index.php?option=com_members&task=edit&id='
                                                    . $this->escape($row->get('login'))
                                                );
                                                echo ' (<a href="' . $memberUrl . '">'
                                                    . $this->escape($row->get('login')) . '</a>)';
                                            }
                                            ?>
                                    </span>
                                    <span class="ticket-datetime">
                                        @ <time datetime="<?php echo $row->get('created'); ?>">
                                            <?php echo $row->get('created'); ?>
                                        </time>
                                    </span>
                                </p>
                                <p>
                                    <?php
                                    $ticketEditUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=' . $this->controller
                                        . '&task=edit&id=' . $row->get('id')
                                    );
                                    $ticketSummary = $this->escape(
                                        $row->get('summary', Lang::txt('COM_SUPPORT_TICKET_NO_CONTENT'))
                                    );
                                    ?>
                                    <a class="ticket-content" href="<?php echo $ticketEditUrl; ?>">
                                            <?php echo $ticketSummary; ?>
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
                                            $assigneeUsername = $this->escape(
                                                stripslashes($row->assignee->get('username'))
                                            );
                                            $assigneeOrg = $this->escape(
                                                stripslashes(
                                                    $row->assignee->get(
                                                        'organization',
                                                        Lang::txt('COM_SUPPORT_USER_ORG_UNKNOWN')
                                                    )
                                                )
                                            );
                                            $assigneePicture = $row->assignee->picture();
                                            $ownerTipImg = '<img border=&quot;1&quot;'
                                                . ' src=&quot;' . $assigneePicture . '&quot;'
                                                . ' name=&quot;imagelib&quot;'
                                                . ' alt=&quot;User photo&quot;'
                                                . ' width=&quot;40&quot; height=&quot;40&quot;'
                                                . ' style=&quot;float: left; margin-right: 0.5em;&quot; />';
                                            $ownerTip = Lang::txt('COM_SUPPORT_TICKET_ASSIGNED_TO')
                                                . '::' . $ownerTipImg
                                                . $assigneeUsername . '<br />' . $assigneeOrg;
                                            ?>
                                            <span
                                                class="ticket-owner hasTip"
                                                title="<?php echo $ownerTip; ?>"
                                            >
                                                <?php
                                                echo $this->escape(
                                                    stripslashes($row->assignee->get('name'))
                                                );
                                                ?>
                                            </span>
                                        <?php } ?>
                                    </p>
                                    <?php } ?>
                                <p class="tkt-severity">
                                    <?php
                                    $severity = $this->escape($row->get('severity', 'normal'));
                                    $severityTip = '<strong>' . Lang::txt('COM_SUPPORT_TICKET_PRIORITY')
                                        . ':</strong>&nbsp;' . $severity;
                                    ?>
                                    <span
                                        class="ticket-severity <?php echo $severity; ?> hasTip"
                                        title="<?php echo $severityTip; ?>"
                                    >
                                        <span><?php echo $severity; ?></span>
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
                        $this->filters['limit']
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
                <p class="instructions">
                    <?php echo Lang::txt('Select a ticket from the list to view details.'); ?>
                </p>
            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->

    </div><!-- / .panel-row -->
</div><!-- / .panel -->
