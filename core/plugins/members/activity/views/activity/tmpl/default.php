<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
    ->js('jquery.infinitescroll', 'com_collections')
    ->js();

$no_html = Request::getInt('no_html', 0);

// get all sessions
$online = array();
$sessions = Hubzero\Session\Helper::getAllSessions(
    array(
    'guest'    => 0,
    'distinct' => 1
    )
);
if ($sessions) {
    // see if any session matches our userid
    foreach ($sessions as $session) {
        $online[] = $session->userid;
    }
}

$qsfilters  = ($this->filters['scope'] ? '&scope=' . $this->filters['scope'] : '');
$qsfilters .= ($this->filters['created_by'] ? '&created_by=' . $this->filters['created_by'] : '');

if (!$no_html) { ?>
<div class="activities">
    <form action="<?php echo Route::url($this->member->link() . '&active=activity'); ?>" method="get">
        <fieldset class="filters">
            <div class="grid">
                <div class="col span12 omega toolbar-options">
                    <?php if ($this->filters['filter'] == 'starred') { ?>
                        <a class="icon-star tooltips"
                            href="<?php echo Route::url($this->member->link() . '&active=activity' . $qsfilters); ?>"
                            title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL'); ?>">
                            <?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL'); ?>
                        </a>
                    <?php } else {
                        $starredUrl = Route::url(
                            $this->member->link()
                            . '&active=activity&filter=starred'
                            . $qsfilters
                        );
                        $starredTitle = Lang::txt(
                            'PLG_MEMBERS_ACTIVITY_FILTER_STARRED'
                        );
                        ?>
                        <a class="icon-star-empty tooltips"
                            href="<?php echo $starredUrl; ?>"
                            title="<?php echo $starredTitle; ?>">
                            <?php echo $starredTitle; ?>
                        </a>
                    <?php } ?>
                    <?php if ($this->digests) { ?>
                        <a class="icon-config tooltips"
                            href="<?php echo Route::url($this->member->link() . '&active=activity&action=settings'); ?>"
                            title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS'); ?>">
                            <?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="grid">
                <div class="col span4">
                    <span class="form-group">
                        <label for="filter-search"><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH'); ?></label>
                        <input type="text"
                            class="form-control"
                            name="q"
                            id="filter-search"
                            value="<?php echo $this->escape($this->filters['search']); ?>"
                            placeholder="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH_PLACEHOLDER'); ?>"/>
                    </span>
                </div>
                <?php
                $scopeLabel = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_SCOPE'
                );
                $scopeAll = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_SCOPE_ALL'
                );
                $createdByLabel = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY'
                );
                $createdByAll = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_ALL'
                );
                $createdByMe = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_ME'
                );
                $createdByNotMe = Lang::txt(
                    'PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_NOTME'
                );
                $meSelected = ($this->filters['created_by'] == 'me')
                    ? ' selected="selected"' : '';
                $notmeSelected = ($this->filters['created_by'] == 'notme')
                    ? ' selected="selected"' : '';
                ?>
                <div class="col span3">
                    <span class="form-group">
                        <label for="filter-scope">
                            <?php echo $scopeLabel; ?>
                        </label>
                        <select class="form-control"
                            name="scope"
                            id="filter-scope">
                            <option value="">
                                <?php echo $scopeAll; ?>
                            </option>
                            <?php foreach ($this->categories as $category) {
                                $catVal = $this->escape($category);
                                $catSel = ($this->filters['scope'] == $category)
                                    ? ' selected="selected"' : '';
                                ?>
                                <option
                                    value="<?php echo $catVal; ?>"
                                    <?php echo $catSel; ?>>
                                    <?php echo $catVal; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </span>
                </div>
                <div class="col span3">
                    <span class="form-group">
                        <label for="filter-created_by">
                            <?php echo $createdByLabel; ?>
                        </label>
                        <select class="form-control"
                            name="created_by"
                            id="filter-created_by">
                            <option value="">
                                <?php echo $createdByAll; ?>
                            </option>
                            <option
                                value="me"
                                <?php echo $meSelected; ?>>
                                <?php echo $createdByMe; ?>
                            </option>
                            <option
                                value="notme"
                                <?php echo $notmeSelected; ?>>
                                <?php echo $createdByNotMe; ?>
                            </option>
                        </select>
                    </span>
                </div>
                <div class="col span2 omega">
                    <input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH'); ?>" />
                </div>
            </div>
        </fieldset>
<?php } ?>

        <?php if ($this->rows->count()) { ?>
            <ul class="activity-feed" data-url="<?php echo Route::url($this->member->link() . '&active=activity'); ?>">
                <?php

                foreach ($this->rows as $row) {
                    $this->view('default_item')
                        ->set('member', $this->member)
                        ->set('row', $row)
                        ->set('online', $online)
                        ->display();
                }
                ?>
            </ul>
            <?php

            //echo $this->rows->pagination;
            $pageNav = $this->pagination(
                $this->total,
                $this->filters['start'],
                $this->filters['limit']
            );
            $pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
            $pageNav->setAdditionalUrlParam('active', 'activity');
            if ($this->filters['filter']) {
                $pageNav->setAdditionalUrlParam('filter', $this->filters['filter']);
            }
            if ($this->filters['search']) {
                $pageNav->setAdditionalUrlParam('search', $this->filters['search']);
            }
            if ($this->filters['created_by']) {
                $pageNav->setAdditionalUrlParam('created_by', $this->filters['created_by']);
            }
            echo $pageNav;
            ?>
        <?php } else { ?>
            <div class="results-none">
                <div class="messages">
                    <p><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_NO_RESULTS'); ?></p>
                </div>
                <div class="questions">
                    <p>
                        <strong><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT_TITLE'); ?></strong><br />
                        <?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT'); ?>
                    <p>
                </div>
            </div>
        <?php } ?>

<?php if (!$no_html) { ?>
    </form>
</div>
<?php }
