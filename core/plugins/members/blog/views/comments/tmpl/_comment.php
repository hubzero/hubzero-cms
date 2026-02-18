<?php

// @phpcs:disable PSR1.Files.SideEffects


/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';

$name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
    if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
    }
}

if ($this->comment->isReported()) {
    $comment = '<p class="warning">' . Lang::txt('PLG_MEMBERS_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
} else {
    $comment  = $this->comment->content;
}

$commentId = $this->comment->get('id');
$createdDatetime = $this->comment->created();
$createdTime = $this->comment->created('time');
$createdDate = $this->comment->created('date');
$modifiedDatetime = $this->comment->modified();
$modifiedTime = $this->comment->modified('time');
$modifiedDate = $this->comment->modified('date');

$deleteUrl = Route::url(
    $this->base . '&action=deletecomment&comment=' . $commentId
);
$editUrl = Route::url(
    $this->base . '&action=editcomment&comment=' . $commentId
);
$canEdit = $this->config->get('access-edit-comment')
    || User::get('id') == $this->comment->get('created_by');
$abuseUrl = Route::url(
    'index.php?option=com_support&task=reportabuse'
    . '&category=blog&id=' . $commentId
    . '&parent=' . $this->comment->get('entry_id')
);
$replyToName = !$this->comment->get('anonymous')
    ? $name
    : Lang::txt('JANONYMOUS');
$replyLegend = Lang::txt(
    'PLG_MEMBERS_BLOG_REPLYING_TO',
    $replyToName
);
?>
    <li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
        <p class="comment-member-photo">
            <a class="comment-anchor" name="c<?php echo $this->comment->get('id'); ?>"></a>
            <img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
        </p>
        <div class="comment-content">
            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <a class="permalink"
                    href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>"
                    title="<?php echo Lang::txt('PLG_MEMBERS_BLOG_PERMALINK'); ?>">
                    <span class="comment-date-at"><?php echo Lang::txt('PLG_MEMBERS_BLOG_AT'); ?></span>
                    <span class="time">
                        <time datetime="<?php echo $createdDatetime; ?>"><?php
                            echo $createdTime;
                        ?></time>
                    </span>
                    <span class="comment-date-on"><?php echo Lang::txt('PLG_MEMBERS_BLOG_ON'); ?></span>
                    <span class="date">
                        <time datetime="<?php echo $createdDatetime; ?>"><?php
                            echo $createdDate;
                        ?></time>
                    </span>
                    <?php if ($this->comment->wasModified()) { ?>
                        &mdash; <?php echo Lang::txt('PLG_MEMBERS_BLOG_EDITED'); ?>
                        <span class="comment-date-at"><?php echo Lang::txt('PLG_MEMBERS_BLOG_AT'); ?></span>
                        <span class="time">
                            <time datetime="<?php echo $modifiedDatetime; ?>"><?php
                                echo $modifiedTime;
                            ?></time>
                        </span>
                        <span class="comment-date-on"><?php echo Lang::txt('PLG_MEMBERS_BLOG_ON'); ?></span>
                        <span class="date">
                            <time datetime="<?php echo $modifiedDatetime; ?>"><?php
                                echo $modifiedDate;
                            ?></time>
                        </span>
                    <?php } ?>
                </a>
            </p>

    <?php if (
    Request::getWord('action') == 'editcomment'
            && Request::getInt('comment') == $this->comment->get('id')
            && ($this->config->get('access-edit-comment') || User::get('id') == $this->comment->get('created_by'))
) { ?>
            <form id="cform<?php echo $this->comment->get('id'); ?>"
                class="comment-edit"
                action="<?php echo Route::url($this->base); ?>"
                method="post"
                enctype="multipart/form-data">
                <fieldset>
                    <legend><span><?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENT_EDIT'); ?></span></legend>

                    <input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
                    <input type="hidden"
                        name="comment[entry_id]"
                        value="<?php echo $this->comment->get('entry_id'); ?>"/>
                    <input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
                    <input type="hidden"
                        name="comment[created]"
                        value="<?php echo $this->comment->get('created'); ?>"/>
                    <input type="hidden"
                        name="comment[created_by]"
                        value="<?php echo $this->comment->get('created_by'); ?>"/>
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
                    <input type="hidden" name="active" value="blog" />
                    <input type="hidden" name="task" value="view" />
                    <input type="hidden" name="action" value="savecomment" />

                    <?php echo Html::input('token'); ?>

                    <label for="comment_<?php echo $this->comment->get('id'); ?>_content">
                        <span class="label-text"><?php echo Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS'); ?></span>
                        <?php

                        echo $this->editor('comment[content]', $this->comment->get('content'), 35, 4, 'comment_' .
                        $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
                        ?>
                    </label>

                    <label class="comment-anonymous-label"
                        for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
                        <input class="option"
                            type="checkbox"
                            name="comment[anonymous]"
                            id="comment_<?php echo $this->comment->get('id'); ?>_anonymous"
                            value="1" <?php if ($this->comment->get('anonymous')) {
                                echo ' checked="checked"';
                                      } ?> />
                        <?php echo Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS'); ?>
                    </label>

                    <p class="submit">
                        <input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SUBMIT'); ?>" />
                    </p>
                </fieldset>
            </form>
    <?php } else { ?>
            <?php echo $comment; ?>

            <p class="comment-options">
            <?php if ($this->config->get('access-delete-comment')) { ?>
                <a class="icon-delete delete"
                    data-confirm="<?php echo Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE'); ?>"
                    href="<?php echo $deleteUrl; ?>"><!--
                    --><?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?><!--
                --></a>
            <?php } ?>
            <?php if (!$this->comment->isReported()) { ?>
                <?php if ($canEdit) { ?>
                    <a class="icon-edit edit"
                        href="<?php echo $editUrl; ?>"><!--
                        --><?php echo Lang::txt('PLG_MEMBERS_BLOG_EDIT'); ?><!--
                    --></a>
                <?php } ?>
                <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                    <?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
                    <a class="icon-reply reply active"
                        data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>"
                        data-txt-inactive="<?php echo Lang::txt('PLG_MEMBERS_BLOG_REPLY'); ?>"
                        href="<?php echo Route::url($this->base); ?>"
                        rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
                    --><?php echo Lang::txt('JCANCEL'); ?><!--
                --></a>
                    <?php } else { ?>
                    <a class="icon-reply reply"
                        data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>"
                        data-txt-inactive="<?php echo Lang::txt('PLG_MEMBERS_BLOG_REPLY'); ?>"
                        href="<?php echo Route::url($this->base . '&reply=' . $this->comment->get('id')); ?>"
                        rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
                    --><?php echo Lang::txt('PLG_MEMBERS_BLOG_REPLY'); ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                <a class="icon-abuse abuse"
                    href="<?php echo $abuseUrl; ?>"
                    rel="comment-form<?php echo $commentId; ?>"><!--
                    --><?php echo Lang::txt('PLG_MEMBERS_BLOG_REPORT_ABUSE'); ?><!--
                --></a>
            <?php } ?>
            </p>

        <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
            <div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) {
                echo ' hide';
                                              } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
                <form id="cform<?php echo $this->comment->get('id'); ?>"
                    action="<?php echo Route::url($this->base); ?>"
                    method="post"
                    enctype="multipart/form-data">
                    <fieldset id="commentform<?php echo $this->comment->get('id'); ?>">
                        <legend><span><?php echo $replyLegend; ?></span></legend>

                        <input type="hidden" name="comment[id]" value="0" />
                        <input type="hidden"
                            name="comment[entry_id]"
                            value="<?php echo $this->comment->get('entry_id'); ?>"/>
                        <input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
                        <input type="hidden" name="comment[created]" value="" />
                        <input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
                        <input type="hidden" name="comment[state]" value="1" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
                        <input type="hidden" name="active" value="blog" />
                        <input type="hidden" name="task" value="view" />
                        <input type="hidden" name="action" value="savecomment" />

                        <label for="comment_<?php echo $this->comment->get('id'); ?>_content">
                            <span class="label-text"><?php echo Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS'); ?></span>
                            <?php

                            echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') .
                            '_content', array('class' => 'minimal no-footer'));
                            ?>
                        </label>

                        <label class="comment-anonymous-label"
                            for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
                            <input class="option"
                                type="checkbox"
                                name="comment[anonymous]"
                                id="comment_<?php echo $this->comment->get('id'); ?>_anonymous"
                                value="1"/>
                            <?php echo Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS'); ?>
                        </label>

                        <?php echo Html::input('token'); ?>

                        <p class="submit">
                            <input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SUBMIT'); ?>" />
                        </p>
                    </fieldset>
                </form>
            </div><!-- / .addcomment -->
        <?php } ?>
    <?php } ?>
        </div><!-- / .comment-content -->
        <?php

        if ($this->depth < $this->config->get('comments_depth', 3)) {
            $replies = $this->comment->replies()
                ->including(['creator', function ($creator) {
                    $creator->select('*');
                }])
                ->whereIn('state', array(
                    Components\Blog\Models\Comment::STATE_PUBLISHED,
                    Components\Blog\Models\Comment::STATE_FLAGGED
                ))
                ->ordered()
                ->rows();

            $this->view('_list')
                 ->set('parent', $this->comment->get('id'))
                 ->set('option', $this->option)
                 ->set('comments', $replies)
                 ->set('config', $this->config)
                 ->set('depth', $this->depth)
                 ->set('cls', $cls)
                 ->set('base', $this->base)
                 ->set('member', $this->member)
                 ->display();
        }
        ?>
    </li>
