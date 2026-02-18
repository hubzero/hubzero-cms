<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$dcls = '';
$lcls = '';

if ($this->item->get('user_id') == User::get('id')) {
    $this->item->set('vote', null);
}

if ($vote = $this->item->get('vote')) {
    switch ($vote) {
        case 1:
        case 'yes':
        case 'positive':
        case 'like':
            $lcls = ' chosen';
            break;

        case -1:
        case 'no':
        case 'negative':
        case 'dislike':
            $dcls = ' chosen';
            break;
    }
} else {
    $this->item->set('vote', null);
}

if (!User::isGuest()) {
    $like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP', $this->item->get('helpful', 0));
    $dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN', $this->item->get('nothelpful', 0));
    $cls = ' tooltips';
} else {
    $like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP_LOGIN');
    $dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN_LOGIN');
    $cls = ' tooltips';
}

$likeBtnCls = ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls;
$dislikeBtnCls = ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls;
$resourceId = $this->item->get('resource_id');
$refId = $this->item->get('id');
$voteYesUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $resourceId
    . '&active=reviews&action=rateitem&refid=' . $refId
    . '&vote=yes'
);
$voteNoUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $resourceId
    . '&active=reviews&action=rateitem&refid=' . $refId
    . '&vote=no'
);
$helpfulCount = $this->item->get('helpful', 0);
$nothelpfulCount = $this->item->get('nothelpful', 0);
$likeTxt = Lang::txt(
    'PLG_RESOURCES_REVIEWS_VOTE_LIKE',
    $helpfulCount
);
$dislikeTxt = Lang::txt(
    'PLG_RESOURCES_REVIEWS_VOTE_DISLIKE',
    $nothelpfulCount
);
?>
<?php if (!$this->item->get('vote')) { ?>
    <?php if (User::isGuest() || $this->item->get('user_id') == User::get('id')) { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo $likeBtnCls; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $likeTxt; ?>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo $dislikeBtnCls; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $dislikeTxt; ?>
            </span>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <a class="vote-button <?php echo $likeBtnCls; ?>"
                href="<?php echo $voteYesUrl; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $likeTxt; ?>
            </a>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <a class="vote-button <?php echo $dislikeBtnCls; ?>"
                href="<?php echo $voteNoUrl; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $dislikeTxt; ?>
            </a>
        </span>
    <?php } ?>
<?php } else { ?>
    <?php if (trim($lcls) == 'chosen') { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo $likeBtnCls; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $likeTxt; ?>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <a class="vote-button <?php echo $dislikeBtnCls; ?>"
                href="<?php echo $voteNoUrl; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $dislikeTxt; ?>
            </a>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <a class="vote-button <?php echo $likeBtnCls; ?>"
                href="<?php echo $voteYesUrl; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $likeTxt; ?>
            </a>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo $dislikeBtnCls; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $dislikeTxt; ?>
            </span>
        </span>
    <?php } ?>
<?php }