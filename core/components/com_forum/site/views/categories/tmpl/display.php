<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css()
     ->js();

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
            <?php
            $formAction = Route::url(
                'index.php?option=' . $this->option . '&controller=categories&task=search'
            );
            ?>
            <form action="<?php echo $formAction; ?>" method="get">
                <div class="container data-entry">
                    <input class="entry-search-submit"
                        type="submit"
                        value="<?php echo Lang::txt('COM_FORUM_SEARCH'); ?>" />
                    <fieldset class="entry-search">
                        <legend><span><?php echo Lang::txt('COM_FORUM_SEARCH_LEGEND'); ?></span></legend>

                        <label for="entry-search-field">
                            <?php echo Lang::txt('COM_FORUM_SEARCH_LABEL'); ?>
                        </label>
                        <input type="text"
                            name="q"
                            id="entry-search-field"
                            value="<?php echo $this->escape($this->filters['search']); ?>"
                            placeholder="<?php echo Lang::txt('COM_FORUM_SEARCH_PLACEHOLDER'); ?>" />
                    </fieldset>
                </div><!-- / .container -->

                <div class="container">
                    <nav class="entries-filters"
                        aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
                        <ul class="entries-menu order-options">
                            <?php
                            $createdClass = ($this->filters['sortby'] == 'created')
                                ? 'active ' . (strtolower($this->filters['sort_Dir']) == 'desc'
                                    ? 'icon-arrow-down'
                                    : 'icon-arrow-up')
                                : 'icon-arrow-down';
                            $createdUrl = Route::url($this->category->link(
                                'here',
                                '&sortby=created&sortdir=' . $sortDir($this->filters, 'created')
                            ));
                            ?>
                            <li>
                                <a class="<?php echo $createdClass; ?>"
                                    href="<?php echo $createdUrl; ?>"
                                    title="<?php echo Lang::txt('COM_FORUM_SORT_BY_CREATED'); ?>">
                                    <?php echo Lang::txt('COM_FORUM_SORT_CREATED'); ?>
                                </a>
                            </li>
                            <?php
                            $activityClass = ($this->filters['sortby'] == 'activity')
                                ? 'active ' . (strtolower($this->filters['sort_Dir']) == 'desc'
                                    ? 'icon-arrow-down'
                                    : 'icon-arrow-up')
                                : 'icon-arrow-down';
                            $activityUrl = Route::url($this->category->link(
                                'here',
                                '&sortby=activity&sortdir=' . $sortDir($this->filters, 'activity')
                            ));
                            ?>
                            <li>
                                <a class="<?php echo $activityClass; ?>"
                                    href="<?php echo $activityUrl; ?>"
                                    title="<?php echo Lang::txt('COM_FORUM_SORT_BY_ACTIVITY'); ?>">
                                    <?php echo Lang::txt('COM_FORUM_SORT_ACTIVITY'); ?>
                                </a>
                            </li>
                            <?php
                            $repliesClass = ($this->filters['sortby'] == 'replies')
                                ? 'active ' . (strtolower($this->filters['sort_Dir']) == 'desc'
                                    ? 'icon-arrow-down'
                                    : 'icon-arrow-up')
                                : 'icon-arrow-down';
                            $repliesUrl = Route::url($this->category->link(
                                'here',
                                '&sortby=replies&sortdir=' . $sortDir($this->filters, 'replies')
                            ));
                            ?>
                            <li>
                                <a class="<?php echo $repliesClass; ?>"
                                    href="<?php echo $repliesUrl; ?>"
                                    title="<?php echo Lang::txt('COM_FORUM_SORT_BY_NUM_POSTS'); ?>">
                                    <?php echo Lang::txt('COM_FORUM_SORT_NUM_POSTS'); ?>
                                </a>
                            </li>
                            <?php
                            $titleClass = ($this->filters['sortby'] == 'title')
                                ? 'active ' . (strtolower($this->filters['sort_Dir']) == 'desc'
                                    ? 'icon-arrow-down'
                                    : 'icon-arrow-up')
                                : 'icon-arrow-up';
                            $titleUrl = Route::url($this->category->link(
                                'here',
                                '&sortby=title&sortdir=' . $sortDir($this->filters, 'title', 'ASC')
                            ));
                            ?>
                            <li>
                                <a class="<?php echo $titleClass; ?>"
                                    href="<?php echo $titleUrl; ?>"
                                    title="<?php echo Lang::txt('COM_FORUM_SORT_BY_TITLE'); ?>">
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
                                        $this->escape(stripslashes($this->category->get('title')))
                                    );
                                } else {
                                    echo Lang::txt(
                                        'COM_FORUM_SEARCH_FOR',
                                        $this->escape($this->filters['search'])
                                    );
                                }
                            } else {
                                echo Lang::txt(
                                    'COM_FORUM_SEARCH_IN',
                                    $this->escape(stripslashes($this->category->get('title')))
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
                                        $creatorName = $row->creator->get('name', $name);
                                        $name = $this->escape(stripslashes($creatorName));
                                        $authorizedLevels = User::getAuthorisedViewLevels();
                                        if (in_array($row->creator->get('access'), $authorizedLevels)) {
                                            $creatorUrl = Route::url($row->creator->link());
                                            $name = '<a href="' . $creatorUrl . '">' . $name . '</a>';
                                        }
                                    }
                                    $cls = array();
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
                                    <tr<?php if (count($cls) > 0) {
                                        echo ' class="' . implode(' ', $cls) . '"';
                                       } ?>>
                                        <th class="priority-5" scope="row">
                                            <span class="entry-identifier <?php echo $icn; ?>">
                                                <?php echo $this->escape($row->get('id')); ?>
                                            </span>
                                        </th>
                                        <td>
                                            <?php $rowUrl = Route::url($row->link()); ?>
                                            <a class="entry-title" href="<?php echo $rowUrl; ?>">
                                                <span>
                                                    <?php echo $this->escape(stripslashes($row->get('title'))); ?>
                                                </span>
                                            </a>
                                            <span class="entry-details">
                                                <span class="entry-date">
                                                    <time datetime="<?php echo $row->created(); ?>">
                                                        <?php echo $row->created('date'); ?>
                                                    </time>
                                                </span>
                                                <?php
                                                $entryAuthor = '<span class="entry-author">' . $name . '</span>';
                                                echo Lang::txt('COM_FORUM_BY_USER', $entryAuthor);
                                                ?>
                                            </span>
                                        </td>
                                        <td class="priority-4">
                                            <span><?php
                                            echo $row->thread()
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
                                                        $lastCreatorName = $lastpost->creator->get('name');
                                                        $lname = $this->escape(stripslashes($lastCreatorName));
                                                        $lastAccess = $lastpost->creator->get('access');
                                                        $authLevels = User::getAuthorisedViewLevels();
                                                        if (in_array($lastAccess, $authLevels)) {
                                                            $lastCreatorUrl = Route::url($lastpost->creator->link());
                                                            $lname = '<a href="' . $lastCreatorUrl . '">'
                                                                . $lname . '</a>';
                                                        }
                                                    }
                                                    ?>
                                                    <span class="entry-date">
                                                        <time datetime="<?php echo $lastpost->created(); ?>">
                                                            <?php echo $lastpost->created('date'); ?>
                                                        </time>
                                                    </span>
                                                    <?php
                                                    $authorSpan = '<span class="entry-author">' . $lname . '</span>';
                                                    echo Lang::txt('COM_FORUM_BY_USER', $authorSpan);
                                                    ?>
                                                <?php } else { ?>
                                                    <?php echo Lang::txt('COM_FORUM_NONE'); ?>
                                                <?php } ?>
                                            </span>
                                        </td>
                                        <?php
                                        $canManage = $this->config->get('access-manage-thread');
                                        $canEdit = $this->config->get('access-edit-thread');
                                        $canDelete = $this->config->get('access-delete-thread');
                                        $isCreator = $row->get('created_by') == User::get('id');
                                        if ($canManage || $canEdit || $canDelete) {
                                            ?>
                                            <td class="entry-options">
                                                <?php if ($canManage || ($canEdit && $isCreator)) { ?>
                                                    <a class="icon-edit edit"
                                                        href="<?php echo Route::url($row->link('edit')); ?>"
                                                        title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
                                                        <?php echo Lang::txt('JACTION_EDIT'); ?>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($canManage || ($canDelete && $isCreator)) { ?>
                                                    <?php $confirmTxt = Lang::txt('COM_FORUM_CONFIRM_DELETE'); ?>
                                                    <a class="icon-delete delete"
                                                        data-txt-confirm="<?php echo $confirmTxt; ?>"
                                                        href="<?php echo Route::url($row->link('delete')); ?>"
                                                        title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
                                                        <?php echo Lang::txt('JACTION_DELETE'); ?>
                                                    </a>
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
                            $lastCreatorName = $last->creator->get('name', $lname);
                            $lname = $this->escape(stripslashes($lastCreatorName));
                            $lastCreatorAccess = $last->creator->get('access');
                            $viewLevels = User::getAuthorisedViewLevels();
                            if (in_array($lastCreatorAccess, $viewLevels)) {
                                $lastCreatorLink = Route::url($last->creator->link());
                                $lname = '<a href="' . $lastCreatorLink . '">' . $lname . '</a>';
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
                            <span class="entry-date-at">
                                <?php echo Lang::txt('COM_FORUM_AT'); ?>
                            </span>
                            <span class="icon-time time">
                                <time datetime="<?php echo $last->created(); ?>">
                                    <?php echo $last->created('time'); ?>
                                </time>
                            </span>
                            <span class="entry-date-on">
                                <?php echo Lang::txt('COM_FORUM_ON'); ?>
                            </span>
                            <span class="icon-date date">
                                <time datetime="<?php echo $last->created(); ?>">
                                    <?php echo $last->created('date'); ?>
                                </time>
                            </span>
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
                            <?php $newThreadUrl = Route::url($this->category->link('newthread')); ?>
                            <a class="icon-add add btn" href="<?php echo $newThreadUrl; ?>">
                                <?php echo Lang::txt('COM_FORUM_NEW_DISCUSSION'); ?>
                            </a>
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
