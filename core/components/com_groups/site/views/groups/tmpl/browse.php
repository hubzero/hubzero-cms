<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this
     ->css()
     ->js('browse');

$view = $this;

// The attribute that marks the current value in the form fields below
$publishedSelected = function ($value) use ($view) {
    if ($view->filters['published'] == $value) {
        return ' selected="selected"';
    }

    return '';
};

$policySelected = function ($value) use ($view) {
    if ($view->filters['policy'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_GROUPS'); ?>: <?php echo $this->title; ?></h2>

    <?php if (User::authorise('core.create', $this->option)) : ?>
        <div id="content-header-extra">
            <p>
                <a class="icon-add add btn" href="<?= Route::url('index.php?option=' . $this->option . '&task=new') ?>">
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
                        <label for="entry-search-field"><?= Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP') ?></label>
                        <input type="text" name="search" id="entry-search-field"
                            value="<?= $this->escape($this->filters['search']) ?>"
                            placeholder="<?= Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER') ?>" />
                        <input type="hidden" name="sortby" value="<?= $this->escape($this->filters['sortby']) ?>" />
                        <input type="hidden" name="policy" value="<?= $this->escape($this->filters['policy']) ?>" />
                        <input type="hidden" name="index" value="<?= $this->escape($this->filters['index']) ?>" />
                    </fieldset>
                </div><!-- / .container -->

                <div class="container">
                    <nav class="entries-filters" aria-label="<?= Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') ?>">
                        <?php
                            $fltrs  = ($this->filters['index'])
                                ? '&index=' . $this->escape($this->filters['index'])
                                : '';
                            $fltrs .= ($this->filters['policy'])
                                ? '&policy=' . $this->escape($this->filters['policy'])
                                : '';
                            $fltrs .= ($this->filters['search'])
                                ? '&search=' . $this->escape($this->filters['search'])
                                : '';
                        ?>
                        <ul class="entries-menu order-options" data-label="<?= Lang::txt('COM_GROUPS_BROWSE_SORT') ?>">
                            <?php
                            $browseUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=browse&sortby=title' . $fltrs,
                            );
                            ?>
                            <li><a class="sort-title<?= ($this->filters['sortby'] == 'title') ? ' active' : '' ?>" href="<?= $browseUrl ?>" title="<?= Lang::txt('COM_GROUPS_BROWSE_SORT_BY_TITLE') ?>" rel="nofollow"><?= Lang::txt('COM_GROUPS_GROUP_TITLE') ?></a></li>
                            <?php
                            $browseUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=browse&sortby=alias' . $fltrs,
                            );
                            ?>
                            <li><a class="sort-alias<?= ($this->filters['sortby'] == 'alias') ? ' active' : '' ?>" href="<?= $browseUrl ?>" title="<?= Lang::txt('COM_GROUPS_BROWSE_SORT_BY_ALIAS') ?>" rel="nofollow"><?= Lang::txt('COM_GROUPS_GROUP_ALIAS') ?></a></li>
                        </ul>
                        <?php
                        $fltrs  = ($this->filters['index']) ? '&index=' . $this->escape($this->filters['index']) : '';
                        $fltrs .= ($this->filters['sortby'])
                            ? '&sortby=' . $this->escape($this->filters['sortby'])
                            : '';
                        $fltrs .= ($this->filters['search'])
                            ? '&search=' . $this->escape($this->filters['search'])
                            : '';
                        ?>
                        <ul class="entries-menu filter-options" data-label="<?= Lang::txt('COM_GROUPS_BROWSE_SHOW') ?>">
                            <li>
                                <label for="filter-published"><?= Lang::txt('COM_GROUPS_BROWSE_STATE') ?></label>
                                <select name="published" id="filter-published">
                                    <option value="1" <?= $publishedSelected(1) ?>><?= Lang::txt('COM_GROUPS_BROWSE_STATE_ACTIVE') ?></option>
                                    <option value="2" <?= $publishedSelected(2) ?>><?= Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED') ?></option>
                                </select>
                            </li>
                            <li>
                                <label for="filter-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY'); ?></label>
                                <select name="policy" id="filter-policy">
                                    <option value=""><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_ALL'); ?></option>
                                    <option value="open" <?= $policySelected('open') ?>><?= Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN') ?></option>
                                    <option value="restricted" <?= $policySelected('restricted') ?>><?= Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED') ?></option>
                                    <option value="invite" <?= $policySelected('invite') ?>><?= Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY') ?></option>
                                    <option value="closed" <?= $policySelected('closed') ?>><?= Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED') ?></option>
                                </select>
                            </li>
                        </ul>
                    </nav>

                    <div class="groups-container">
                        <?php
                        if ($this->groups) {
                            foreach ($this->groups as $group) {
                                $this
                                    ->view('_group')
                                    ->set('group', $group)
                                    ->display();
                            }
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
                            $this->filters['limit'],
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
                    <p><?= Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', Route::url('index.php?option=com_members')) ?></p>
                </div><!-- / .container -->
            <?php endif; ?>
        </aside><!-- / .aside -->
        </div>
    </section><!-- / .main section -->
</form>
