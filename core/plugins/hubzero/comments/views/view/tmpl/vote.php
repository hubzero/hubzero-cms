<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';
$this->url = preg_replace('/\#[^\#]*$/', '', $this->url);
if (!strstr($this->url, '?')) {
    $this->url .= '?';
} else {
    $this->url .= '&';
}

if ($vote = $this->item->get('vote')) {
    switch ($vote) {
        case 'yes':
        case 'positive':
        case 'up':
        case 'like':
        case '1':
        case '+':
            $lcls = ' chosen';
            break;

        case 'no':
        case 'negative':
        case 'down':
        case 'dislike':
        case '-1':
        case '-':
            $dcls = ' chosen';
            break;
    }
} else {
    $this->item->set('vote', null);
}

if (!User::isGuest()) {
    $like_title    = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_UP', $this->item->get('positive', 0));
    $dislike_title = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DOWN', $this->item->get('negative', 0));
    $cls = ' tooltips';
} else {
    $like_title    = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_UP_LOGIN');
    $dislike_title = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DOWN_LOGIN');
    $cls = ' tooltips';
}

$no_html = Request::getInt('no_html', 0);

$hasVoted    = $this->item->get('vote');
$isCreator   = User::get('id') == $this->item->get('created_by');
$cannotVote  = $hasVoted || $isCreator;
$positiveCount = $this->item->get('positive', 0);
$negativeCount = $this->item->get('negative', 0);
$likeCls     = ($positiveCount > 0 ? 'like' : 'neutral') . $cls;
$dislikeCls  = ($negativeCount > 0 ? 'dislike' : 'neutral') . $cls;
$likeLabel   = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_LIKE');
$dislikeLabel = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE');
$voteUpUrl   = Route::url(
    $this->url . 'action=commentvote&voteup='
    . $this->item->get('id')
);
$voteDownUrl = Route::url(
    $this->url . 'action=commentvote&votedown='
    . $this->item->get('id')
);

if (!$no_html) { ?>
<p class="comment-voting voting">
<?php } ?>
    <span class="vote-like<?php echo $lcls; ?>">
        <?php if ($cannotVote) { ?>
            <span class="vote-button <?php echo $likeCls; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $positiveCount; ?>
                <span> <?php echo $likeLabel; ?></span>
            </span>
        <?php } else { ?>
            <a class="vote-button <?php echo $likeCls; ?>"
                href="<?php echo $voteUpUrl; ?>"
                title="<?php echo $like_title; ?>">
                <?php echo $positiveCount; ?>
                <span> <?php echo $likeLabel; ?></span>
            </a>
        <?php } ?>
    </span>
    <span class="vote-dislike<?php echo $dcls; ?>">
        <?php if ($cannotVote) { ?>
            <span class="vote-button <?php echo $dislikeCls; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $negativeCount; ?>
                <span> <?php echo $dislikeLabel; ?></span>
            </span>
        <?php } else { ?>
            <a class="vote-button <?php echo $dislikeCls; ?>"
                href="<?php echo $voteDownUrl; ?>"
                title="<?php echo $dislike_title; ?>">
                <?php echo $negativeCount; ?>
                <span> <?php echo $dislikeLabel; ?></span>
            </a>
        <?php } ?>
    </span>
<?php if (!$no_html) { ?>
</p>
<?php }
