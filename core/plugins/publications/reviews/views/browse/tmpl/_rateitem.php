<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$dcls = '';
$lcls = '';

if ($vote = $this->item->get('vote')) {
    switch ($vote) {
        case 'yes':
        case 'positive':
        case 'like':
            $lcls = ' chosen';
            break;

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
    $like_title = 'Vote this up :: ' . $this->item->get('helpful', 0) . ' people liked this';
    $dislike_title = 'Vote this down :: ' . $this->item->get('nothelpful', 0) . ' people did not like this';
    $cls = ' tooltips';
} else {
    $like_title = 'Vote this up :: Please login to vote.';
    $dislike_title = 'Vote this down :: Please login to vote.';
    $cls = ' tooltips';
}

$likeBtnCls = ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls;
$dislikeBtnCls = ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls;
$pubId = $this->item->get('publication_id');
$refId = $this->item->get('id');
$voteYesUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $pubId
    . '&active=reviews&action=rateitem&refid=' . $refId
    . '&vote=yes'
);
$voteNoUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $pubId
    . '&active=reviews&action=rateitem&refid=' . $refId
    . '&vote=no'
);
$helpfulCount = $this->item->get('helpful', 0);
$nothelpfulCount = $this->item->get('nothelpful', 0);
?>
<?php if (!$this->item->get('vote')) { ?>
    <?php if (User::isGuest()) { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo $likeBtnCls; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $helpfulCount; ?><span> Like</span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo $dislikeBtnCls; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $nothelpfulCount; ?><span> Dislike</span>
            </span>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <a class="vote-button <?php echo $likeBtnCls; ?>"
                href="<?php echo $voteYesUrl; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $helpfulCount; ?><span> Like</span>
            </a>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <a class="vote-button <?php echo $dislikeBtnCls; ?>"
                href="<?php echo $voteNoUrl; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $nothelpfulCount; ?><span> Dislike</span>
            </a>
        </span>
    <?php } ?>
<?php } else { ?>
    <?php if (trim($lcls) == 'chosen') { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo $likeBtnCls; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $helpfulCount; ?><span> Like</span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <a class="vote-button <?php echo $dislikeBtnCls; ?>"
                href="<?php echo $voteNoUrl; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $nothelpfulCount; ?><span> Dislike</span>
            </a>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <a class="vote-button <?php echo $likeBtnCls; ?>"
                href="<?php echo $voteYesUrl; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $helpfulCount; ?><span> Like</span>
            </a>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo $dislikeBtnCls; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $nothelpfulCount; ?><span> Dislike</span>
            </span>
        </span>
    <?php } ?>
<?php }
