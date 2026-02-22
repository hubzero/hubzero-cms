<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('browse');
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_GROUPS'); ?>: <?php echo $this->title; ?></h2>

    <?php if (User::authorise('core.create', $this->option)) : ?>
        <div id="content-header-extra">
            <p>
                <?php $url = Route::url('index.php?option=' . $this->option . '&task=new'); ?>
                <a class="icon-add add btn" href="<?php echo $url; ?>">
                    <?php echo Lang::txt('COM_GROUPS_NEW'); ?>
                </a>
            </p>
        </div><!-- / #content-header-extra -->
    <?php endif; ?>
</header>

<?php
foreach ($this->notifications as $notification) {
    echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
    <section class="main section">
        <div class="section-inner hz-layout-with-aside">
            <div class="subject">

                <div class="container data-entry">
                    <input class="entry-search-submit" type="submit" value="Search" />
                    <fieldset class="entry-search">
                        <legend><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_LEGEND'); ?></legend>
                        <?php $txt = Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP'); ?>
                        <label for="entry-search-field"><?php echo $txt; ?></label>
                        <?php $v1 = $this->escape($this->filters['search']); ?>
                        <?php $v0 = Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER'); ?>
                        <input
                            type="text"
                            name="search"
                            id="entry-search-field"
                            value="<?php echo $v1; ?>" placeholder="<?php echo $v0; ?>" />
                        <input
                            type="hidden"
                            name="sortby"
                            value="<?php echo $this->escape($this->filters['sortby']); ?>" />
                        <input
                            type="hidden"
                            name="policy"
                            value="<?php echo $this->escape($this->filters['policy']); ?>" />
                        <input
                            type="hidden"
                            name="index"
                            value="<?php echo $this->escape($this->filters['index']); ?>" />
                    </fieldset>
                </div><!-- / .container -->

                <div class="container">
                    <?php $txt = Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>
                    <nav class="entries-filters" aria-label="<?php echo $txt; ?>">
                        <?php
                            $fltrs  = ($this->filters['index'])  ? '&index=' .
                                $this->escape($this->filters['index'])   : '';
                            $fltrs .= ($this->filters['policy']) ? '&policy=' .
                                $this->escape($this->filters['policy']) : '';
                            $fltrs .= ($this->filters['search']) ? '&search=' .
                                $this->escape($this->filters['search']) : '';
                        ?>
                        <?php $txt = Lang::txt('COM_GROUPS_BROWSE_SORT'); ?>
                        <ul class="entries-menu order-options" data-label="<?php echo $txt; ?>">
                            <?php
                            $titleActive = ($this->filters['sortby'] == 'title') ? ' active' : '';
                            $aliasActive = ($this->filters['sortby'] == 'alias') ? ' active' : '';
                            $titleUrl = Route::url(
                                'index.php?option=' . $this->option .
                                '&task=browse&sortby=title' . $fltrs
                            );
                            $aliasUrl = Route::url(
                                'index.php?option=' . $this->option .
                                '&task=browse&sortby=alias' . $fltrs
                            );
                            $sortByTitle = Lang::txt('COM_GROUPS_BROWSE_SORT_BY_TITLE');
                            $sortByAlias = Lang::txt('COM_GROUPS_BROWSE_SORT_BY_ALIAS');
                            $titleTxt = Lang::txt('COM_GROUPS_GROUP_TITLE');
                            $aliasTxt = Lang::txt('COM_GROUPS_GROUP_ALIAS');
                            ?>
                            <li>
                                <a class="sort-title<?php echo $titleActive; ?>"
                                    href="<?php echo $titleUrl; ?>"
                                    title="<?php echo $sortByTitle; ?>"><?php echo $titleTxt; ?></a>
                            </li>
                            <li>
                                <a class="sort-alias<?php echo $aliasActive; ?>"
                                    href="<?php echo $aliasUrl; ?>"
                                    title="<?php echo $sortByAlias; ?>"><?php echo $aliasTxt; ?></a>
                            </li>
                        </ul>
                        <?php
                        $fltrs  = ($this->filters['index'])  ? '&index=' .
                            $this->escape($this->filters['index'])   : '';
                        $fltrs .= ($this->filters['sortby']) ? '&sortby=' .
                            $this->escape($this->filters['sortby']) : '';
                        $fltrs .= ($this->filters['search']) ? '&search=' .
                            $this->escape($this->filters['search']) : '';
                        ?>
                        <?php $txt = Lang::txt('COM_GROUPS_BROWSE_SHOW'); ?>
                        <ul class="entries-menu filter-options" data-label="<?php echo $txt; ?>">
                            <li>
                                <?php $txt = Lang::txt('COM_GROUPS_BROWSE_STATE'); ?>
                                <label for="filter-published"><?php echo $txt; ?></label>
                                <select name="published" id="filter-published">
                                    <?php $val1 = ($this->filters['published'] == 1) ? ' selected="selected"' : ''; ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_STATE_ACTIVE'); ?>
                                    <option value="1" <?php echo $val1; ?>><?php echo $val; ?></option>
                                    <?php $val1 = ($this->filters['published'] == 2) ? ' selected="selected"' : ''; ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED'); ?>
                                    <option value="2" <?php echo $val1; ?>><?php echo $val; ?></option>
                                </select>
                            </li>
                            <li>
                                <label for="filter-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY'); ?></label>
                                <select name="policy" id="filter-policy">
                                    <option value=""><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_ALL'); ?></option>
                                    <?php $val1 = ($this->filters['policy'] == 'open') ? ' selected="selected"' : ''; ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?>
                                    <option value="open" <?php echo $val1; ?>><?php echo $val; ?></option>
                                    <?php
                                    $val1 = ($this->filters['policy'] == 'restricted')
                                        ? ' selected="selected"' : '';
                                    ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?>
                                    <option value="restricted" <?php echo $val1; ?>><?php echo $val; ?></option>
                                    <?php
                                    $val1 = ($this->filters['policy'] == 'invite')
                                        ? ' selected="selected"' : '';
                                    ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?>
                                    <option value="invite" <?php echo $val1; ?>><?php echo $val; ?></option>
                                    <?php
                                    $val1 = ($this->filters['policy'] == 'closed')
                                        ? ' selected="selected"' : '';
                                    ?>
                                    <?php $val = Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?>
                                    <option value="closed" <?php echo $val1; ?>><?php echo $val; ?></option>
                                </select>
                            </li>
                        </ul>
                    </nav>

                    <div class="groups-container">
                        <?php
                        if ($this->groups) {
                            foreach ($this->groups as $group) {
                                $this->view('_group')
                                    ->set('group', $group)
                                    ->display();
                            } // for loop
                            ?>
                        <?php } else { ?>
                            <div class="results-none">
                                <p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
                            </div>
                        <?php } ?>
                    </div><!-- / .groups -->
                    <?php if ($this->groups) : ?>
                        <?php
                        // Initiate paging
                        $pageNav = $this->pagination(
                            $this->total,
                            $this->filters['start'],
                            $this->filters['limit']
                        );
                        $pageNav->setAdditionalUrlParam('index', $this->filters['index']);
                        $pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
                        $pageNav->setAdditionalUrlParam('policy', $this->filters['policy']);
                        $pageNav->setAdditionalUrlParam('search', $this->filters['search']);

                        echo $pageNav->render();
                        ?>
                    <?php endif; ?>
                </div><!-- / .container -->
            </div><!-- / .subject -->
            <aside class="aside">
            <div class="container">
                <h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_TITLE'); ?></h3>
                <p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_ONE'); ?></p>
                <p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_TWO'); ?></p>
                <p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_THREE'); ?></p>
            </div><!-- / .container -->

            <?php if (Component::isEnabled('com_members')) : ?>
                <div class="container">
                    <h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_TITLE'); ?></h3>
                    <?php $url = Route::url('index.php?option=com_members'); ?>
                    <p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', $url); ?></p>
                </div><!-- / .container -->
            <?php endif; ?>
        </aside><!-- / .aside -->
        </div>
    </section><!-- / .main section -->
</form>