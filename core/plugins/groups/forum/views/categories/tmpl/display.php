<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols


/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$base = 'index.php?option='
    . $this->option
    . '&cn='
    . $this->group->get('cn')
    . '&active=forum&scope='
    . $this->filters['section']
    . '/'
    . $this->filters['category'];

if (!function_exists('sortDir')) {
    /**
     * Returns opposite of how it was sorted
     *
     * @param  array  $filters  Array of filters
     * @param  string $current
     * @param  string $dir      Direction of order
     * @return string
     */
    function sortDir($filters, $current, $dir = 'DESC')
    {
        if ($filters['sortby'] == $current && $filters['sort_Dir'] == $dir) {
            $dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
        }
        return strtolower($dir);
    }
}

// prepare the URL parameters for the delete URLS to retain the sorting
$sortingValues = (object) ['sortdir' => $this->filters['sort_Dir'], 'sortby' => $this->filters['sortby'], 'start' =>
$this->filters['start'], 'limit' => $this->filters['limit']];
$sortingQueryString = '';
if (!empty($sortingValues->sortby) || !empty($sortingValues->start || !empty($sortingValues->limit))) {
    $sortingQueryString = '?' . http_build_query($sortingValues);
}

$this->category->set('section_alias', $this->filters['section']);

$this->css()
     ->js();
?>

<ul id="page_options">
    <li>
        <?php
        $allCategoriesUrl = Route::url(
            'index.php?option=' . $this->option
            . '&cn=' . $this->group->get('cn')
            . '&active=forum'
        );
        ?>
        <a class="icon-folder categories btn"
            href="<?php echo $allCategoriesUrl; ?>">
            <?php echo Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES'); ?>
        </a>
    </li>
</ul>

<section class="main section">
    <?php
    $searchFormUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->get('cn')
        . '&active=forum&scope=search'
    );
    ?>
    <form action="<?php echo $searchFormUrl; ?>" method="get">
        <div class="container data-entry">
            <input class="entry-search-submit"
                type="submit"
                value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH'); ?>"/>
            <fieldset class="entry-search">
                <legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
                <label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
                <input type="text"
                    name="q"
                    id="entry-search-field"
                    value="<?php echo $this->escape($this->filters['search']); ?>"
                    placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>"/>
            </fieldset>
        </div><!-- / .container -->
    </form>
    <form action="<?php echo Route::url($this->category->link()); ?>" method="get">

        <?php if ($this->category->get('closed')) { ?>
            <p class="warning">
                <?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY_CLOSED'); ?>
            </p>
        <?php } ?>

        <div class="container">
            <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
                <ul class="entries-menu order-options">
                    <li>
                        <?php
                        $createdClass = $this->filters['sortby'] == 'created'
                            ? 'active ' . strtolower($this->filters['sort_Dir'])
                            : sortDir($this->filters, 'created');
                        $createdUrl = Route::url(
                            $base . '&sortby=created&sortdir='
                            . sortDir($this->filters, 'created')
                        );
                        $createdTitle = Lang::txt('PLG_GROUPS_FORUM_SORT_BY_CREATED');
                        ?>
                        <a class="<?php echo $createdClass; ?>"
                            href="<?php echo $createdUrl; ?>"
                            title="<?php echo $createdTitle; ?>">
                            <?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_CREATED'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $activityClass = $this->filters['sortby'] == 'activity'
                            ? 'active ' . strtolower($this->filters['sort_Dir'])
                            : sortDir($this->filters, 'activity');
                        $activityUrl = Route::url(
                            $base . '&sortby=activity&sortdir='
                            . sortDir($this->filters, 'activity')
                        );
                        $activityTitle = Lang::txt('PLG_GROUPS_FORUM_SORT_BY_ACTIVITY');
                        ?>
                        <a class="<?php echo $activityClass; ?>"
                            href="<?php echo $activityUrl; ?>"
                            title="<?php echo $activityTitle; ?>">
                            <?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_ACTIVITY'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $repliesClass = $this->filters['sortby'] == 'replies'
                            ? 'active ' . strtolower($this->filters['sort_Dir'])
                            : sortDir($this->filters, 'replies');
                        $repliesUrl = Route::url(
                            $base . '&sortby=replies&sortdir='
                            . sortDir($this->filters, 'replies')
                        );
                        $repliesTitle = Lang::txt('PLG_GROUPS_FORUM_SORT_BY_NUM_POSTS');
                        ?>
                        <a class="<?php echo $repliesClass; ?>"
                            href="<?php echo $repliesUrl; ?>"
                            title="<?php echo $repliesTitle; ?>">
                            <?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_NUM_POSTS'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $titleClass = $this->filters['sortby'] == 'title'
                            ? 'active ' . strtolower($this->filters['sort_Dir'])
                            : sortDir($this->filters, 'title', 'ASC');
                        $titleUrl = Route::url(
                            $base . '&sortby=title&sortdir='
                            . sortDir($this->filters, 'title', 'ASC')
                        );
                        $titleTitle = Lang::txt('PLG_GROUPS_FORUM_SORT_BY_TITLE');
                        ?>
                        <a class="<?php echo $titleClass; ?>"
                            href="<?php echo $titleUrl; ?>"
                            title="<?php echo $titleTitle; ?>">
                            <?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_TITLE'); ?>
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
                                'PLG_GROUPS_FORUM_SEARCH_FOR_IN',
                                $this->escape($this->filters['search']),
                                $this->escape(stripslashes($this->category->get('title')))
                            );
                        } else {
                            echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR', $this->escape($this->filters['search']));
                        }
                    } else {
                        echo Lang::txt(
                            'PLG_GROUPS_FORUM_SEARCH_IN',
                            $this->escape(stripslashes($this->category->get('title')))
                        );
                    }
                    ?>
                </caption>
                <?php
                if (
                    !$this->category->get('closed')
                    && $this->config->get('access-create-thread')
                ) {
                    $canDelete = $this->config->get('access-delete-thread');
                    $canEdit = $this->config->get('access-edit-thread');
                    $colspan = ($canDelete || $canEdit) ? '5' : '4';
                    $newUrl = Route::url($base . '/new');
                    ?>
                    <thead>
                        <tr>
                            <td colspan="<?php echo $colspan; ?>">
                                <a class="icon-add add btn"
                                    href="<?php echo $newUrl; ?>">
                                    <?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION'); ?>
                                </a>
                            </td>
                        </tr>
                    </thead>
                    <?php if (count($this->threads) > 10) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo $colspan; ?>">
                                <a class="icon-add add btn"
                                    href="<?php echo $newUrl; ?>">
                                    <?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION'); ?>
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                    <?php }
                } ?>
                <tbody>
                    <?php
                    if ($this->threads->count() > 0) {
                        foreach ($this->threads as $row) {
                            $name = Lang::txt('JANONYMOUS');
                            if (!$row->get('anonymous')) {
                                $name = $this->escape(stripslashes($row->creator->get('name', $name)));
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                                }
                            }
                            $cls = array();
                            if ($row->isClosed()) {
                                $cls[] = 'closed';
                            }
                            if ($row->isSticky()) {
                                $cls[] = 'sticky';
                            }

                            $row->set('category', $this->filters['category']);
                            $row->set('section', $this->filters['section']);
                            ?>
                            <tr<?php if (count($cls) > 0) {
                                echo ' class="' . implode(' ', $cls) . '"';
                               } ?>>
                                <th class="priority-5">
                                    <span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
                                </th>
                                <td>
                                    <a class="entry-title"
                                        href="<?php echo Route::url($base . '/' . $row->get('id')); ?>">
                                        <span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
                                    </a>
                                    <span class="entry-details">
                                        <span class="entry-date">
                                            <?php $createdDatetime = $row->created(); ?>
                                            <?php $createdDate = $row->created('date'); ?>
                                            <time datetime="<?php echo $createdDatetime; ?>">
                                                <?php echo $createdDate; ?>
                                            </time>
                                        </span>
                                        <?php
                                        $authorSpan = '<span class="entry-author">'
                                            . $name . '</span>';
                                        echo Lang::txt(
                                            'PLG_GROUPS_FORUM_BY_USER',
                                            $authorSpan
                                        );
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
                                        <?php echo Lang::txt('PLG_GROUPS_FORUM_COMMENTS'); ?>
                                    </span>
                                </td>
                                <td class="priority-3">
                                    <span><?php echo Lang::txt('PLG_GROUPS_FORUM_LAST_POST'); ?></span>
                                    <span class="entry-details">
                                        <?php
                                        $lastpost = $row->lastActivity();
                                        if ($lastpost->get('id')) {
                                            $lname = Lang::txt('JANONYMOUS');
                                            if (!$lastpost->get('anonymous')) {
                                                $lname = $this->escape(stripslashes($lastpost->creator->get('name')));
                                                if (
                                                    in_array(
                                                        $lastpost->creator->get('access'),
                                                        User::getAuthorisedViewLevels()
                                                    )
                                                ) {
                                                    $lname = '<a href="'
                                                        . Route::url($lastpost->creator->link())
                                                        . '">'
                                                        . $lname
                                                        . '</a>';
                                                }
                                            }
                                            ?>
                                            <span class="entry-date">
                                                <?php $lpDatetime = $lastpost->created(); ?>
                                                <?php $lpDate = $lastpost->created('date'); ?>
                                                <time datetime="<?php echo $lpDatetime; ?>">
                                                    <?php echo $lpDate; ?>
                                                </time>
                                            </span>
                                            <?php
                                            $lpAuthorSpan = '<span class="entry-author">'
                                                . $lname . '</span>';
                                            echo Lang::txt(
                                                'PLG_GROUPS_FORUM_BY_USER',
                                                $lpAuthorSpan
                                            );
                                            ?>
                                        <?php } else { ?>
                                            <?php echo Lang::txt('PLG_GROUPS_FORUM_NONE'); ?>
                                        <?php } ?>
                                    </span>
                                </td>
                                <?php if ($this->group->published == 1) { ?>
                                    <?php
                                    $canDeleteThread = $this->config->get('access-delete-thread');
                                    $canEditThread = $this->config->get('access-edit-thread');
                                    $isCreator = User::get('id') == $row->get('created_by');
                                    if ($canDeleteThread || $canEditThread || $isCreator) {
                                        ?>
                                        <td class="entry-options">
                                            <?php if ($isCreator || $canEditThread) { ?>
                                                <?php
                                                $editUrl = Route::url(
                                                    $base . '/' . $row->get('id') . '/edit'
                                                );
                                                ?>
                                                <a class="icon-edit edit"
                                                    href="<?php echo $editUrl; ?>">
                                                    <?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?>
                                                </a>
                                            <?php } ?>
                                            <?php if ($canDeleteThread) { ?>
                                                <?php
                                                $deleteUrl = Route::url(
                                                    $base . '/' . $row->get('id') . '/delete'
                                                ) . $sortingQueryString;
                                                ?>
                                                <a class="icon-delete delete"
                                                    href="<?php echo $deleteUrl; ?>">
                                                    <?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                            <tr>
                                <td><?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY_EMPTY'); ?></td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php
            $pageNav = $this->threads->pagination;
            $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'forum');
            $pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->filters['category']);
            echo $pageNav;
            ?>
        </div><!-- / .container -->
        <input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
        <input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
    </form>
</section><!-- /.main -->
