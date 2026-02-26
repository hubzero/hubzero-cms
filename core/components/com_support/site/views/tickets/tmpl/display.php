<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access.
defined('_HZEXEC_') or die();

$live_site = rtrim(Request::base(), '/');

$this->css()
     ->css('conditions.css')
     ->js('jquery.hoverIntent.js', 'system')
     ->js('json2.js')
     ->js('condition.builder.js')
     ->js('tickets.js');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if ($this->acl->check('read', 'tickets')) { ?>
            <li>
                <?php
                $statsUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller . '&task=stats'
                );
                ?>
                <a class="icon-stats stats btn" href="<?php echo $statsUrl; ?>">
                    <?php echo Lang::txt('COM_SUPPORT_STATS'); ?>
                </a>
            </li>
        <?php } ?>
            <li class="last">
                <?php
                $newUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller . '&task=new'
                );
                ?>
                <a class="icon-add add btn" href="<?php echo $newUrl; ?>">
                    <?php echo Lang::txt('COM_SUPPORT_NEW_TICKET'); ?>
                </a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="panel tickets">
    <div class="panel-row">

        <?php
        $updateUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1'
        );
        ?>
        <div class="pane pane-queries" id="queries" data-update="<?php echo $updateUrl; ?>">
            <div class="pane-inner">

                <?php if ($this->acl->check('read', 'tickets')) { ?>
                    <?php
                    $showCurrent = intval($this->filters['show']);
                    $searchStr   = $this->filters['search'];
                    $watchOpenUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=display&show=-1&limitstart=0'
                        . ($showCurrent != -1 ? '&search=' : '')
                    );
                    $watchClosedUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=display&show=-2&limitstart=0'
                        . ($showCurrent != -2 ? '&search=' : '')
                    );
                    ?>
                    <ul id="watch-list">
                        <li id="folder_watching" class="open">
                            <span class="icon-watch folder">
                                <?php echo Lang::txt('COM_SUPPORT_WATCH_LIST'); ?>
                            </span>
                            <ul id="queries_watching" class="wqueries">
                                <li<?php if ($showCurrent == -1) {
                                    echo ' class="active"';
                                   }?>>
                                    <a class="aquery" href="<?php echo $watchOpenUrl; ?>">
                                        <?php echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_OPEN')); ?>
                                        <span><?php echo $this->watch['open']; ?></span>
                                    </a>
                                </li>
                                <li<?php if ($showCurrent == -2) {
                                    echo ' class="active"';
                                   }?>>
                                    <a class="aquery" href="<?php echo $watchClosedUrl; ?>">
                                        <?php
                                        echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_CLOSED'));
                                        ?>
                                        <span><?php echo $this->watch['closed']; ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php } ?>

                <ul id="query-list">
                    <?php if (count($this->folders) > 0) { ?>
                        <?php foreach ($this->folders as $folder) { ?>
                            <li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
                                <?php
                                $folderId    = $this->escape($folder->id);
                                $folderTitle = $this->escape($folder->title);
                                $tok         = Session::getFormToken();
                                ?>
                                <span
                                    class="icon-folder folder"
                                    id="<?php echo $folderId; ?>-title"
                                    data-id="<?php echo $folderId; ?>"
                                ><?php echo $folderTitle; ?></span>
                                <?php if ($this->acl->check('read', 'tickets')) { ?>
                                    <?php
                                    $delFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=removefolder'
                                        . '&id=' . $folder->id . '&' . $tok . '=1'
                                    );
                                    $editFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=editfolder'
                                        . '&id=' . $folder->id . '&tmpl=component&' . $tok . '=1'
                                    );
                                    $saveFolderUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&controller=queries&task=savefolder'
                                        . '&' . $tok . '=1&fields[id]=' . $folder->id
                                    );
                                    $delConfirm  = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                                    $folderLabel = Lang::txt('COM_SUPPORT_FOLDER_NAME');
                                    ?>
                                    <span class="folder-options">
                                        <a
                                            class="delete"
                                            href="<?php echo $delFolderUrl; ?>"
                                            data-confirm="<?php echo $delConfirm; ?>"
                                            title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                                        >
                                            <?php echo Lang::txt('JACTION_DELETE'); ?>
                                        </a>
                                        <a
                                            class="edit editfolder"
                                            data-id="<?php echo $folderId; ?>"
                                            href="<?php echo $editFolderUrl; ?>"
                                            data-href="<?php echo $saveFolderUrl; ?>"
                                            data-name="<?php echo $folderLabel; ?>"
                                            title="<?php echo Lang::txt('JACTION_EDIT'); ?>"
                                        >
                                            <?php echo Lang::txt('JACTION_EDIT'); ?>
                                        </a>
                                    </span>
                                <?php } ?>
                                <ul
                                    id="queries_<?php echo $this->escape($folder->id); ?>"
                                    class="queries"
                                >
                                    <?php foreach ($folder->queries as $query) { ?>
                                        <?php
                                        $qId         = $this->escape($query->id);
                                        $isActiveQ   = (intval($this->filters['show']) == $query->id);
                                        $qUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&controller=' . $this->controller
                                            . '&task=display&show=' . $query->id
                                            . (!$isActiveQ ? '&search=&limitstart=0' : '')
                                        );
                                        ?>
                                        <li
                                            id="query_<?php echo $qId; ?>"
                                            <?php if ($isActiveQ) {
                                                echo ' class="active"';
                                            }?>
                                        >
                                            <a class="aquery" href="<?php echo $qUrl; ?>">
                                                <?php echo $this->escape(stripslashes($query->title)); ?>
                                                <span><?php echo $query->get('count'); ?></span>
                                            </a>
                                            <?php if ($this->acl->check('read', 'tickets')) { ?>
                                                <?php
                                                $delQueryUrl = Route::url(
                                                    'index.php?option=' . $this->option
                                                    . '&controller=queries&task=remove'
                                                    . '&id=' . $query->id . '&' . $tok . '=1'
                                                );
                                                $editQueryUrl = Route::url(
                                                    'index.php?option=' . $this->option
                                                    . '&controller=queries&task=edit'
                                                    . '&id=' . $query->id . '&tmpl=component&' . $tok . '=1'
                                                );
                                                ?>
                                                <span class="query-options">
                                                    <a
                                                        class="delete"
                                                        href="<?php echo $delQueryUrl; ?>"
                                                        data-confirm="<?php echo $delConfirm; ?>"
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
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <?php if ($this->acl->check('read', 'tickets')) { ?>
                    <?php
                    $addQueryUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=queries&task=add&' . $tok . '=1'
                    );
                    $addFolderUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=queries&task=addfolder&' . $tok . '=1'
                    );
                    $saveFolderNewUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=queries&task=savefolder&' . $tok . '=1'
                    );
                    $addQueryTitle  = Lang::txt('COM_SUPPORT_ADD_QUERY');
                    $addFolderTitle = Lang::txt('COM_SUPPORT_ADD_FOLDER');
                    $folderLabel    = Lang::txt('COM_SUPPORT_FOLDER_NAME');
                    ?>
                    <ul class="controls">
                        <li>
                            <a
                                class="icon-list modal"
                                id="new-query"
                                href="<?php echo $addQueryUrl; ?>"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                                title="<?php echo $addQueryTitle; ?>"
                            >
                                <?php echo $addQueryTitle; ?>
                            </a>
                        </li>
                        <li>
                            <a
                                class="icon-folder"
                                id="new-folder"
                                href="<?php echo $addFolderUrl; ?>"
                                data-href="<?php echo $saveFolderNewUrl; ?>"
                                data-name="<?php echo $folderLabel; ?>"
                                title="<?php echo $addFolderTitle; ?>"
                            >
                                <?php echo $addFolderTitle; ?>
                            </a>
                        </li>
                    </ul>
                <?php } ?>

            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->
        <div class="pane pane-list">
            <div class="pane-inner" id="tickets">
                <?php
                $formAction = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller . '&task=display'
                );
                $direction  = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc';
                $sortBase   = 'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=display&show=' . $this->filters['show']
                    . '&search=' . $this->filters['search']
                    . '&sortdir=' . $direction
                    . '&limit=' . $this->filters['limit'] . '&limitstart=0';
                $sortUrl = array(
                    'created'  => Route::url($sortBase . '&sort=created'),
                    'status'   => Route::url($sortBase . '&sort=status'),
                    'severity' => Route::url($sortBase . '&sort=severity'),
                    'summary'  => Route::url($sortBase . '&sort=summary'),
                    'group'    => Route::url($sortBase . '&sort=group'),
                    'owner'    => Route::url($sortBase . '&sort=owner'),
                );
                $clickToSort = Lang::txt('COM_SUPPORT_CLICK_TO_SORT');
                $curSort     = $this->filters['sort'];
                $sortDir     = strtolower($this->filters['sortdir']);
                ?>
                <form action="<?php echo $formAction; ?>" method="post" id="ticketForm">
                    <div class="list-options">
                        <ul class="sort-options">
                            <li>
                                <span class="sort-header">
                                    <?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?>
                                </span>
                                <ul>
                                    <li>
                                        <?php
                                        $ageClass = 'sort-age'
                                            . ($curSort == 'created' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $ageClass; ?>"
                                            href="<?php echo $sortUrl['created']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $statusClass = 'sort-status'
                                            . ($curSort == 'status' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $statusClass; ?>"
                                            href="<?php echo $sortUrl['status']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $sevClass = 'sort-severity'
                                            . ($curSort == 'severity' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $sevClass; ?>"
                                            href="<?php echo $sortUrl['severity']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $sumClass = 'sort-summary'
                                            . ($curSort == 'summary' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $sumClass; ?>"
                                            href="<?php echo $sortUrl['summary']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $grpClass = 'sort-group'
                                            . ($curSort == 'group' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $grpClass; ?>"
                                            href="<?php echo $sortUrl['group']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $ownClass = 'sort-owner'
                                            . ($curSort == 'owner' ? ' active ' . $sortDir : '');
                                        ?>
                                        <a
                                            class="<?php echo $ownClass; ?>"
                                            href="<?php echo $sortUrl['owner']; ?>"
                                            title="<?php echo $clickToSort; ?>"
                                        >
                                            <?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <fieldset id="filter-bar">
                            <label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
                            <input
                                type="text"
                                name="search"
                                id="filter_search"
                                value="<?php echo $this->escape($this->filters['search']); ?>"
                                placeholder="<?php echo Lang::txt('COM_SUPPORT_SEARCH_THIS_QUERY'); ?>"
                            />

                            <input
                                type="hidden"
                                name="sort"
                                value="<?php echo $this->escape($this->filters['sort']); ?>"
                            />
                            <input
                                type="hidden"
                                name="sortdir"
                                value="<?php echo $this->escape($this->filters['sortdir']); ?>"
                            />
                            <input
                                type="hidden"
                                name="show"
                                value="<?php echo $this->escape($this->filters['show']); ?>"
                            />

                            <input
                                type="submit"
                                class="submit"
                                value="<?php echo Lang::txt('COM_SUPPORT_GO'); ?>"
                            />
                        </fieldset>
                    </div>
                    <table id="tktlist">
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <?php
                                    $pageNav = $this->pagination(
                                        $this->total,
                                        $this->filters['start'],
                                        $this->filters['limit']
                                    );
                                    $pageNav->setAdditionalUrlParam('show', $this->filters['show']);
                                    $pageNav->setAdditionalUrlParam('search', $this->filters['search']);
                                    echo $pageNav->render();
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                    <?php
                    $k = 0;
                    if (count($this->rows) > 0) {
                        $cls = 'even';

                        $i = 0;
                        $statuses = array();
                        foreach ($this->rows as $row) {
                            // Was there any activity on this item?
                            $lastcomment = $row->comments()
                                ->order('created', 'desc')
                                ->row()
                                ->get('created');

                            $tags = $row->tags('linkedlist');

                            if (!in_array($row->status->get('id'), $statuses)) {
                                $statuses[] = $row->status->get('id');
                                $statusColor = $row->status->get('color');
                                $statusId    = $row->status->get('id');
                                $this->css(
                                    '#tktlist tbody tr td.status-' . $statusId
                                    . ' { border-left-color: #' . $statusColor . '; }'
                                );
                            }
                            $rowStatusId    = $row->status->get('id');
                            $rowStatusText  = $row->status->get('text');
                            $rowStatusClass = $row->status->get('class');
                            $rowIsOpen      = $row->isOpen();
                            $rowOpenClass   = ($rowIsOpen ? 'open' : 'closed') . ' ' . $rowStatusClass;
                            $tipTitle       = Lang::txt('COM_SUPPORT_DETAILS');
                            $tipStatusLabel = Lang::txt('COM_SUPPORT_COL_STATUS') . ': ' . $rowStatusText;
                            ?>
                            <tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
                                <td class="status-<?php echo $rowStatusId; ?>">
                                    <span
                                        class="hasTip"
                                        title="<?php echo $tipTitle; ?> :: <?php echo $tipStatusLabel; ?>"
                                    >
                                        <span class="ticket-id">
                                            <?php echo $row->get('id'); ?>
                                        </span>
                                        <span class="<?php echo $rowOpenClass; ?> status">
                                            <?php
                                            echo $rowStatusText;
                                            if (!$rowIsOpen) {
                                                echo ' (' . $this->escape($row->get('resolved')) . ')';
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        $targetDate = $row->get('target_date');
                                        $hasTarget  = ($targetDate && $targetDate != '0000-00-00 00:00:00');
                                        if ($hasTarget) {
                                            $targetDateObj    = Date::of($targetDate);
                                            $targetDateLocal  = $targetDateObj->toLocal(
                                                Lang::txt('DATE_FORMAT_HZ1')
                                            );
                                            $targetDateFormat = $targetDateObj->format('Y-m-d\TH:i:s\Z');
                                            $targetTip = Lang::txt('COM_SUPPORT_TARGET_DATE', $targetDateLocal);
                                            ?>
                                            <span
                                                class="ticket-target_date tooltips"
                                                title="<?php echo $targetTip; ?>"
                                            >
                                                <time datetime="<?php echo $targetDateFormat; ?>">
                                                    <?php echo $targetDateLocal; ?>
                                                </time>
                                            </span>
                                        <?php } ?>
                                    </span>
                                </td>
                                <td colspan="6">
                                    <p>
                                        <span class="ticket-author">
                                            <?php
                                            echo $this->escape($row->get('name'));
                                            if ($row->submitter->get('id')) {
                                                $subUrl   = Route::url(
                                                    'index.php?option=com_members&id='
                                                    . $row->submitter->get('id')
                                                );
                                                $subLogin = $this->escape($row->get('login'));
                                                echo ' (<a href="' . $subUrl . '">'
                                                    . $subLogin . '</a>)';
                                            } elseif ($row->get('login')) {
                                                echo ' (' . $this->escape($row->get('login')) . ')';
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        $createdDate = Date::of($row->get('created'));
                                        $createdFmt  = $createdDate->format('Y-m-d\TH:i:s\Z');
                                        ?>
                                        <span class="ticket-datetime">
                                            @ <time datetime="<?php echo $createdFmt; ?>">
                                                <?php echo $createdDate->toLocal(); ?>
                                            </time>
                                        </span>
                                        <?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
                                            <?php
                                            $lastDate = Date::of($lastcomment);
                                            $lastFmt  = $lastDate->format('Y-m-d\TH:i:s\Z');
                                            ?>
                                            <span class="ticket-activity">
                                                <time datetime="<?php echo $lastFmt; ?>">
                                                    <?php echo $lastDate->relative(); ?>
                                                </time>
                                            </span>
                                        <?php } ?>
                                    </p>
                                    <p>
                                        <?php
                                        $rowContent = $this->escape(
                                            str_replace(
                                                array('<br />', '&amp;'),
                                                array('', '&'),
                                                $row->content
                                            )
                                        );
                                        $rowLink = Route::url(
                                            $row->link() . '&show=' . $this->filters['show']
                                            . '&search=' . $this->filters['search']
                                            . '&limit=' . $this->filters['limit']
                                            . '&limitstart=' . $this->filters['start']
                                        );
                                        $rowSummary = $row->content
                                            ? \Hubzero\Utility\Str::truncate(strip_tags($row->content), 200)
                                            : Lang::txt('COM_SUPPORT_NO_CONTENT_FOUND');
                                        ?>
                                        <a
                                            class="ticket-content"
                                            title="<?php echo $rowContent; ?>"
                                            href="<?php echo $rowLink; ?>"
                                        >
                                            <?php echo $rowSummary; ?>
                                        </a>
                                    </p>
                                    <?php if ($tags || $row->isOwned() || $row->get('group_id')) { ?>
                                        <p class="ticket-details">
                                        <?php if ($this->acl->check('update', 'tickets') && $tags) { ?>
                                            <span class="ticket-tags">
                                                <?php echo $tags; ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($row->get('group_id')) { ?>
                                            <span class="ticket-group">
                                                <?php
                                                $gname = Lang::txt('COM_SUPPORT_UNKNOWN');
                                                if (
                                                    $group = \Hubzero\User\Group::getInstance(
                                                        $row->get('group_id')
                                                    )
                                                ) {
                                                    $gname = $group->get('cn');
                                                }
                                                echo $this->escape($gname);
                                                ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($row->isOwned()) { ?>
                                            <?php
                                            $assigneePhoto = $row->assignee->picture();
                                            $assigneeUser  = $this->escape(
                                                stripslashes($row->assignee->get('username', ''))
                                            );
                                            $assigneeOrg   = $this->escape(
                                                stripslashes(
                                                    $row->assignee->get(
                                                        'organization',
                                                        Lang::txt('COM_SUPPORT_UNKNOWN')
                                                    )
                                                )
                                            );
                                            $ownerTip = Lang::txt('COM_SUPPORT_ASSIGNED_TO')
                                                . '::'
                                                . '<img border=&quot;1&quot; src=&quot;' . $assigneePhoto
                                                . '&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot;'
                                                . ' width=&quot;40&quot; height=&quot;40&quot;'
                                                . ' style=&quot;float: left; margin-right: 0.5em;&quot; />'
                                                . $assigneeUser . '<br />' . $assigneeOrg;
                                            ?>
                                            <span
                                                class="ticket-owner hasTip"
                                                title="<?php echo $ownerTip; ?>"
                                            >
                                                <?php
                                                echo $this->escape(
                                                    stripslashes($row->assignee->get('name', ''))
                                                );
                                                ?>
                                            </span>
                                        <?php } ?>
                                        </p>
                                    <?php } ?>
                                </td>
                                <td class="tkt-severity">
                                    <?php
                                    $sev      = $this->escape($row->get('severity', 'normal'));
                                    $sevTip   = Lang::txt('COM_SUPPORT_PRIORITY') . ':&nbsp;' . $sev;
                                    ?>
                                    <span
                                        class="ticket-severity <?php echo $sev; ?> hasTip"
                                        title="<?php echo $sevTip; ?>"
                                    >
                                        <span><?php echo $sev; ?></span>
                                    </span>
                                    <?php if ($this->acl->check('delete', 'tickets')) { ?>
                                        <?php
                                        $deleteUrl  = Route::url($row->link('delete'));
                                        $delConfirm = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                                        ?>
                                        <a
                                            class="delete"
                                            href="<?php echo $deleteUrl; ?>"
                                            data-confirm="<?php echo $delConfirm; ?>"
                                            title="<?php echo Lang::txt('JACTION_DELETE'); ?>"
                                        >
                                            <?php echo Lang::txt('JACTION_DELETE'); ?>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                            $k = 1 - $k;
                        }
                    } else {
                        ?>
                            <tr class="odd noresults">
                                <td colspan="7">
                                    <?php echo Lang::txt('COM_SUPPORT_NO_RESULTS_FOUND'); ?>
                                </td>
                            </tr>
                        <?php
                    }
                    ?>
                        </tbody>
                    </table>

                    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="task" value="display" />
                </form>
            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->
    </div><!-- / .panel-row -->
</section><!-- / .panel -->
