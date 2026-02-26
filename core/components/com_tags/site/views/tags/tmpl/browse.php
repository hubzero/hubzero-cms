<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-tag btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
                <?php echo Lang::txt('COM_TAGS_MORE_TAGS'); ?>
            </a>
        </p>
    </div><!-- / #content-header-extra -->
</header>

<section class="main section">
    <?php $browseUrl = Route::url('index.php?option=' . $this->option . '&task=browse'); ?>
    <form class="section-inner hz-layout-with-aside" action="<?php echo $browseUrl; ?>" method="get">
        <div class="subject">

            <div class="container data-entry">
                <?php $searchVal = $this->escape($this->filters['search']); ?>
                <?php $placeholder = Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER'); ?>
                <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TAGS_SEARCH'); ?>" />
                <fieldset class="entry-search">
                    <label for="entry-search-text"><?php echo Lang::txt('COM_TAGS_SEARCH_TAGS'); ?></label>
                    <input
                        type="text"
                        name="search"
                        id="entry-search-text"
                        value="<?php echo $searchVal; ?>"
                        placeholder="<?php echo $placeholder; ?>"
                    />
                </fieldset>
            </div><!-- / .container -->

            <div class="container">
                <?php $filterLabel = Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>
                <nav class="entries-filters" aria-label="<?php echo $filterLabel; ?>">
                    <ul class="entries-menu sort-options">
                        <li>
                            <?php
                                $filters = '&search='
                                    . urlencode($this->filters['search'])
                                    . '&limit=' . Request::getInt('limit', 25)
                                    . '&limitstart=' . Request::getInt('limitstart', 0);

                                $cls = ($this->filters['sort'] == 'total') ? 'active ' : '';
                                $sortDir = $cls
                                    ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc')
                                    : 'asc';
                                $url = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&task=browse&sort=total&sortdir='
                                    . $sortDir . $filters
                                );
                                $icon = $cls
                                    ? ($this->filters['sort_Dir'] == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                                    : 'icon-arrow-down';
                                $popTitle = Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY_TITLE');
                                ?>
                            <a class="<?php echo $cls . $icon; ?>"
                                href="<?php echo $url; ?>"
                                title="<?php echo $popTitle; ?>">
                                <?php echo Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY'); ?>
                            </a>
                        </li>
                        <li>
                            <?php
                                $sort = $this->filters['sort'];
                                $cls = ($sort == '' || $sort == 'raw_tag')
                                    ? 'active ' : '';
                                $sortDir = $cls
                                    ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc')
                                    : 'asc';
                                $url = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&task=browse&sort=raw_tag&sortdir='
                                    . $sortDir . $filters
                                );
                                $icon = $cls
                                    ? ($this->filters['sort_Dir'] == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down')
                                    : 'icon-arrow-down';
                                $alphaTitle = Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA_TITLE');
                                ?>
                            <a class="<?php echo $cls . $icon; ?>"
                                href="<?php echo $url; ?>"
                                title="<?php echo $alphaTitle; ?>">
                                <?php echo Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA'); ?>
                            </a>
                        </li>
                    </ul>
                </nav>

                <table class="entries" id="taglist">
                    <!-- <caption>
                        <?php
                        if (!$this->filters['limit']) {
                            $this->filters['limit'] = $this->total;
                        }
                        $s = ($this->total > 0) ? $this->filters['start'] + 1 : $this->filters['start'];
                        $max = $this->filters['start'] + $this->filters['limit'];
                        $e = ($this->total > $max) ? $max : $this->total;

                        if ($this->filters['search'] != '') {
                            echo Lang::txt(
                                'COM_TAGS_BROWSE_SEARCH_FOR_IN',
                                $this->escape($this->filters['search']),
                                Lang::txt('COM_TAGS')
                            );
                        } else {
                            echo Lang::txt('COM_TAGS');
                        }
                        ?>
                        <span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
                    </caption> -->
                    <thead>
                        <tr>
                            <th scope="col">
                                <?php echo Lang::txt('COM_TAGS_TAG'); ?>
                            </th>
                            <th class="priority-3" scope="col">
                                <?php echo Lang::txt('COM_TAGS_COL_ALIAS'); ?>
                            </th>
                            <?php
                            $canEdit = $this->config->get('access-edit-tag');
                            $canDelete = $this->config->get('access-delete-tag');
                            ?>
                            <?php if ($canEdit || $canDelete) { ?>
                                <th scope="col" colspan="2">
                                    <?php echo Lang::txt('COM_TAGS_COL_ACTION'); ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($this->rows->count()) {
                        $cls = 'even';
                        foreach ($this->rows as $row) {
                            $cls = ($cls == 'even' ? 'odd' : 'even');
                            ?>
                            <tr class="<?php echo $cls; ?>">
                                <td>
                                    <?php
                                    $tagUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&tag=' . $row->get('tag')
                                    );
                                    $adminClass = $row->get('admin') ? ' admin' : '';
                                    ?>
                                    <a class="tag <?php echo $adminClass; ?>"
                                        href="<?php echo $tagUrl; ?>">
                                        <?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
                                    </a>
                                </td>
                                <td class="priority-3">
                                    <?php
                                    $subs = $row->get('substitutes')
                                        ? $this->escape($row->substitutes) : '';
                                    $none = '<span>' . Lang::txt('COM_TAGS_NONE') . '</span>';

                                    echo $subs
                                        ? \Hubzero\Utility\Str::truncate($subs, 75)
                                        : $none;
                                    ?>
                                </td>
                                <?php if ($canEdit || $canDelete) { ?>
                                    <td>
                                        <?php if ($canDelete) {
                                            $deleteUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&task=delete&id[]=' . $row->get('id')
                                                . '&search=' . urlencode($this->filters['search'])
                                                . '&sort=' . $this->filters['sort']
                                                . '&sortdir=' . $this->filters['sort_Dir']
                                                . '&limit=' . $this->filters['limit']
                                                . '&limitstart=' . $this->filters['start']
                                            );
                                            $confirmTxt = Lang::txt('COM_TAGS_CONFIRM_DELETE');
                                            ?>
                                            <a class="icon-delete delete delete-tag"
                                                data-confirm="<?php echo $confirmTxt; ?>"
                                                href="<?php echo $deleteUrl; ?>">
                                                <?php echo Lang::txt('JACTION_DELETE'); ?>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($canEdit) {
                                            $editUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&task=edit&id=' . $row->get('id')
                                                . '&search=' . urlencode($this->filters['search'])
                                                . '&sort=' . $this->filters['sort']
                                                . '&sortdir=' . $this->filters['sort_Dir']
                                                . '&limit=' . $this->filters['limit']
                                                . '&limitstart=' . $this->filters['start']
                                            );
                                            $rawTag = $this->escape(
                                                stripslashes($row->get('raw_tag'))
                                            );
                                            $editTitle = Lang::txt(
                                                'COM_TAGS_EDIT_TAG',
                                                $rawTag
                                            );
                                            ?>
                                            <a class="icon-edit edit"
                                                href="<?php echo $editUrl; ?>"
                                                title="<?php echo $editTitle; ?>">
                                                <?php echo Lang::txt('JACTION_EDIT'); ?>
                                            </a>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr class="odd">
                            <?php $colspan = ($canEdit || $canDelete) ? 4 : 2; ?>
                            <td colspan="<?php echo $colspan; ?>">
                                <?php echo Lang::txt('COM_TAGS_NO_RESULTS'); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
                    // Initiate paging
                    $pageNav = $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit']
                    );
                    $pageNav->setAdditionalUrlParam('search', $this->filters['search']);
                    $pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
                    $pageNav->setAdditionalUrlParam('sortdir', $this->filters['sort_Dir']);
                    echo $pageNav->render();
                    ?>
                <div class="clearfix"></div>
                <input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
            </div><!-- / .container -->
        </div><!-- / .main subject -->
        <aside class="aside">
            <div class="container">
                <p>
                    <?php echo Lang::txt('COM_TAGS_BROWSE_EXPLANATION'); ?>
                </p>
                <p class="help">
                    <strong><?php echo Lang::txt('COM_TAGS_WHATS_AN_ALIAS'); ?></strong>
                    <br /><?php echo Lang::txt('COM_TAGS_ALIAS_EXPLANATION'); ?>
                </p>
            </div><!-- / .container -->
        </aside><!-- / .aside -->
    </form>
</section><!-- / .main section -->
