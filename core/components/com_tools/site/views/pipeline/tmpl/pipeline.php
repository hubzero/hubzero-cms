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

$this->css('pipeline.css')
     ->js('pipeline.js');

// Initiate paging
$pageNav = $this->pagination(
    $this->total,
    $this->filters['start'],
    $this->filters['limit']
);
$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

$srch = $this->escape(urlencode($this->filters['search']));
$lim  = $this->filters['limit'];
$filt = $this->filters['filterby'];
$sflt = urlencode($this->filters['sortby']);
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php $newUrl = Route::url('index.php?option=' . $this->option . '&task=create'); ?>
            <li class="last"><a class="icon-add add btn" href="<?php echo $newUrl; ?>">
                <?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
    <?php
    $pipelineUrl = Route::url(
        'index.php?option=' . $this->option
        . '&controller=' . $this->controller
        . '&task=pipeline'
    );
    $filterLabel = Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS');
    $sortLabel   = Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY');
    $filterMenuLabel = Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER');
    $searchVal   = $this->escape($this->filters['search']);
    $searchPlaceholder = Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER');
    $sortbyVal   = $this->escape($this->filters['sortby']);
    $filterbyVal = $this->escape($this->filters['filterby']);
    ?>
    <form action="<?php echo $pipelineUrl; ?>" method="get">
        <div class="container data-entry">
            <input class="entry-search-submit" type="submit"
                value="<?php echo Lang::txt('COM_TOOLS_SEARCH'); ?>" />
            <fieldset class="entry-search">
                <label for="search">
                    <?php echo Lang::txt('COM_TOOLS_FIND_TOOL'); ?>
                </label>
                <input type="text" name="search" id="entry-search-text"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPlaceholder; ?>" />

                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task" value="pipeline" />

                <input type="hidden" name="sortby" value="<?php echo $sortbyVal; ?>" />
                <input type="hidden" name="filterby" value="<?php echo $filterbyVal; ?>" />
            </fieldset>
        </div><!-- / .container data-entry -->

        <div class="container cf">
            <nav class="entries-filters" aria-label="<?php echo $filterLabel; ?>">
                <ul class="entries-menu order-options" data-label="<?php echo $sortLabel; ?>">
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $sby1 = 'f.state, f.priority, f.toolname';
                        $cls1 = 'sort-status' . ($this->filters['sortby'] == $sby1 ? ' active' : '');
                        $url1 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby1)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls1; ?>" href="<?php echo $url1; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
                        </a>
                    </li>
                    <?php } else { ?>
                    <li>
                        <?php
                        $sby2 = 'f.state, f.registered';
                        $cls2 = 'sort-status' . ($this->filters['sortby'] == $sby2 ? ' active' : '');
                        $url2 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby2)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls2; ?>" href="<?php echo $url2; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li>
                        <?php
                        $sby3 = 'f.registered';
                        $cls3 = 'sort-date' . ($this->filters['sortby'] == $sby3 ? ' active' : '');
                        $url3 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby3)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls3; ?>" href="<?php echo $url3; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_REG'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_DATE'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $sby4 = 'f.toolname';
                        $cls4 = 'sort-name' . ($this->filters['sortby'] == $sby4 ? ' active' : '');
                        $url4 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby4)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls4; ?>" href="<?php echo $url4; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_NAME'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_ALIAS'); ?>
                        </a>
                    </li>
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $sby5 = 'f.priority';
                        $cls5 = 'sort-priority' . ($this->filters['sortby'] == $sby5 ? ' active' : '');
                        $url5 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby5)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls5; ?>" href="<?php echo $url5; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_PRIORITY'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_PRIORITY'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $sby6 = 'f.state_changed DESC';
                        $cls6 = 'sort-change ' . ($this->filters['sortby'] == $sby6 ? ' active' : '');
                        $url6 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=' . $filt
                            . '&sortby=' . urlencode($sby6)
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls6; ?>" href="<?php echo $url6; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_STATUS_CHANGE'); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>

                <ul class="entries-menu filter-options"
                    data-label="<?php echo $filterMenuLabel; ?>">
                    <li>
                        <?php
                        $cls7 = 'filter-all' . ($this->filters['filterby'] == 'all' ? ' active' : '');
                        $url7 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=all&sortby=' . $sflt
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls7; ?>" href="<?php echo $url7; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_ALL'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_ALL'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $cls8 = 'filter-mine' . ($this->filters['filterby'] == 'mine' ? ' active' : '');
                        $url8 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=mine&sortby=' . $sflt
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls8; ?>" href="<?php echo $url8; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_MINE'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_MINE'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $cls9 = 'filter-published'
                            . ($this->filters['filterby'] == 'published' ? ' active' : '');
                        $url9 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=published&sortby=' . $sflt
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls9; ?>" href="<?php echo $url9; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_PUBLISHED'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_PUBLISHED'); ?>
                        </a>
                    </li>
                    <?php if ($this->admin) { ?>
                    <li>
                        <?php
                        $cls10 = 'filter-dev' . ($this->filters['filterby'] == 'dev' ? ' active' : '');
                        $url10 = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=pipeline&limit=' . $lim
                            . '&filterby=dev&sortby=' . $sflt
                            . '&search=' . $srch
                        );
                        ?>
                        <a class="<?php echo $cls10; ?>" href="<?php echo $url10; ?>"
                            title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_DEV'); ?>">
                            <?php echo Lang::txt('COM_TOOLS_DEVELOPMENT'); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>

            <table class="tools entries">
                <caption>
                    <?php
                    $captionKey = 'COM_TOOLS_CONTRIBTOOL_FILTER_'
                        . strtoupper($this->filters['filterby']);
                    echo Lang::txt($captionKey); ?>
                    <span>
                        (<?php echo (count($this->rows) > 0)
                            ? $this->filters['start'] + 1 : 0; ?> -
                        <?php echo $this->filters['start'] + count($this->rows); ?> of
                        <?php echo $pageNav->total; ?>)
                    </span>
                </caption>
                <thead>
                    <tr>
                        <th scope="col" class="priority-5"></th>
                        <th scope="col"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
                        <th scope="col" class="priority-4"><?php echo Lang::txt('COM_TOOLS_ALIAS'); ?></th>
                        <th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?></th>
                        <!-- <th scope="col">
                            <?php echo Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'); ?>
                        </th> -->
                        <th scope="col"><?php echo Lang::txt('COM_TOOLS_LINKS'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $k = 0;

                for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
                    $row = &$this->rows[$i];

                    $row->state_changed = ($row->state_changed
                        && $row->state_changed != '0000-00-00 00:00:00')
                        ? $row->state_changed : $row->registered;
                    $row->title .= ($row->version) ? ' v' . $row->version : '';

                    \Components\Tools\Helpers\Html::getStatusName($row->state, $status);
                    $rowStatusUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=status&app=' . $row->toolname
                    );
                    $timeAgo = \Components\Tools\Helpers\Html::timeAgo($row->state_changed)
                        . ' ' . Lang::txt('COM_TOOLS_AGO');
                    $rowEstablished = \Components\Tools\Helpers\Html::toolEstablished($row->state);
                    $rowActive = \Components\Tools\Helpers\Html::toolActive($row->state);
                    $resourceUrl = Route::url(
                        'index.php?option=' . $this->option . '&app=' . $row->toolname
                    );
                    $ticketUrl = Route::url(
                        'index.php?option=com_support&task=ticket&id=' . $row->ticketid
                    );
                    $wikiUrl = $developer_url . $project_path . $row->toolname . '/wiki';
                    $dateStr = Date::of($row->registered)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
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
                            <a class="entry-title" href="<?php echo $rowStatusUrl; ?>">
                                <?php echo $this->escape(stripslashes($row->title)); ?>
                            </a><br />
                            <span class="entry-details">
                                <?php echo Lang::txt('COM_TOOLS_REGISTERED'); ?>
                                <span class="entry-date"><?php echo $dateStr; ?></span>
                            </span>
                        </td>
                        <td class="priority-4">
                            <a class="entry-alias" href="<?php echo $rowStatusUrl; ?>">
                                <?php echo $this->escape($row->toolname); ?>
                            </a>
                        </td>
                        <td class="priority-3">
                            <a class="entry-status" href="<?php echo $rowStatusUrl; ?>">
                                <?php echo $status; ?>
                            </a><br />
                            <span class="entry-details">
                                <span class="entry-time"><?php echo $timeAgo; ?></span>
                            </span>
                        </td>
                        <td <?php if (!$rowEstablished) {
                            echo 'class="disabled_links"';
                            } ?>>
                            <?php if (!$rowActive) { ?>
                                <span class="entry-page">
                                    <?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
                                </span>
                            <?php } else { ?>
                                <a class="entry-page" href="<?php echo $resourceUrl; ?>">
                                    <?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
                                </a>
                            <?php } ?>
                                |
                                <a class="entry-history" href="<?php echo $ticketUrl; ?>">
                                    <?php echo strtolower(Lang::txt('COM_TOOLS_HISTORY')); ?>
                                </a>
                                |
                            <?php if (strtolower($status) == 'abandoned') { ?>
                                <span class="entry-wiki">
                                    <?php echo strtolower(Lang::txt('COM_TOOLS_PROJECT')); ?>
                                </span>
                            <?php } else { ?>
                                <a class="entry-wiki" href="<?php echo $wikiUrl; ?>" rel="external">
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
