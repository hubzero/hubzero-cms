<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

// import filters
$filterln = '&' . Session::getFormToken() . '=1';
foreach ($this->filters as $key => $val) {
    if ($val) {
        $filterln .= '&' . $key . '=' . $val;
    }
}

$dcls = '';
$lcls = '';
$cls  = ' tooltips';

if (!User::isGuest()) {
    // Logged in
    $like_title    = Lang::txt('COM_WISHLIST_VOTING_I_LIKE_THIS');
    $dislike_title = Lang::txt('COM_WISHLIST_VOTING_I_DISLIKE_THIS');

    if ($this->item->get('vote')) {
        $like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_ALREADY_VOTED');
        if ($this->item->get('vote') == $this->item->get('positive')) {
            $lcls = ' chosen';
        }
        if ($this->item->get('vote') == $this->item->get('negative')) {
            $dcls = ' chosen';
        }
    }
    if (User::get('id') == $this->item->get('proposed_by')) {
        $like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_CANNOT_VOTE_FOR_OWN');
    }
    if (
        $this->item->get('status') == 1
        || $this->item->get('status') == 3
        || $this->item->get('status') == 4
    ) {
        $like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_CLOED');
    }
} else {
    // Not logged in
    $like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_LOGIN_TO_VOTE');
}

$likeUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=rateitem&refid=' . $this->item->get('id')
    . '&vote=yes&page=' . $this->page
    . $filterln
);
$dislikeUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=rateitem&refid=' . $this->item->get('id')
    . '&vote=no&page=' . $this->page
    . $filterln
);
$likePos  = ($this->item->get('positive') > 0) ? 'like' : 'neutral';
$likePos2 = ($this->item->get('positive') > 0) ? 'like' : 'like';
$dislikeNeg  = ($this->item->get('negative') > 0) ? 'dislike' : 'neutral';
$dislikeNeg2 = ($this->item->get('negative') > 0) ? 'dislike' : 'dislike';
$voteUpTxt   = Lang::txt('COM_WISHLIST_VOTING_VOTE_UP');
$voteDownTxt = Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN');
$likeTxt     = Lang::txt('COM_WISHLIST_VOTING_LIKE');
$dislikeTxt  = Lang::txt('COM_WISHLIST_VOTING_DISLIKE');
?>
<span class="vote-like<?php echo $lcls; ?>">
    <?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
        <span class="vote-button <?php echo $likePos; ?><?php echo $cls; ?>"
            title="<?php echo $voteUpTxt; ?> :: <?php echo $like_title; ?>">
            <?php echo $this->item->get('positive'); ?>
            <span> <?php echo $likeTxt; ?></span>
        </span>
    <?php } else { ?>
        <a class="vote-button <?php echo $likePos2; ?><?php echo $cls; ?>"
            href="<?php echo $likeUrl; ?>"
            title="<?php echo $voteUpTxt; ?> :: <?php echo $like_title; ?>">
            <?php echo $this->item->get('positive', 0); ?>
            <span> <?php echo $likeTxt; ?></span>
        </a>
    <?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
    <?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
        <span class="vote-button <?php echo $dislikeNeg; ?><?php echo $cls; ?>"
            title="<?php echo $voteDownTxt; ?> :: <?php echo $dislike_title; ?>">
            <?php echo $this->item->get('negative'); ?>
            <span> <?php echo $dislikeTxt; ?></span>
        </span>
    <?php } else { ?>
        <a class="vote-button <?php echo $dislikeNeg2; ?><?php echo $cls; ?>"
            href="<?php echo $dislikeUrl; ?>"
            title="<?php echo $voteDownTxt; ?> :: <?php echo $dislike_title; ?>">
            <?php echo $this->item->get('negative', 0); ?>
            <span> <?php echo $dislikeTxt; ?></span>
        </a>
    <?php } ?>
</span>
