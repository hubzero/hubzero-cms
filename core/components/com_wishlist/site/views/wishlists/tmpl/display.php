<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('vote.css')
     ->js();

$sitename = Config::get('sitename');

$base = $this->wishlist->link();

$cloud = new \Components\Wishlist\Models\Tags($this->wishlist->get('id'));

// Wish List
if ($this->wishlist->get('id')) {
    if (!$this->wishlist->isPublic() && !$this->wishlist->access('manage')) { ?>
        <section class="main section">
            <p class="waring"><?php echo Lang::txt('WARNING_NOT_AUTHORIZED_PRIVATE_LIST'); ?></p>
        </section><!-- / .main section -->
    <?php } else { ?>
        <header id="content-header">
            <h2><?php echo $this->title; ?></h2>

            <div id="content-header-extra">
                <ul id="useroptions">
                    <li class="last">
                        <a class="icon-add add btn"
                            href="<?php echo Route::url($this->wishlist->link('new')); ?>">
                            <?php echo Lang::txt('COM_WISHLIST_TASK_ADD'); ?>
                        </a>
                    </li>
                </ul>
            </div><!-- / #content-header-extra -->
        </header><!-- / #content-header -->

        <form method="get" action="<?php echo Route::url($base); ?>">
            <?php
            // Admin messages
            if ($this->wishlist->access('manage') && !$this->getError()) {
                // Wish was deleted from the list
                if ($this->task == 'deletewish') {
                    echo '<p class="passed">' . Lang::txt('COM_WISHLIST_NOTICE_WISH_DELETED') . '</p>' . "\n";
                }

                // Wish was moved to a new list
                if ($this->task == 'movewish') {
                    echo '<p class="passed">' . Lang::txt('COM_WISHLIST_NOTICE_WISH_MOVED') . '</p>' . "\n";
                }

                switch ($this->wishlist->get('saved')) {
                    case '1':
                        // List settings saved
                        echo '<p class="passed">'
                            . Lang::txt('COM_WISHLIST_NOTICE_LIST_SETTINGS_SAVED') . '</p>' . "\n";
                        break;
                    case '2':
                        // Changes to wish saved
                        echo '<p class="passed">'
                            . Lang::txt('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED') . '</p>' . "\n";
                        break;
                    case '3':
                        // New wish posted
                        echo '<p class="passed">'
                            . Lang::txt('COM_WISHLIST_NOTICE_WISH_POSTED') . '</p>' . "\n";
                        break;
                }
            }
            ?>
            <?php if ($this->getError()) { ?>
                <p class="error"><?php echo $this->getError(); ?></p>
            <?php } ?>

            <section class="main section">
                <div class="section-inner hz-layout-with-aside">
                    <div class="subject">
                        <div class="container data-entry">
                            <input class="entry-search-submit"
                                type="submit"
                                value="<?php echo Lang::txt('COM_WISHLIST_SEARCH'); ?>" />
                            <fieldset class="entry-search">
                                <legend><?php echo Lang::txt('COM_WISHLIST_SEARCH_LEGEND'); ?></legend>

                                <label for="entry-search-field">
                                    <?php echo Lang::txt('COM_WISHLIST_SEARCH_LABEL'); ?>
                                </label>
                                <?php $searchVal = $this->escape($this->filters['search']); ?>
                                <?php $searchPh = Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>
                                <input type="text"
                                    name="search"
                                    id="entry-search-field"
                                    value="<?php echo $searchVal; ?>"
                                    placeholder="<?php echo $searchPh; ?>" />

                                <input type="hidden"
                                    name="tags"
                                    value="<?php echo $this->escape($this->filters['tag']); // xss fix ?>" />
                                <input type="hidden"
                                    name="sortby"
                                    value="<?php echo $this->escape($this->filters['sortby']); // xss fix ?>" />
                                <input type="hidden"
                                    name="filterby"
                                    value="<?php echo $this->escape($this->filters['filterby']); // xss fix ?>" />

                                <input type="hidden"
                                    name="task"
                                    value="<?php echo $this->escape($this->task); /* XSS fix, see ticket 1420*/ ?>" />
                                <input type="hidden" name="newsearch" value="1" />
                            </fieldset>
                            <?php if ($this->filters['tag']) { ?>
                                <fieldset class="applied-tags">
                                    <ol class="tags">
                                    <?php
                                    $url  = $base;
                                    $url .= ($this->filters['search']
                                        ? '&search=' . $this->escape($this->filters['search']) : '');
                                    $url .= ($this->filters['sortby']
                                        ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
                                    $url .= ($this->filters['filterby']
                                        ? '&filterby=' . $this->escape($this->filters['filterby']) : '');

                                    $tags = $cloud->parseTags($this->filters['tag']);
                                    foreach ($tags as $tag) {
                                        $remainingTags = $cloud->parseTags($this->filters['tag'], $tag);
                                        $tagUrl = Route::url($url . '&tag=' . implode(',', $remainingTags));
                                        ?>
                                        <li>
                                            <a class="tag" href="<?php echo $tagUrl; ?>">
                                                <?php echo $this->escape(stripslashes($tag)); ?>
                                                <span class="remove">x</a>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    </ol>
                                </fieldset>
                            <?php } ?>
                        </div><!-- / .container data-entry -->

                        <div class="container">
                            <nav class="entries-filters"
                                aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
                                <?php
                                $filterby = $this->filters['filterby'];
                                $sortby   = $this->filters['sortby'];
                                $tag      = $this->filters['tag'];
                                ?>
                                <ul class="entries-menu order-options"
                                    data-label="<?php echo Lang::txt('COM_WISHLIST_SORT'); ?>">
                                    <?php if ($this->wishlist->get('admin')) { ?>
                                        <?php
                                        $rankActive = ($sortby == 'ranking') ? ' active' : '';
                                        $rankUrl = Route::url(
                                            $base . '&filterby=' . $filterby
                                            . '&sortby=ranking&tags=' . $tag
                                        );
                                        $rankTitle = Lang::txt('COM_WISHLIST_SORT_RANKING_TITLE');
                                        $rankLabel = Lang::txt('COM_WISHLIST_SORT_RANKING');
                                        ?>
                                        <li>
                                            <a class="icon-arrow-down sort-ranking<?php echo $rankActive; ?>"
                                                href="<?php echo $rankUrl; ?>"
                                                title="<?php echo $rankTitle; ?>">
                                                <?php echo $rankLabel; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($this->wishlist->get('banking')) { ?>
                                        <?php
                                        $bonusActive = ($sortby == 'bonus') ? ' active' : '';
                                        $bonusUrl = Route::url(
                                            $base . '&filterby=' . $filterby
                                            . '&sortby=bonus&tags=' . $tag
                                        );
                                        $bonusTitle = Lang::txt('COM_WISHLIST_SORT_BONUS_TITLE');
                                        $bonusLabel = Lang::txt('COM_WISHLIST_SORT_BONUS');
                                        ?>
                                        <li>
                                            <a class="icon-arrow-down sort-bonus<?php echo $bonusActive; ?>"
                                                href="<?php echo $bonusUrl; ?>"
                                                title="<?php echo $bonusTitle; ?>">
                                                <?php echo $bonusLabel; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $feedbackActive = ($sortby == 'feedback') ? ' active' : '';
                                    $feedbackUrl = Route::url(
                                        $base . '&filterby=' . $filterby
                                        . '&sortby=feedback&tags=' . $tag
                                    );
                                    $feedbackTitle = Lang::txt('COM_WISHLIST_SORT_FEEDBACK_TITLE');
                                    $feedbackLabel = Lang::txt('COM_WISHLIST_SORT_FEEDBACK');
                                    $submitterActive = ($sortby == 'submitter') ? ' active' : '';
                                    $submitterUrl = Route::url(
                                        $base . '&filterby=' . $filterby
                                        . '&sortby=submitter&tags=' . $tag
                                    );
                                    $submitterTitle = Lang::txt('COM_WISHLIST_SORT_SUBMITTER_TITLE');
                                    $submitterLabel = Lang::txt('COM_WISHLIST_SORT_SUBMITTER');
                                    $dateActive = ($sortby == 'date') ? ' active' : '';
                                    $dateUrl = Route::url(
                                        $base . '&filterby=' . $filterby
                                        . '&sortby=date&tags=' . $tag
                                    );
                                    $dateLabel = Lang::txt('COM_WISHLIST_SORT_DATE');
                                    ?>
                                    <li>
                                        <a class="icon-arrow-down sort-feedback<?php echo $feedbackActive; ?>"
                                            href="<?php echo $feedbackUrl; ?>"
                                            title="<?php echo $feedbackTitle; ?>">
                                            <?php echo $feedbackLabel; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="icon-arrow-down sort-submitter<?php echo $submitterActive; ?>"
                                            href="<?php echo $submitterUrl; ?>"
                                            title="<?php echo $submitterTitle; ?>">
                                            <?php echo $submitterLabel; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="icon-arrow-down sort-date<?php echo $dateActive; ?>"
                                            href="<?php echo $dateUrl; ?>"
                                            title="<?php echo $dateLabel; ?>">
                                            <?php echo $dateLabel; ?>
                                        </a>
                                    </li>
                                </ul>

                                <ul class="entries-menu filter-options"
                                    data-label="<?php echo Lang::txt('COM_WISHLIST_FILTER'); ?>">
                                    <?php
                                    $allActive      = ($filterby == 'all') ? ' active' : '';
                                    $allUrl         = Route::url(
                                        $base . '&filterby=all&sortby=' . $sortby . '&tags=' . $tag
                                    );
                                    $openActive     = ($filterby == 'open') ? ' active' : '';
                                    $openUrl        = Route::url(
                                        $base . '&filterby=open&sortby=' . $sortby . '&tags=' . $tag
                                    );
                                    $acceptedActive = ($filterby == 'accepted') ? ' active' : '';
                                    $acceptedUrl    = Route::url(
                                        $base . '&filterby=accepted&sortby=' . $sortby . '&tags=' . $tag
                                    );
                                    $rejectedActive = ($filterby == 'rejected') ? ' active' : '';
                                    $rejectedUrl    = Route::url(
                                        $base . '&filterby=rejected&sortby=' . $sortby . '&tags=' . $tag
                                    );
                                    $grantedActive  = ($filterby == 'granted') ? ' active' : '';
                                    $grantedUrl     = Route::url(
                                        $base . '&filterby=granted&sortby=' . $sortby . '&tags=' . $tag
                                    );
                                    ?>
                                    <li>
                                        <a class="filter-all<?php echo $allActive; ?>"
                                            href="<?php echo $allUrl; ?>">
                                            <?php echo Lang::txt('COM_WISHLIST_FILTER_ALL'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="filter-open<?php echo $openActive; ?>"
                                            href="<?php echo $openUrl; ?>">
                                            <?php echo Lang::txt('COM_WISHLIST_FILTER_OPEN'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="filter-accepted<?php echo $acceptedActive; ?>"
                                            href="<?php echo $acceptedUrl; ?>">
                                            <?php echo Lang::txt('COM_WISHLIST_FILTER_ACCEPTED'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="filter-rejected<?php echo $rejectedActive; ?>"
                                            href="<?php echo $rejectedUrl; ?>">
                                            <?php echo Lang::txt('COM_WISHLIST_FILTER_REJECTED'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="filter-granted<?php echo $grantedActive; ?>"
                                            href="<?php echo $grantedUrl; ?>">
                                            <?php echo Lang::txt('COM_WISHLIST_FILTER_GRANTED'); ?>
                                        </a>
                                    </li>
                                    <?php if (!User::isGuest()) { ?>
                                        <?php
                                        $submitterFilterActive = ($filterby == 'submitter') ? ' active' : '';
                                        $submitterFilterUrl = Route::url(
                                            $base . '&filterby=submitter&sortby=' . $sortby . '&tags=' . $tag
                                        );
                                        ?>
                                        <li>
                                            <a class="filter-submitter<?php echo $submitterFilterActive; ?>"
                                                href="<?php echo $submitterFilterUrl; ?>">
                                                <?php echo Lang::txt('COM_WISHLIST_FILTER_SUBMITTER'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($this->wishlist->access('manage')) { //1 or 2?>
                                        <?php
                                        $publicActive  = ($filterby == 'public') ? ' active' : '';
                                        $publicUrl     = Route::url(
                                            $base . '&filterby=public&sortby=' . $sortby . '&tags=' . $tag
                                        );
                                        $privateActive = ($filterby == 'private') ? ' active' : '';
                                        $privateUrl    = Route::url(
                                            $base . '&filterby=private&sortby=' . $sortby . '&tags=' . $tag
                                        );
                                        ?>
                                        <li>
                                            <a class="filter-public<?php echo $publicActive; ?>"
                                                href="<?php echo $publicUrl; ?>">
                                                <?php echo Lang::txt('COM_WISHLIST_FILTER_PUBLIC'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="filter-private<?php echo $privateActive; ?>"
                                                href="<?php echo $privateUrl; ?>">
                                                <?php echo Lang::txt('COM_WISHLIST_FILTER_PRIVATE'); ?>
                                            </a>
                                        </li>
                                        <?php if ($this->wishlist->access('own')) { // 2?>
                                            <?php
                                            $mineActive = ($filterby == 'mine') ? ' active' : '';
                                            $mineUrl    = Route::url(
                                                $base . '&filterby=mine&sortby=' . $sortby . '&tags=' . $tag
                                            );
                                            ?>
                                            <li>
                                                <a class="filter-mine<?php echo $mineActive; ?>"
                                                    href="<?php echo $mineUrl; ?>">
                                                    <?php echo Lang::txt('COM_WISHLIST_FILTER_MINE'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </nav>

                            <table class="ideas entries">
                                <caption>
                                    <?php echo Lang::txt('COM_WISHLIST_FILTER_' . strtoupper($filterby)); ?>
                                    <?php
                                    if ($this->filters['tag'] != '') {
                                        echo Lang::txt(
                                            'COM_WISHLIST_WISHES_TAGGED_WITH',
                                            $this->escape($this->filters['tag'])
                                        );
                                    }
                                    ?>
                                    <span>
                                        (<?php
                                        echo ($this->total > 0)
                                            ? ($this->filters['start'] + 1) : $this->filters['start'];
                                        ?> - <?php echo $this->filters['start'] + $this->wishes->count();
?> of <?php echo $this->total; ?>)
                                    </span>
                                </caption>
                                <tbody>
                            <?php
                            if ($this->wishes->count()) {
                                $y = 1;

                                $filters  = '';
                                foreach ($this->filters as $key => $flt) {
                                    if ($flt) {
                                        if ($key == 'comments') {
                                            continue;
                                        }
                                        $filters .= '&' . $key . '=' . $flt;
                                    }
                                }

                                foreach ($this->wishes as $item) {
                                    $item->set('category', $this->wishlist->get('category'));
                                    $item->set('referenceid', $this->wishlist->get('referenceid'));
                                    $item->set('bonus', ($this->wishlist->get('banking') ? $item->get('bonus') : 0));

                                    if ($item->isReported()) {
                                        $status = 'outstanding';
                                    } elseif (
                                        $item->get('ranked')
                                        && !$item->isGranted()
                                        && !$item->isWithdrawn()
                                        && !$item->isRejected()
                                        && $this->wishlist->access('manage')
                                    ) {
                                        $status = 'unranked';
                                    } else {
                                        $status = 'outstanding';
                                    }

                                    $state = $item->status('alias');

                                    $icn = 'icon-lightbulb';
                                    if ($state == 'granted') {
                                        $icn = 'icon-success';
                                    }
                                    if ($state == 'rejected') {
                                        $icn = 'icon-remove';
                                    }
                                    if ($state == 'flagged') {
                                        $icn = 'icon-flag';
                                    }
                                    if ($state == 'withdrawn') {
                                        $icn = 'icon-delete';
                                    }

                                    if ($item->isPrivate()) {
                                        $state .= ' private';
                                    }

                                    $name = Lang::txt('JANONYMOUS');
                                    if (!$item->get('anonymous')) {
                                        $name = $this->escape(
                                            stripslashes($item->proposer->get('name', $name))
                                        );
                                        if (in_array($item->proposer->get('access'), User::getAuthorisedViewLevels())) {
                                            $proposerUrl = Route::url($item->proposer->link());
                                            $name = '<a href="' . $proposerUrl . '">' . $name . '</a>';
                                        }
                                    }

                                    $itemUrl      = Route::url($item->link('permalink', $filters));
                                    $commentsUrl  = Route::url($item->link('comments'));
                                    $commentTotal = $item->comments()->total();
                                    $commentsTxt  = Lang::txt('COM_WISHLIST_COMMENTS');
                                    ?>
                                    <tr class="<?php echo $state; ?>">
                                        <th scope="row" class="priority-5 <?php echo $status; ?>">
                                            <span class="<?php echo $icn; ?> entry-status">
                                                <?php echo $status; ?>
                                            </span>
                                        </th>
                                        <td>
                                    <?php if (!$item->isReported()) { ?>
                                            <a class="entry-title" href="<?php echo $itemUrl; ?>">
                                                <?php echo $this->escape(stripslashes($item->get('subject'))); ?>
                                            </a>
                                            <br />
                                            <span class="entry-details">
                                                <span class="entry-identifier">#<?php echo $item->get('id'); ?></span>
                                                <span class="entry-details-divider">&bull;</span>
                                                <?php echo Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY'); ?>
                                                <?php echo $name . ' '; ?>
                                                <span class="entry-date-at">
                                                    <?php echo Lang::txt('COM_WISHLIST_AT'); ?>
                                                </span>
                                                <span class="entry-time">
                                                    <time datetime="<?php echo $item->proposed(); ?>">
                                                        <?php echo $item->proposed('time'); ?>
                                                    </time>
                                                </span>
                                                <span class="entry-date-on">
                                                    <?php echo Lang::txt('COM_WISHLIST_ON'); ?>
                                                </span>
                                                <span class="entry-date">
                                                    <time datetime="<?php echo $item->proposed(); ?>">
                                                        <?php echo $item->proposed('date'); ?>
                                                    </time>
                                                </span>
                                                <span class="entry-details-divider">&bull;</span>
                                                <span class="entry-comments">
                                                    <?php $commentTitle = $commentTotal . ' ' . $commentsTxt; ?>
                                                    <a href="<?php echo $commentsUrl; ?>"
                                                        title="<?php echo $commentTitle; ?>">
                                                        <?php echo $commentTotal; ?>
                                                    </a>
                                                </span>
                                            </span>
                                    <?php } else { ?>
                                            <span class="warning adjust">
                                                <?php echo Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?>
                                            </span>
                                    <?php } ?>
                                        </td>
                                    <?php if (!$item->isReported() && $this->wishlist->get('banking')) { ?>
                                        <td class="priority-5 reward">
                                            <span class="entry-reward">
                                            <?php
                                            $hasBonus = $item->get('bonus') > 0
                                                && ($item->isOpen() or $item->isAccepted());
                                            if ($hasBonus) {
                                                ?>
                                                <?php
                                                $bonusLinkUrl = Route::url($item->link('addbonus', $filters));
                                                $bonusByCount = $item->get('bonusgivenby');
                                                $multipleUsers = Lang::txt('COM_WISHLIST_MULTIPLE_USERS');
                                                $contributedTxt = Lang::txt(
                                                    'COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL'
                                                );
                                                $pointsTxt = Lang::txt('COM_WISHLIST_POINTS');
                                                $asBonusTxt = Lang::txt('COM_WISHLIST_WISH_BONUS_AS_BONUS');
                                                $addBonusTxt = Lang::txt('COM_WISHLIST_WISH_ADD_BONUS');
                                                $bonusTitle = $addBonusTxt . ' ::' . $bonusByCount
                                                    . ' ' . $multipleUsers . ' ' . $contributedTxt
                                                    . ' ' . $item->get('bonus') . ' ' . $pointsTxt
                                                    . ' ' . $asBonusTxt;
                                                ?>
                                                <a class="bonus tooltips"
                                                    href="<?php echo $bonusLinkUrl; ?>"
                                                    title="<?php echo $bonusTitle; ?>">
                                                    <?php echo $item->get('bonus', 0); ?>
                                                    <span><?php echo Lang::txt('COM_WISHLIST_POINTS'); ?></span>
                                                </a>
                                            <?php } elseif ($item->isOpen() or $item->isAccepted()) { ?>
                                                <?php
                                                $nobonusLinkUrl = Route::url($item->link('addbonus', $filters));
                                                $addBonusTxt2 = Lang::txt('COM_WISHLIST_WISH_ADD_BONUS');
                                                $noUsersTxt = Lang::txt('COM_WISHLIST_WISH_BONUS_NO_USERS_CONTRIBUTED');
                                                $nobonusTitle = $addBonusTxt2 . ' :: ' . $noUsersTxt;
                                                ?>
                                                <a class="nobonus tooltips"
                                                    href="<?php echo $nobonusLinkUrl; ?>"
                                                    title="<?php echo $nobonusTitle; ?>">
                                                    <?php echo $item->get('bonus', 0); ?>
                                                    <span><?php echo Lang::txt('COM_WISHLIST_POINTS'); ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <?php
                                                $notAcceptedTxt = Lang::txt(
                                                    'COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED'
                                                );
                                                ?>
                                                <span class="inactive"
                                                    title="<?php echo $notAcceptedTxt; ?>">
                                                    &nbsp;
                                                </span>
                                            <?php } ?>
                                            </span>
                                        </td>
                                    <?php } ?>
                                    <?php if (!$item->isReported()) { ?>
                                        <td class="priority-4 voting">
                                            <?php
                                            $this->view('_vote')
                                                 ->set('option', $this->option)
                                                 ->set('item', $item)
                                                 ->set('listid', $this->wishlist->get('id'))
                                                 ->set('plugin', 0)
                                                 ->set('admin', 0)
                                                 ->set('page', 'wishlist')
                                                 ->set('filters', $this->filters)
                                                 ->display();
                                            ?>
                                        </td>
                                        <td class="priority-3 ranking">
                                            <?php
                                            $html = '';
                                            switch ($item->get('status')) {
                                                case 0:
                                                    if (!$item->get('ranked') && $this->wishlist->access('manage')) {
                                                        $rankLink = $item->link('rank', $filters);
                                                        $rankTxt  = Lang::txt('COM_WISHLIST_WISH_RANK_THIS');
                                                        $html .= '<a class="rankit" href="' . $rankLink . '">'
                                                            . $rankTxt . '</a>' . "\n";
                                                    } elseif ($item->get('ranked')) {
                                                        $pct = (($item->get('ranking', 0) / 50) * 100);
                                                        $this->css('
															.priority-level-' . $item->get('id') . ' {
																width: ' . $pct . '%;
															}
														');

                                                        $priorTxt = Lang::txt('COM_WISHLIST_WISH_PRIORITY');
                                                        $html .= '<span class="priority-level-base">
															<span class="priority-level priority-level-'
                                                            . $item->get('id') . '" title="' . $pct . '%">
																<span>' . $priorTxt . ': '
                                                            . $item->get('ranking', 0) . '</span>
															</span>
														</span>';
                                                    }
                                                    if ($item->isAccepted()) {
                                                        $html .= '<span class="accepted">'
                                                            . Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED')
                                                            . '</span>';
                                                    }
                                                    break;
                                                case 1:
                                                    $html .= '<span class="granted">'
                                                        . Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED') . '</span>';
                                                    break;
                                                case 3:
                                                    $html .= '<span class="rejected">'
                                                        . Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED') . '</span>';
                                                    break;
                                                case 4:
                                                    $html .= '<span class="withdrawn">'
                                                        . Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN') . '</span>';
                                                    break;
                                            }
                                            echo $html;
                                            ?>
                                        </td>
                                    <?php } // end if (!$item->isReported()) ?>
                                    </tr>
                                <?php } // end foreach wish ?>
                            <?php } else { ?>
                                    <tr>
                                        <td>
                                        <?php if ($this->filters['filterby'] == 'all' && !$this->filters['tag']) { ?>
                                            <p>
                                                <?php echo Lang::txt('COM_WISHLIST_NO_WISHES_BE_FIRST'); ?>
                                            </p>
                                        <?php } else { ?>
                                            <p class="noresults">
                                                <?php echo Lang::txt('COM_WISHLIST_NO_WISHES_SELECTION'); ?>
                                            </p>
                                            <p class="nav_wishlist">
                                                <a href="<?php echo Route::url($base); ?>">
                                                    <?php echo Lang::txt('COM_WISHLIST_VIEW_ALL_WISHES'); ?>
                                                </a>
                                            </p>
                                        <?php } ?>
                                        </td>
                                    </tr>
                            <?php } // end if wishlist item ?>
                                </tbody>
                            </table>
                            <?php
                            // Page navigation
                            $pageNav = $this->pagination(
                                $this->total,
                                $this->filters['start'],
                                $this->filters['limit']
                            );
                            $pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
                            $pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
                            $pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
                            $pageNav->setAdditionalUrlParam('newsearch', 0);
                            if ($this->filters['search']) {
                                $pageNav->setAdditionalUrlParam('search', $this->filters['search']);
                            }
                            echo $pageNav->render();
                            ?>
                            <div class="clearfix"></div>
                        </div><!-- / .container -->
                    </div><!-- / .subject -->
                    <aside class="aside">
                    <?php
                        // Popular tags
                    if ($this->wishlist->get('category') == 'general') {
                        $tags = $cloud->render('html', array(
                            'limit'    => $this->config->get('maxtags', 10),
                            'start'    => 0,
                            'sort'     => 'total',
                            'sort_Dir' => '',
                            'scope'    => 'wishlist',
                            'scope_id' => 0,
                            'base'     => $base,
                            'filters'  => $this->filters
                        ));

                        if ($tags) {
                            ?>
                                <div class="container">
                                    <h3><?php echo Lang::txt('COM_WISHLIST_POPULAR_TAGS'); ?></h3>
                                <?php echo $tags; ?>
                                    <p><?php echo Lang::txt('COM_WISHLIST_CLICK_TAG_TO_FILTER'); ?></p>
                                </div><!-- / .container -->
                                <?php
                        } // end if ($tags)
                    } // end if ($this->wishlist->category == 'general')

                    if ($this->wishlist->get('category') == 'resource') {
                        $resUrl   = Route::url(
                            'index.php?option=com_resources&id=' . $this->wishlist->get('referenceid')
                        );
                        $resTitle = $this->escape($this->wishlist->item('title'));
                        $html = '<p>' . Lang::txt(
                            'COM_WISHLIST_THIS_LIST_IS_FOR_RES',
                            '<a href="' . $resUrl . '">' . $resTitle . '</a>'
                        ) . '</p>';
                    } elseif ($this->wishlist->get('description')) {
                        $html  = '<p>' . $this->escape($this->wishlist->get('description')) . '<p>';
                    } else {
                        $html  = '<p>' . Lang::txt('COM_WISHLIST_HELP_US_IMPROVE', $sitename) . '</p>';
                    }

                    switch ($this->wishlist->get('admin')) {
                        case '1':
                            $html .= '<p class="info">'
                                . Lang::txt('COM_WISHLIST_NOTICE_SITE_ADMIN') . '</p>' . "\n";
                            break;
                        case '2':
                            $settingsUrl = Route::url($this->wishlist->link('settings'));
                            $settingsTxt = Lang::txt('COM_WISHLIST_LIST_SETTINGS');
                            $html .= '<p class="info">'
                                . Lang::txt('COM_WISHLIST_NOTICE_LIST_ADMIN')
                                . ' Edit <a href="' . $settingsUrl . '">' . $settingsTxt . '</a>.'
                                . '</p>' . "\n";
                            break;
                        case '3':
                            $html .= '<p class="info">'
                                . Lang::txt('COM_WISHLIST_NOTICE_ADVISORY_ADMIN') . '</p>' . "\n";
                            break;
                    }
                        echo $html;

                        // Show what's popular
                    if (
                        $this->wishlist->access('manage')
                         && $this->wishes->count() >= 10
                         && $this->wishlist->get('category') == 'general'
                         && $this->filters['filterby'] == 'all'
                    ) {
                        Request::setVar('rid', $this->wishlist->get('referenceid'));
                        Request::setVar('category', $this->wishlist->get('category'));

                        echo \Hubzero\Module\Helper::renderModules('wishvoters');
                    }
                    ?>
                </aside><!-- / .aside -->
                </div>
            </section><!-- / .main section -->
        </form>
    <?php } // end if public ?>
<?php } else { ?>
    <p class="error"><?php echo Lang::txt('COM_WISHLIST_ERROR_LIST_NOT_FOUND'); ?></p>
<?php } // end if wish list
