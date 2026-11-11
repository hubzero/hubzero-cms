<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{item: mixed, url: mixed} $this
 */

$dcls = '';
$lcls = '';
$this->set('url', preg_replace('/\#[^\#]*$/', '', $this->url));
if (!strstr($this->url, '?')) {
    $this->set('url', $this->url . ('?'));
} else {
    $this->set('url', $this->url . ('&'));
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

if (!$no_html) { ?>
<div class="comment-voting voting">
<?php } ?>
    <span class="vote-like<?php echo $lcls; ?>">
        <?php
        // || !$this->params->get('access-vote-comment')) {
        if ($this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { ?>
            <span class="vote-button <?= ($this->item->get('positive', 0) > 0 ? 'like' : 'neutral') . $cls ?>"
                title="<?= $like_title ?>">
                <?= $this->item->get('positive', 0) ?><span> <?= Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_LIKE') ?></span>
            </span>
        <?php } else { ?>
            <button type="submit" form="hz-post-action"
                formaction="<?= Route::url($this->url . 'action=commentvote&voteup=' . $this->item->get('id')) ?>"
                class="vote-button <?= ($this->item->get('positive', 0) > 0 ? 'like' : 'neutral') . $cls ?>"
                title="<?= $like_title ?>">
                <?= $this->item->get('positive', 0) ?><span> <?= Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_LIKE') ?></span>
            </button>
        <?php } ?>
    </span>
    <span class="vote-dislike<?php echo $dcls; ?>">
        <?php if ($this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { ?>
            <span class="vote-button <?= ($this->item->get('negative', 0) > 0 ? 'dislike' : 'neutral') . $cls ?>"
                title="<?= $dislike_title ?>">
                <?php
                $spanText = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE');
                ?>
                <?= $this->item->get('negative', 0) ?><span> <?= $spanText ?></span>
            </span>
        <?php } else { ?>
            <button type="submit" form="hz-post-action"
                formaction="<?= Route::url($this->url . 'action=commentvote&votedown=' . $this->item->get('id')) ?>"
                class="vote-button <?= ($this->item->get('negative', 0) > 0 ? 'dislike' : 'neutral') . $cls ?>"
                title="<?= $dislike_title ?>">
                <?php
                $spanText = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE');
                ?>
                <?= $this->item->get('negative', 0) ?><span> <?= $spanText ?></span>
            </button>
        <?php } ?>
    </span>
<?php if (!$no_html) { ?>
</div>
<?php }
