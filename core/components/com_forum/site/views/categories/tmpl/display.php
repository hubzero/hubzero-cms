<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{category: mixed, config: mixed, filters: mixed, threads: mixed} $this
 */

$this
     ->css()
     ->js();

/**
 * Filters directory
 *
 * @param   array   $filters
 * @param   string  $current
 * @param   string  $dir
 * @return  string
 */
$sortDir = function ($filters, $current, $dir = 'DESC') {
    if ($filters['sortby'] == $current && $filters['sort_Dir'] == $dir) {
        $dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
    }
    return strtolower($dir);
};

$this->category->set('section_alias', $this->filters['section']);
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-folder categories btn" href="<?php echo Route::url($this->category->link('base')); ?>">
                <?php echo Lang::txt('COM_FORUM_ALL_CATEGORIES'); ?>
            </a>
        </p>
    </div>
</header>

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <form action="<?= Route::url('index.php?option=' . $this->option . '&controller=categories&task=search') ?>"
                method="get">
                <div class="container data-entry">
                    <input class="entry-search-submit" type="submit" value="<?= Lang::txt('COM_FORUM_SEARCH') ?>" />
                    <fieldset class="entry-search">
                        <legend><span><?php echo Lang::txt('COM_FORUM_SEARCH_LEGEND'); ?></span></legend>

                        <label for="entry-search-field"><?php echo Lang::txt('COM_FORUM_SEARCH_LABEL'); ?></label>
                        <input type="text" name="q" id="entry-search-field"
                            value="<?= $this->escape($this->filters['search']) ?>"
                            placeholder="<?= Lang::txt('COM_FORUM_SEARCH_PLACEHOLDER') ?>" />
                    </fieldset>
                </div><!-- / .container -->

                <div class="container">
                    <nav class="entries-filters" aria-label="<?= Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') ?>">
                        <ul class="entries-menu order-options">
                            <li>
                                <?php
                                $linkUrl = Route::url(
                                    $this->category->link(
                                        'here',
                                        '&sortby=created&sortdir=' . $sortDir($this->filters, 'created'),
                                    ),
                                );
                                $classes = ($this->filters['sortby'] == 'created')
                                    ? 'active '
                                        . (
                                            strtolower($this->filters['sort_Dir']) == 'desc'
                                                ? 'icon-arrow-down'
                                                : 'icon-arrow-up'
                                        )
                                    : 'icon-arrow-down';
                                ?>
                                <a class="<?= $classes ?>" href="<?= $linkUrl ?>"
                                    title="<?= Lang::txt('COM_FORUM_SORT_BY_CREATED') ?>" rel="nofollow">
                                    <?php echo Lang::txt('COM_FORUM_SORT_CREATED'); ?>
                                </a>
                            </li>
                            <li>
                                <?php
                                $linkUrl = Route::url(
                                    $this->category->link(
                                        'here',
                                        '&sortby=activity&sortdir=' . $sortDir($this->filters, 'activity'),
                                    ),
                                );
                                $classes = ($this->filters['sortby'] == 'activity')
                                    ? 'active '
                                        . (
                                            strtolower($this->filters['sort_Dir']) == 'desc'
                                                ? 'icon-arrow-down'
                                                : 'icon-arrow-up'
                                        )
                                    : 'icon-arrow-down';
                                ?>
                                <a class="<?= $classes ?>" href="<?= $linkUrl ?>"
                                    title="<?= Lang::txt('COM_FORUM_SORT_BY_ACTIVITY') ?>" rel="nofollow">
                                    <?php echo Lang::txt('COM_FORUM_SORT_ACTIVITY'); ?>
                                </a>
                            </li>
                            <li>
                                <?php
                                $linkUrl = Route::url(
                                    $this->category->link(
                                        'here',
                                        '&sortby=replies&sortdir=' . $sortDir($this->filters, 'replies'),
                                    ),
                                );
                                $classes = ($this->filters['sortby'] == 'replies')
                                    ? 'active '
                                        . (
                                            strtolower($this->filters['sort_Dir']) == 'desc'
                                                ? 'icon-arrow-down'
                                                : 'icon-arrow-up'
                                        )
                                    : 'icon-arrow-down';
                                ?>
                                <a class="<?= $classes ?>" href="<?= $linkUrl ?>"
                                    title="<?= Lang::txt('COM_FORUM_SORT_BY_NUM_POSTS') ?>" rel="nofollow">
                                    <?php echo Lang::txt('COM_FORUM_SORT_NUM_POSTS'); ?>
                                </a>
                            </li>
                            <li>
                                <?php
                                $linkUrl = Route::url(
                                    $this->category->link(
                                        'here',
                                        '&sortby=title&sortdir=' . $sortDir($this->filters, 'title', 'ASC'),
                                    ),
                                );
                                $classes = ($this->filters['sortby'] == 'title')
                                    ? 'active '
                                        . (
                                            strtolower($this->filters['sort_Dir']) == 'desc'
                                                ? 'icon-arrow-down'
                                                : 'icon-arrow-up'
                                        )
                                    : 'icon-arrow-up';
                                ?>
                                <a class="<?= $classes ?>" href="<?= $linkUrl ?>"
                                    title="<?= Lang::txt('COM_FORUM_SORT_BY_TITLE') ?>" rel="nofollow">
                                    <?php echo Lang::txt('COM_FORUM_SORT_TITLE'); ?>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <table class="entries">
                        <caption>
                            <?php
                            if ($this->filters['search']) {
                                if ($this->category->get('title')) {
                                    echo Lang::txt(
                                        'COM_FORUM_SEARCH_FOR_IN',
                                        $this->escape($this->filters['search']),
                                        $this->escape(stripslashes($this->category->get('title'))),
                                    );
                                } else {
                                    echo Lang::txt('COM_FORUM_SEARCH_FOR', $this->escape($this->filters['search']));
                                }
                            } else {
                                echo Lang::txt(
                                    'COM_FORUM_SEARCH_IN',
                                    $this->escape(stripslashes($this->category->get('title'))),
                                );
                            }
                            ?>
                        </caption>
                        <tbody>
                            <?php
                            if ($this->threads->count() > 0) {
                                foreach ($this->threads as $row) {
                                    $name = Lang::txt('JANONYMOUS');
                                    if (!$row->get('anonymous')) {
                                        $name = $this->escape(stripslashes($row->creator->get('name', $name)));
                                        if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                            $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name
                                                . '</a>';
                                        }
                                    }
                                    $cls = [];
                                    $icn = 'icon-comments';
                                    if ($row->isClosed()) {
                                        $cls[] = 'closed';
                                        $icn = 'icon-lock';
                                    }
                                    if ($row->isSticky()) {
                                        $cls[] = 'sticky';
                                        $icn = 'icon-asterisk';
                                    }

                                    $row->set('category', $this->filters['category']);
                                    $row->set('section', $this->filters['section']);
                                    ?>
                                    <tr<?php echo count($cls) > 0 ? ' class="' . implode(' ', $cls) . '"' : ''; ?>>
                                        <th class="priority-5" scope="row">
                                            <?php
                                            $entryIdentifierText = $this->escape($row->get('id'));
                                            ?>
                                            <span
                                                class="entry-identifier <?= $icn ?>"><?= $entryIdentifierText ?></span>
                                        </th>
                                        <td>
                                            <a class="entry-title" href="<?php echo Route::url($row->link()); ?>">
                                                <span><?= $this->escape(stripslashes($row->get('title'))) ?></span>
                                            </a>
                                            <span class="entry-details">
                                                <span class="entry-date">
                                                    <?php
                                                    $timeText = $row->created('date');
                                                    ?>
                                                    <time datetime="<?= $row->created() ?>"><?= $timeText ?></time>
                                                </span>
                                                <?= Lang::txt(
                                                    'COM_FORUM_BY_USER',
                                                    '<span class="entry-author">' . $name . '</span>',
                                                ) ?>
                                            </span>
                                        </td>
                                        <td class="priority-4">
                                            <span><?php
                                            echo $row
                                                ->thread()
                                                ->whereEquals('state', $row->get('state'))
                                                ->whereIn('access', $this->filters['access'])
                                                ->total();
                                            ?></span>
                                            <span class="entry-details">
                                                <?php echo Lang::txt('COM_FORUM_COMMENTS'); ?>
                                            </span>
                                        </td>
                                        <td class="priority-3">
                                            <span><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></span>
                                            <span class="entry-details">
                                                <?php
                                                $lastpost = $row->lastActivity();
                                                if ($lastpost->get('id')) {
                                                    $lname = Lang::txt('JANONYMOUS');
                                                    if (!$lastpost->get('anonymous')) {
                                                        $lname = $this->escape(
                                                            stripslashes($lastpost->creator->get('name')),
                                                        );
                                                        if (
                                                            in_array(
                                                                $lastpost->creator->get('access'),
                                                                User::getAuthorisedViewLevels(),
                                                            )
                                                        ) {
                                                            $lname = '<a href="'
                                                                . Route::url($lastpost->creator->link()) . '">' . $lname
                                                                . '</a>';
                                                        }
                                                    }
                                                    ?>
                                                    <span class="entry-date">
                                                        <?php
                                                        $datetime = $lastpost->created();
                                                        $timeText = $lastpost->created('date');
                                                        ?>
                                                        <time datetime="<?= $datetime ?>"><?= $timeText ?></time>
                                                    </span>
                                                    <?= Lang::txt(
                                                        'COM_FORUM_BY_USER',
                                                        '<span class="entry-author">' . $lname . '</span>',
                                                    ) ?>
                                                <?php } else { ?>
                                                    <?php echo Lang::txt('COM_FORUM_NONE'); ?>
                                                <?php } ?>
                                            </span>
                                        </td>
                                        <?php
                                        if (
                                            $this->config->get('access-manage-thread')
                                            || $this->config->get('access-edit-thread')
                                            || $this->config->get('access-delete-thread')
                                        ) { ?>
                                            <td class="entry-options">
                                                <?php
                                                if (
                                                    $this->config->get('access-manage-thread')
                                                    || (
                                                        $this->config->get('access-edit-thread')
                                                        && $row->get('created_by') == User::get('id')
                                                    )
                                                ) { ?>
                                                    <a class="icon-edit edit"
                                                        href="<?= Route::url($row->link('edit')) ?>"
                                                        title="<?= Lang::txt('JACTION_EDIT') ?>">
                                                        <?php echo Lang::txt('JACTION_EDIT'); ?>
                                                    </a>
                                                <?php } ?>
                                                <?php
                                                if (
                                                    $this->config->get('access-manage-thread')
                                                    || (
                                                        $this->config->get('access-delete-thread')
                                                        && $row->get('created_by') == User::get('id')
                                                    )
                                                ) { ?>
                                                    <?php
                                                    $actionUrl = Route::url($row->link('delete'));
                                                    $confirm = Lang::txt('COM_FORUM_CONFIRM_DELETE');
                                                    $label = Lang::txt('JACTION_DELETE');
                                                    ?>
                                                    <button type="submit" form="hz-post-action"
                                                        formaction="<?= $actionUrl ?>" class="icon-delete delete"
                                                        title="<?= Lang::txt('JACTION_DELETE') ?>"
                                                        data-txt-confirm="<?= $confirm ?>"><?= $label ?></button>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td><?php echo Lang::txt('COM_FORUM_CATEGORY_EMPTY'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php
                    $pageNav = $this->threads->pagination;
                    $pageNav->setAdditionalUrlParam('section', $this->filters['section']);
                    $pageNav->setAdditionalUrlParam('category', $this->filters['category']);
                    $pageNav->setAdditionalUrlParam('q', $this->filters['search']);
                    echo $pageNav;
                    ?>
                    <div class="clearfix"></div>
                </div><!-- / .container -->
            </form>
        </div><!-- /.subject -->
        <aside class="aside">
            <div class="container">
                <h3><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></h3>
                <p>
                    <?php
                    $last = $this->category->lastActivity();
                    if ($last->get('id')) {
                        $lname = Lang::txt('JANONYMOUS');
                        if (!$last->get('anonymous')) {
                            $lname = $this->escape(stripslashes($last->creator->get('name', $lname)));
                            if (in_array($last->creator->get('access'), User::getAuthorisedViewLevels())) {
                                $lname = '<a href="' . Route::url($last->creator->link()) . '">' . $lname . '</a>';
                            }
                        }
                        $last->set('category', $this->filters['category']);
                        $last->set('section', $this->filters['section']);
                        ?>
                        <a class="entry-comment" href="<?php echo Route::url($last->link()); ?>">
                            <?php echo \Hubzero\Utility\Str::truncate(strip_tags($last->get('comment')), 170); ?>
                        </a>
                        <span class="entry-author">
                            <?php echo $lname; ?>
                        </span>
                        <span class="entry-date">
                            <span class="entry-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
                            <span class="icon-time time"><time
                                datetime="<?= $last->created() ?>"><?= $last->created('time') ?></time></span>
                            <span class="entry-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
                            <span class="icon-date date"><time
                                datetime="<?= $last->created() ?>"><?= $last->created('date') ?></time></span>
                        </span>
                    <?php } else { ?>
                        <?php echo Lang::txt('COM_FORUM_NONE'); ?>
                    <?php } ?>
                </p>
            </div><!-- / .container -->

            <?php if ($this->config->get('access-create-thread')) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN'); ?></h3>
                    <?php if (!$this->category->isClosed()) { ?>
                        <p>
                            <?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION'); ?>
                        </p>
                        <p>
                            <?php
                            $linkUrl = Route::url($this->category->link('newthread'));
                            ?>
                            <a class="icon-add add btn"
                                href="<?= $linkUrl ?>"><?= Lang::txt('COM_FORUM_NEW_DISCUSSION') ?></a>
                        </p>
                    <?php } else { ?>
                        <p class="warning">
                            <?php echo Lang::txt('COM_FORUM_CATEGORY_CLOSED'); ?>
                        </p>
                    <?php } ?>
                </div><!-- / .container -->
            <?php } ?>
        </aside><!-- / .aside -->
    </div>
</section><!-- /.main -->
