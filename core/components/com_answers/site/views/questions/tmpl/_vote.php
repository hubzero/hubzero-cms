<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{item: mixed, vote: mixed} $this
 */

$dcls = '';
$lcls = '';

if ($ballot = $this->vote->get('vote', null)) {
    switch ($ballot) {
        case 1:
            $lcls = ' chosen';
            break;

        case -1:
            $dcls = ' chosen';
            break;
    }
}

if ($pos = $this->item->get('positive')) {
    $this->item->set('helpful', $pos);
}
if ($neg = $this->item->get('negative')) {
    $this->item->set('nothelpful', $neg);
}

if (!User::isGuest()) {
    $like_title    = Lang::txt('COM_ANSWERS_VOTE_LIKE_TITLE', $this->item->get('helpful', 0));
    $dislike_title = Lang::txt('COM_ANSWERS_VOTE_DISLIKE_TITLE', $this->item->get('nothelpful', 0));
    $cls = ' tooltips';
} else {
    $like_title    = Lang::txt('COM_ANSWERS_VOTE_LIKE_LOGIN');
    $dislike_title = Lang::txt('COM_ANSWERS_VOTE_DISLIKE_LOGIN');
    $cls = ' tooltips';
}

$voteUrlBase = 'index.php?option=' . $this->option . '&task=vote&category=' . $this->vote->get('item_type') . '&id='
    . $this->vote->get('item_id');
?>
<?php if (!$this->vote->get('id')) { ?>
    <?php if (User::isGuest() || User::get('id') == $this->item->get('created_by')) { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
            echo $cls; ?>" title="<?php echo $like_title; ?>">
                <?= $this->item->get('helpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_LIKE') ?></span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
            echo $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?= $this->item->get('nothelpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_DISLIKE') ?></span>
            </span>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <?php
            $actionUrl = Route::url($voteUrlBase . '&vote=yes');
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>"
                class="vote-button <?= ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral' ?><?= $cls ?>"
                title="<?php echo $like_title; ?>">
                <?= $this->item->get('helpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_LIKE') ?></span>
            </button>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <?php
            $actionUrl = Route::url($voteUrlBase . '&vote=no');
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>"
                class="vote-button <?= ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral' ?><?= $cls ?>"
                title="<?php echo $dislike_title; ?>">
                <?= $this->item->get('nothelpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_DISLIKE') ?></span>
            </button>
        </span>
    <?php } ?>
<?php } else { ?>
    <?php if (trim($lcls) == 'chosen') { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
            echo $cls; ?>" title="<?php echo $like_title; ?>">
                <?= $this->item->get('helpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_LIKE') ?></span>
            </span>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <?php
            $actionUrl = Route::url($voteUrlBase . '&vote=no');
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>"
                class="vote-button <?= ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral' ?><?= $cls ?>"
                title="<?php echo $dislike_title; ?>">
                <?= $this->item->get('nothelpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_DISLIKE') ?></span>
            </button>
        </span>
    <?php } else { ?>
        <span class="vote-like<?php echo $lcls; ?>">
            <?php
            $actionUrl = Route::url($voteUrlBase . '&vote=yes');
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>"
                class="vote-button <?= ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral' ?><?= $cls ?>"
                title="<?php echo $like_title; ?>">
                <?= $this->item->get('helpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_LIKE') ?></span>
            </button>
        </span>
        <span class="vote-dislike<?php echo $dcls; ?>">
            <span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
            echo $cls; ?>" title="<?php echo $dislike_title; ?>">
                <?= $this->item->get('nothelpful', 0) ?><span> <?= Lang::txt('COM_ANSWERS_VOTE_DISLIKE') ?></span>
            </span>
        </span>
    <?php } ?>
<?php }
