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

$this
     ->css()
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
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats',
                );
                ?>
                <a class="icon-stats stats btn" href="<?= $statsUrl ?>">
                    <?php echo Lang::txt('COM_SUPPORT_STATS'); ?>
                </a>
            </li>
        <?php } ?>
            <li class="last">
                <?php
                $newUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new',
                );
                ?>
                <a class="icon-add add btn" href="<?= $newUrl ?>">
                    <?php echo Lang::txt('COM_SUPPORT_NEW_TICKET'); ?>
                </a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="panel tickets">
    <div class="panel-row">

        <?php
        $saveorderingUrl = Route::url(
            'index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . Session::getFormToken()
                . '=1',
        );
        ?>
        <div class="pane pane-queries" id="queries" data-update="<?= $saveorderingUrl ?>">
            <div class="pane-inner">

                <?php if ($this->acl->check('read', 'tickets')) { ?>
                    <ul id="watch-list">
                        <li id="folder_watching" class="open">
                            <span class="icon-watch folder"><?php echo Lang::txt('COM_SUPPORT_WATCH_LIST'); ?></span>
                            <ul id="queries_watching" class="wqueries">
                                <li<?php echo intval($this->filters['show']) == -1 ? ' class="active"' : ''; ?>>
                                    <?php
                                    $displayUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                                            . '&task=display&show=-1&limitstart=0'
                                            . (intval($this->filters['show']) != -1 ? '&search=' : ''),
                                    );
                                    ?>
                                    <a class="aquery" href="<?= $displayUrl ?>">
                                        <?php
                                        $listOpenText = $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_OPEN'));
                                        ?>
                                        <?= $listOpenText ?> <span><?= $this->watch['open'] ?></span>
                                    </a>
                                </li>
                                <li<?php echo intval($this->filters['show']) == -2 ? ' class="active"' : ''; ?>>
                                    <?php
                                    $displayUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                                            . '&task=display&show=-2&limitstart=0'
                                            . (intval($this->filters['show']) != -2 ? '&search=' : ''),
                                    );
                                    ?>
                                    <a class="aquery" href="<?= $displayUrl ?>">
                                        <?php
                                        $listClosedText = $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_CLOSED'));
                                        ?>
                                        <?= $listClosedText ?> <span><?= $this->watch['closed'] ?></span>
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
                                $dataId = $this->escape($folder->id);
                                ?>
                                <span class="icon-folder folder" id="<?= $this->escape($folder->id) ?>-title"
                                    data-id="<?= $dataId ?>"><?= $this->escape($folder->title) ?></span>
                                <?php if ($this->acl->check('read', 'tickets')) { ?>
                                    <span class="folder-options">
                                        <?php
                                        $removefolderUrl = Route::url(
                                            'index.php?option=' . $this->option
                                                . '&controller=queries&task=removefolder&id=' . $folder->id,
                                        );
                                        ?>
                                        <button type="submit" form="hz-post-action" formaction="<?= $removefolderUrl ?>"
                                            class="delete" title="<?= Lang::txt('JACTION_DELETE') ?>"
                                            data-confirm="<?= Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE') ?>">
                                            <?php echo Lang::txt('JACTION_DELETE'); ?>
                                        </button>
                                        <?php
                                        $editfolderUrl = Route::url(
                                            'index.php?option=' . $this->option
                                                . '&controller=queries&task=editfolder&id=' . $folder->id
                                                . '&tmpl=component&' . Session::getFormToken() . '=1',
                                        );
                                        $dataHrefUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=queries&task=savefolder&'
                                                . Session::getFormToken() . '=1&fields[id]=' . $folder->id,
                                        );
                                        ?>
                                        <a class="edit editfolder" data-id="<?= $this->escape($folder->id) ?>"
                                            href="<?= $editfolderUrl ?>" data-href="<?= $dataHrefUrl ?>"
                                            data-name="<?= Lang::txt('COM_SUPPORT_FOLDER_NAME') ?>"
                                            title="<?= Lang::txt('JACTION_EDIT') ?>">
                                            <?php echo Lang::txt('JACTION_EDIT'); ?>
                                        </a>
                                    </span>
                                <?php } ?>
                                <ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
                                    <?php foreach ($folder->queries as $query) { ?>
                                        <?php
                                        $classAttr = intval($this->filters['show']) == $query->id
                                            ? ' class="active"'
                                            : '';
                                        ?>
                                        <li id="query_<?= $this->escape($query->id) ?>" <?= $classAttr ?>>
                                            <?php
                                            $displayUrl = Route::url(
                                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                    . '&task=display&show=' . $query->id
                                                    . (
                                                        intval($this->filters['show']) != $query->id
                                                            ? '&search=&limitstart=0'
                                                            : ''
                                                    ),
                                            );
                                            ?>
                                            <a class="aquery" href="<?= $displayUrl ?>">
                                                <?= $this->escape(stripslashes($query->title)) ?> <span><?= $query->get('count') ?></span>
                                            </a>
                                            <?php if ($this->acl->check('read', 'tickets')) { ?>
                                                <span class="query-options">
                                                    <?php
                                                    $removeUrl = Route::url(
                                                        'index.php?option=' . $this->option
                                                            . '&controller=queries&task=remove&id=' . $query->id,
                                                    );
                                                    $confirmText = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                                                    ?>
                                                    <button type="submit" form="hz-post-action"
                                                        formaction="<?= $removeUrl ?>" class="delete"
                                                        title="<?= Lang::txt('JACTION_DELETE') ?>"
                                                        data-confirm="<?= $confirmText ?>">
                                                        <?php echo Lang::txt('JACTION_DELETE'); ?>
                                                    </button>
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
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <?php if ($this->acl->check('read', 'tickets')) { ?>
                    <ul class="controls">
                        <li>
                            <?php
                            $addUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=queries&task=add&'
                                    . Session::getFormToken() . '=1',
                            );
                            ?>
                            <a class="icon-list modal" id="new-query" href="<?= $addUrl ?>"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                                title="<?= Lang::txt('COM_SUPPORT_ADD_QUERY') ?>">
                                <?php echo Lang::txt('COM_SUPPORT_ADD_QUERY'); ?>
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
                                data-href="<?= $savefolderUrl ?>"
                                data-name="<?= Lang::txt('COM_SUPPORT_FOLDER_NAME') ?>"
                                title="<?= Lang::txt('COM_SUPPORT_ADD_FOLDER') ?>">
                                <?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>
                            </a>
                        </li>
                    </ul>
                <?php } ?>

            </div><!-- / .pane-inner -->
        </div><!-- / .pane -->
        <div class="pane pane-list">
            <div class="pane-inner" id="tickets">
                <?php
                $displayUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display',
                );
                ?>
                <form action="<?= $displayUrl ?>" method="post" id="ticketForm">
                    <div class="list-options">
                        <?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
                        <ul class="sort-options">
                            <li>
                                <span class="sort-header"><?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?></span>
                                <ul>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=created&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-age<?= $this->filters['sort'] == 'created' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=status&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-status<?= $this->filters['sort'] == 'status' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=severity&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-severity<?= $this->filters['sort'] == 'severity' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=summary&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-summary<?= $this->filters['sort'] == 'summary' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=group&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-group<?= $this->filters['sort'] == 'group' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $displayUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=display&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&sort=owner&sortdir=' . $direction
                                                . '&limit=' . $this->filters['limit'] . '&limitstart=0',
                                        );
                                        ?>
                                        <a class="sort-owner<?= $this->filters['sort'] == 'owner' ? ' active ' . strtolower($this->filters['sortdir']) : '' ?>" href="<?= $displayUrl ?>" title="<?= Lang::txt('COM_SUPPORT_CLICK_TO_SORT') ?>">
                                            <?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <fieldset id="filter-bar">
                            <label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
                            <input type="text" name="search" id="filter_search"
                                value="<?= $this->escape($this->filters['search']) ?>"
                                placeholder="<?= Lang::txt('COM_SUPPORT_SEARCH_THIS_QUERY') ?>" />

                            <input type="hidden" name="sort" value="<?= $this->escape($this->filters['sort']) ?>" />
                            <input type="hidden" name="sortdir"
                                value="<?= $this->escape($this->filters['sortdir']) ?>" />
                            <input type="hidden" name="show" value="<?= $this->escape($this->filters['show']) ?>" />

                            <input type="submit" class="submit" value="<?php echo Lang::txt('COM_SUPPORT_GO'); ?>" />
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
                                        $this->filters['limit'],
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
                        $statuses = [];
                        foreach ($this->rows as $row) {
                            // Was there any activity on this item?
                            $lastcomment = $row
                                ->comments()
                                ->order('created', 'desc')
                                ->row()
                                ->get('created');

                            $tags = $row->tags('linkedlist');

                            if (!in_array($row->status->get('id'), $statuses)) {
                                $statuses[] = $row->status->get('id');
                                $this->css(
                                    '#tktlist tbody tr td.status-' . $row->status->get('id') . ' { border-left-color: #'
                                        . $row->status->get('color') . '; }',
                                );
                            }
                            ?>
                            <tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
                                <td class="status-<?php echo $row->status->get('id'); ?>">
                                    <span class="hasTip" title="<?= Lang::txt('COM_SUPPORT_DETAILS') ?> :: <?= Lang::txt('COM_SUPPORT_COL_STATUS') . ': ' . $row->status->get('text') ?>">
                                        <span class="ticket-id">
                                            <?php echo $row->get('id'); ?>
                                        </span>
                                        <span class="<?= ($row->isOpen() ? 'open' : 'closed') . ' ' . $row->status->get('class') ?> status">
                                            <?php
                                            echo $row->status->get('text');
                                            echo (!$row->isOpen())
                                                ? ' (' . $this->escape($row->get('resolved')) . ')'
                                                : '';
                                            ?>
                                        </span>
                                        <?php
                                        if (
                                            $row->get('target_date')
                                            && $row->get('target_date') != '0000-00-00 00:00:00'
                                        ) { ?>
                                            <?php
                                            $titleText = Lang::txt(
                                                'COM_SUPPORT_TARGET_DATE',
                                                Date::of($row->get('target_date'))->toLocal(
                                                    Lang::txt('DATE_FORMAT_HZ1'),
                                                ),
                                            );
                                            ?>
                                            <span class="ticket-target_date tooltips" title="<?= $titleText ?>">
                                                <?php
                                                $datetime = Date::of($row->get('target_date'))->format(
                                                    'Y-m-d\TH:i:s\Z',
                                                );
                                                $timeText = Date::of($row->get('target_date'))->toLocal(
                                                    Lang::txt('DATE_FORMAT_HZ1'),
                                                );
                                                ?>
                                                <time datetime="<?= $datetime ?>"><?= $timeText ?></time>
                                            </span>
                                        <?php } ?>
                                    </span>
                                </td>
                                <td colspan="6">
                                    <p>
                                        <span class="ticket-author">
                                            <?php
                                            echo $this->escape($row->get('name'));
                                            echo ($row->submitter->get('id'))
                                                ? ' (<a href="'
                                                    . Route::url(
                                                        'index.php?option=com_members&id=' . $row->submitter->get('id'),
                                                    ) . '">' . $this->escape($row->get('login')) . '</a>)'
                                                : (
                                                    $row->get('login')
                                                        ? ' (' . $this->escape($row->get('login')) . ')'
                                                        : ''
                                                );
                                            ?>
                                        </span>
                                        <span class="ticket-datetime">
                                            @ <time datetime="<?= Date::of($row->get('created'))->format('Y-m-d\TH:i:s\Z') ?>"><?= Date::of($row->get('created'))->toLocal() ?></time>
                                        </span>
                                        <?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
                                            <span class="ticket-activity">
                                                <?php
                                                $datetime = Date::of($lastcomment)->format('Y-m-d\TH:i:s\Z');
                                                $timeText = Date::of($lastcomment)->relative();
                                                ?>
                                                <time datetime="<?= $datetime ?>"><?= $timeText ?></time>
                                            </span>
                                        <?php } ?>
                                    </p>
                                    <p>
                                        <?php
                                        $linkUrl = Route::url(
                                            $row->link() . '&show=' . $this->filters['show'] . '&search='
                                                . $this->filters['search'] . '&limit=' . $this->filters['limit']
                                                . '&limitstart=' . $this->filters['start'],
                                        );
                                        $titleText = $this->escape(
                                            str_replace(['<br />', '&amp;'], ['', '&'], $row->content),
                                        );
                                        ?>
                                        <a class="ticket-content" title="<?= $titleText ?>" href="<?= $linkUrl ?>">
                                            <?= $row->content
                                                ? \Hubzero\Utility\Str::truncate(strip_tags($row->content), 200)
                                                : Lang::txt('COM_SUPPORT_NO_CONTENT_FOUND') ?>
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
                                                if ($group = \Hubzero\User\Group::getInstance($row->get('group_id'))) {
                                                    $gname = $group->get('cn');
                                                }
                                                echo $this->escape($gname);
                                                ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($row->isOwned()) { ?>
                                            <span class="ticket-owner hasTip" title="<?= Lang::txt('COM_SUPPORT_ASSIGNED_TO') ?>::<img border=&quot;1&quot; src=&quot;<?= $row->assignee->picture() ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?= $this->escape(stripslashes($row->assignee->get('username', ''))) ?><br /><?= $this->escape(stripslashes($row->assignee->get('organization', Lang::txt('COM_SUPPORT_UNKNOWN')))) ?>">
                                                <?= $this->escape(stripslashes($row->assignee->get('name', ''))) ?>
                                            </span>
                                        <?php } ?>
                                        </p>
                                    <?php } ?>
                                </td>
                                <td class="tkt-severity">
                                    <span class="ticket-severity <?= $this->escape($row->get('severity', 'normal')) ?> hasTip" title="<?= Lang::txt('COM_SUPPORT_PRIORITY') ?>:&nbsp;<?= $this->escape($row->get('severity', 'normal')) ?>">
                                        <span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
                                    </span>
                                    <?php if ($this->acl->check('delete', 'tickets')) { ?>
                                        <?php
                                        $actionUrl = Route::url($row->link('delete'));
                                        ?>
                                        <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>" class="delete" title="<?= Lang::txt('JACTION_DELETE') ?>" data-confirm="<?= Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE') ?>"><?= Lang::txt('JACTION_DELETE') ?></button>
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
