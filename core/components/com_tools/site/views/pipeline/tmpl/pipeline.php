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

// No direct access.
defined('_HZEXEC_') or die();
// get configurations/ defaults
$developer_site = $this->config->get('developer_site', 'hubFORGE');
$live_site = rtrim(Request::base(), '/');
$developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#', '', $live_site);
$project_path  = $this->config->get('project_path', '/tools/');
$dev_suffix    = $this->config->get('dev_suffix', '_dev');

$this
     ->css('pipeline.css')
     ->js('pipeline.js');

// Initiate paging
$pageNav = $this->pagination(
    $this->total,
    $this->filters['start'],
    $this->filters['limit'],
);
$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php
            $createUrl = Route::url('index.php?option=' . $this->option . '&task=create');
            ?>
            <li class="last"><a class="icon-add add btn"
                href="<?= $createUrl ?>"><?= Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') ?></a></li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
    <?php
    $pipelineUrl = Route::url(
        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline',
    );
    ?>
    <form action="<?= $pipelineUrl ?>" method="get">
        <div class="container data-entry">
            <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TOOLS_SEARCH'); ?>" />
            <fieldset class="entry-search">
                <label for="search">
                    <?php echo Lang::txt('COM_TOOLS_FIND_TOOL'); ?>
                </label>
                <input type="text" name="search" id="entry-search-text"
                    value="<?= $this->escape($this->filters['search']) ?>"
                    placeholder="<?= Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER') ?>" />

                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task" value="pipeline" />

                <input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
                <input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); ?>" />
            </fieldset>
        </div><!-- / .container data-entry -->

        <div class="container cf">
            <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
                <ul class="entries-menu order-options" data-label="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY') ?>">
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby='
                                . urlencode('f.state, f.priority, f.toolname') . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        $activeClass = $this->filters['sortby'] == 'f.state, f.priority, f.toolname' ? ' active' : '';
                        ?>
                        <a class="sort-status<?= $activeClass ?>" href="<?= $pipelineUrl ?>"
                            title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS') ?>" rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
                        </a>
                    </li>
                    <?php } else { ?>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby='
                                . urlencode('f.state, f.registered') . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        $activeClass = $this->filters['sortby'] == 'f.state, f.registered' ? ' active' : '';
                        ?>
                        <a class="sort-status<?= $activeClass ?>" href="<?= $pipelineUrl ?>"
                            title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS') ?>" rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.registered')
                                . '&search=' . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="sort-date<?= $this->filters['sortby'] == 'f.registered' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_REG') ?>"
                            rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_DATE'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.toolname')
                                . '&search=' . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="sort-name<?= $this->filters['sortby'] == 'f.toolname' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_NAME') ?>"
                            rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_ALIAS'); ?>
                        </a>
                    </li>
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.priority')
                                . '&search=' . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="sort-priority<?= $this->filters['sortby'] == 'f.priority' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_PRIORITY') ?>" rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_PRIORITY'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=' . $this->filters['filterby'] . '&sortby='
                                . urlencode('f.state_changed DESC') . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        $activeClass = $this->filters['sortby'] == 'f.state_changed DESC' ? ' active' : '';
                        ?>
                        <a class="sort-change <?= $activeClass ?>" href="<?= $pipelineUrl ?>"
                            title="<?= Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE') ?>" rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_STATUS_CHANGE'); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>

                <ul class="entries-menu filter-options" data-label="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER') ?>">
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=all&sortby=' . urlencode($this->filters['sortby']) . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="filter-all<?= $this->filters['filterby'] == 'all' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_ALL') ?>"
                            rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_ALL'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=mine&sortby=' . urlencode($this->filters['sortby']) . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="filter-mine<?= $this->filters['filterby'] == 'mine' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_MINE') ?>"
                            rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_MINE'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=published&sortby=' . urlencode($this->filters['sortby']) . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="filter-published<?= $this->filters['filterby'] == 'published' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>"
                            title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_PUBLISHED') ?>" rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_PUBLISHED'); ?>
                        </a>
                    </li>
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $pipelineUrl = Route::url(
                            'index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit']
                                . '&filterby=dev&sortby=' . urlencode($this->filters['sortby']) . '&search='
                                . $this->escape(urlencode($this->filters['search'])),
                        );
                        ?>
                        <a class="filter-dev<?= $this->filters['filterby'] == 'dev' ? ' active' : '' ?>"
                            href="<?= $pipelineUrl ?>" title="<?= Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_DEV') ?>"
                            rel="nofollow">
                            <?php echo Lang::txt('COM_TOOLS_DEVELOPMENT'); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>

            <table class="tools entries">
                <caption>
                    <?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_' . strtoupper($this->filters['filterby'])); ?>
                    <span>
                        (<?= (count($this->rows) > 0) ? $this->filters['start'] + 1 : 0 ?> - <?= $this->filters['start'] + count($this->rows) ?> of <?= $pageNav->total ?>)
                    </span>
                </caption>
                <thead>
                    <tr>
                        <th scope="col" class="priority-5"></th>
                        <th scope="col"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
                        <th scope="col" class="priority-4"><?php echo Lang::txt('COM_TOOLS_ALIAS'); ?></th>
                        <th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?></th>
                        <!-- <th scope="col"><?php echo Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'); ?></th> -->
                        <th scope="col"><?php echo Lang::txt('COM_TOOLS_LINKS'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $k = 0;

                for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
                    $row = &$this->rows[$i];

                    $row->state_changed = ($row->state_changed && $row->state_changed != '0000-00-00 00:00:00')
                        ? $row->state_changed
                        : $row->registered;
                    $row->title .= ($row->version) ? ' v' . $row->version : '';

                    \Components\Tools\Helpers\Html::getStatusName($row->state, $status);
                    ?>
                    <tr class="<?php echo strtolower($status);
                    if (!$this->admin) {
                        echo ' user-submitted';
                    } ?>">
                        <th class="priority-5">
                            <span class="entry-id">
                                <?php echo $this->escape($row->id); ?>
                            </span>
                        </th>
                        <td>
                            <?php
                            $statusUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=status&app=' . $row->toolname,
                            );
                            ?>
                            <a class="entry-title" href="<?= $statusUrl ?>">
                                <?php echo $this->escape(stripslashes($row->title)); ?>
                            </a><br />
                            <span class="entry-details">
                                <?php echo Lang::txt('COM_TOOLS_REGISTERED'); ?>
                                <?php
                                $entryDateText = Date::of($row->registered)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                                ?>
                                <span class="entry-date"><?= $entryDateText ?></span>
                            </span>
                        </td>
                        <td class="priority-4">
                            <?php
                            $statusUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=status&app=' . $row->toolname,
                            );
                            ?>
                            <a class="entry-alias" href="<?= $statusUrl ?>">
                                <?php echo $this->escape($row->toolname); ?>
                            </a>
                        </td>
                        <td class="priority-3">
                            <?php
                            $statusUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=status&app=' . $row->toolname,
                            );
                            ?>
                            <a class="entry-status" href="<?= $statusUrl ?>">
                                <?php echo $status; ?>
                            </a><br />
                            <span class="entry-details">
                                <?php
                                $entryTimeText = \Components\Tools\Helpers\Html::timeAgo($row->state_changed) . ' '
                                    . Lang::txt('COM_TOOLS_AGO');
                                ?>
                                <span class="entry-time"><?= $entryTimeText ?></span>
                            </span>
                        </td>
                        <?php
                        $classAttr = !\Components\Tools\Helpers\Html::toolEstablished($row->state)
                            ? 'class="disabled_links"'
                            : '';
                        ?>
                        <td <?= $classAttr ?>>
                            <?php if (!\Components\Tools\Helpers\Html::toolActive($row->state)) { ?>
                                <span class="entry-page">
                                    <?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
                                </span>
                            <?php } else { ?>
                                <?php
                                $linkUrl = Route::url('index.php?option=' . $this->option . '&app=' . $row->toolname);
                                ?>
                                <a class="entry-page" href="<?= $linkUrl ?>">
                                    <?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
                                </a>
                            <?php } ?>
                                |
                                <?php
                                $ticketUrl = Route::url(
                                    'index.php?option=com_support&task=ticket&id=' . $row->ticketid,
                                );
                                ?>
                                <a class="entry-history" href="<?= $ticketUrl ?>">
                                    <?php echo strtolower(Lang::txt('COM_TOOLS_HISTORY')); ?>
                                </a>
                                |
                            <?php if (strtolower($status) == 'abandoned') { ?>
                                <span class="entry-wiki">
                                    <?php echo strtolower(Lang::txt('COM_TOOLS_PROJECT')); ?>
                                </span>
                            <?php } else { ?>
                                <a class="entry-wiki" href="<?= $developer_url . $project_path . $row->toolname ?>/wiki"
                                    rel="external">
                                    <?php echo strtolower(Lang::txt('COM_TOOLS_PROJECT')); ?>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
                </tbody>
            </table>

            <?php
            echo $pageNav->render();
            ?>
        </div><!-- / .container -->
    </form>
</section><!-- /.main section -->