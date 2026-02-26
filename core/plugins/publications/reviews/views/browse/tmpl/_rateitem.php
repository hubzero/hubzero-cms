<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';

// A publication page requested without a version redirects to the last
// public release and drops the query string, which lost the vote; name the
// version being viewed so the vote link lands without a redirect
$verq = Request::getInt('v', 0) ? '&v=' . Request::getInt('v', 0) : '';

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

$vote_link = 'index.php?option=' . $this->option . '&id=' . $this->item->get('publication_id') . $verq . '&active=reviews&action=rateitem&refid=' . $this->item->get('id');
?>
<?php if (!$this->item->get('vote')) { ?>
    <?php if (User::isGuest()) { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') .  $cls; ?>" title="<?php echo $like_title; ?>">
                <?php echo $this->item->get('helpful', 0); ?><span> Like</span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') .  $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
            </span>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($vote_link . '&vote=yes'); ?>" class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
                <?php echo $this->item->get('helpful', 0); ?><span> Like</span>
            </button>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($vote_link . '&vote=no'); ?>" class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
            </button>
        </span>
    <?php } ?>
<?php } else { ?>
    <?php if (trim($lcls) == 'chosen') { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
                <?php echo $this->item->get('helpful', 0); ?><span> Like</span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($vote_link . '&vote=no'); ?>" class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
            </button>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($vote_link . '&vote=yes'); ?>" class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
                <?php echo $this->item->get('helpful', 0); ?><span> Like</span>
            </button>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
            </span>
        </span>
    <?php } ?>
<?php }
