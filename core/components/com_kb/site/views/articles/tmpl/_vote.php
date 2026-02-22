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

$like_link = Route::url(
    $this->item->link('vote') . '&vote=like&'
    . Session::getFormToken() . '=1'
);
$dislike_link = Route::url(
    $this->item->link('vote') . '&vote=dislike&'
    . Session::getFormToken() . '=1'
);

if (isset($this->vote)) {
    switch ($this->vote) {
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
    $this->vote = null;
}

if (!User::isGuest()) {
    $like_title = Lang::txt(
        'COM_KB_VOTE_UP',
        $this->item->get('helpful', 0)
    );
    $dislike_title = Lang::txt(
        'COM_KB_VOTE_DOWN',
        $this->item->get('nothelpful', 0)
    );
    $cls = ' tooltips';
} else {
    $like_title    = Lang::txt('COM_KB_VOTE_UP_LOGIN');
    $dislike_title = Lang::txt('COM_KB_VOTE_DOWN_LOGIN');
    $cls = ' tooltips';
}

$helpfulCount = $this->item->get('helpful', 0);
$nothelpfulCount = $this->item->get('nothelpful', 0);
$likeTxt = Lang::txt('COM_KB_VOTE_LIKE');
$dislikeTxt = Lang::txt('COM_KB_VOTE_DISLIKE');
?>

<?php if ($this->id) : ?>
    <span class="vote-like">
        <span class="vote-button neutral disabled tooltips"
            title="<?php echo $like_title; ?>">
            <?php echo $helpfulCount; ?>
            <span> <?php echo $likeTxt; ?></span>
        </span>
    </span>
    <span class="vote-dislike">
        <span class="vote-button neutral disabled tooltips"
            title="<?php echo $dislike_title; ?>">
            <?php echo $nothelpfulCount; ?>
            <span> <?php echo $dislikeTxt; ?></span>
        </span>
    </span>
<?php else : ?>
    <?php if (!$this->vote) : ?>
        <?php if (User::isGuest()) : ?>
            <span class="vote-like<?php echo $lcls; ?>">
                <span class="vote-button like tooltips"
                    title="<?php echo $like_title; ?>">
                    <?php echo $helpfulCount; ?>
                    <span> <?php echo $likeTxt; ?></span>
                </span>
            </span>
            <span class="vote-dislike<?php echo $lcls; ?>">
                <?php $ddCls = 'vote-button dislike dislike-disabled tooltips'; ?>
                <span class="<?php echo $ddCls; ?>"
                    title="<?php echo $dislike_title; ?>">
                    <?php echo $nothelpfulCount; ?>
                    <span> <?php echo $dislikeTxt; ?></span>
                </span>
            </span>
        <?php else : ?>
            <span class="vote-like<?php echo $lcls; ?>">
                <a class="vote-button like tooltips"
                    href="<?php echo $like_link; ?>"
                    title="<?php echo $like_title; ?>">
                    <?php echo $helpfulCount; ?>
                    <span> <?php echo $likeTxt; ?></span>
                </a>
            </span>
            <span class="vote-dislike<?php echo $lcls; ?>">
                <a class="vote-button dislike tooltips"
                    href="<?php echo $dislike_link; ?>"
                    title="<?php echo $dislike_title; ?>">
                    <?php echo $nothelpfulCount; ?>
                    <span> <?php echo $dislikeTxt; ?></span>
                </a>
            </span>
        <?php endif; ?>
    <?php else : ?>
        <?php if (trim($lcls) == 'chosen') : ?>
            <?php
            $likeBtnCls = ($helpfulCount > 0) ? 'like' : 'neutral';
            $dislikeBtnCls = ($nothelpfulCount > 0)
                ? 'dislike' : 'neutral';
            ?>
            <span class="vote-like<?php echo $lcls; ?>">
                <span class="vote-button <?php echo $likeBtnCls; ?> tooltips"
                    title="<?php echo $like_title; ?>">
                    <?php echo $helpfulCount; ?>
                    <span> <?php echo $likeTxt; ?></span>
                </span>
            </span>
            <span class="vote-dislike<?php echo $dcls; ?>">
                <a class="vote-button <?php echo $dislikeBtnCls; ?> tooltips"
                    href="<?php echo $dislike_link; ?>"
                    title="<?php echo $dislike_title; ?>">
                    <?php echo $nothelpfulCount; ?>
                    <span> <?php echo $dislikeTxt; ?></span>
                </a>
            </span>
        <?php else : ?>
            <?php
            $likeBtnCls = ($helpfulCount > 0) ? 'like' : 'neutral';
            $dislikeBtnCls = ($nothelpfulCount > 0)
                ? 'dislike' : 'neutral';
            ?>
            <span class="vote-like<?php echo $lcls; ?>">
                <a class="vote-button <?php echo $likeBtnCls; ?> tooltips"
                    href="<?php echo $like_link; ?>"
                    title="<?php echo $like_title; ?>">
                    <?php echo $helpfulCount; ?>
                    <span> <?php echo $likeTxt; ?></span>
                </a>
            </span>
            <span class="vote-dislike<?php echo $dcls; ?>">
                <span class="vote-button <?php echo $dislikeBtnCls; ?> tooltips"
                    title="<?php echo $dislike_title; ?>">
                    <?php echo $nothelpfulCount; ?>
                    <span> <?php echo $dislikeTxt; ?></span>
                </span>
            </span>
        <?php endif; ?>
    <?php endif; ?>
<?php endif;
