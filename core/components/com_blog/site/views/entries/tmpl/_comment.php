<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
    $comment = '<p class="warning">' . Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
} else {
    $comment  = $this->comment->content();
}
?>
    <li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
        <p class="comment-member-photo">
            <img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
        </p>
        <div class="comment-content">
            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <?php
                $permalinkUrl = Route::url(
                    $this->base . '#c' . $this->comment->get('id')
                );
                $createdDatetime = $this->comment->get('created');
                ?>
                <a class="permalink"
                    href="<?php echo $permalinkUrl; ?>"
                    title="<?php echo Lang::txt('COM_BLOG_PERMALINK'); ?>"
                >
                    <span class="comment-date-at">
                        <?php echo Lang::txt('COM_BLOG_AT'); ?>
                    </span>
                    <span class="time">
                        <time datetime="<?php echo $createdDatetime; ?>">
                            <?php echo $this->comment->created('time'); ?>
                        </time>
                    </span>
                    <span class="comment-date-on">
                        <?php echo Lang::txt('COM_BLOG_ON'); ?>
                    </span>
                    <span class="date">
                        <time datetime="<?php echo $createdDatetime; ?>">
                            <?php echo $this->comment->created('date'); ?>
                        </time>
                    </span>
                    <?php if ($this->comment->wasModified()) { ?>
                        <?php $modifiedDatetime = $this->comment->modified(); ?>
                        &mdash; <?php echo Lang::txt('COM_BLOG_EDITED'); ?>
                        <span class="comment-date-at">
                            <?php echo Lang::txt('COM_BLOG_AT'); ?>
                        </span>
                        <span class="time">
                            <time datetime="<?php echo $modifiedDatetime; ?>">
                                <?php echo $this->comment->modified('time'); ?>
                            </time>
                        </span>
                        <span class="comment-date-on">
                            <?php echo Lang::txt('COM_BLOG_ON'); ?>
                        </span>
                        <span class="date">
                            <time datetime="<?php echo $modifiedDatetime; ?>">
                                <?php echo $this->comment->modified('date'); ?>
                            </time>
                        </span>
                    <?php } ?>
                </a>
            </p>

        <?php
        $canEditComment = $this->config->get('access-edit-comment')
            || User::get('id') == $this->comment->get('created_by');
        if (
            Request::getWord('action') == 'editcomment'
            && Request::getInt('comment') == $this->comment->get('id')
            && $canEditComment
        ) {
            ?>
            <form id="cform<?php echo $this->comment->get('id'); ?>"
                class="comment-edit"
                action="<?php echo Route::url($this->base); ?>"
                method="post"
                enctype="multipart/form-data"
            >
                <fieldset>
                    <legend><span><?php echo Lang::txt('COM_BLOG_COMMENT_EDIT'); ?></span></legend>

                    <input type="hidden"
                        name="comment[id]"
                        value="<?php echo $this->comment->get('id'); ?>"
                    />
                    <input type="hidden"
                        name="comment[entry_id]"
                        value="<?php echo $this->comment->get('entry_id'); ?>"
                    />
                    <input type="hidden"
                        name="comment[parent]"
                        value="<?php echo $this->comment->get('parent'); ?>"
                    />
                    <input type="hidden"
                        name="comment[created]"
                        value="<?php echo $this->comment->get('created'); ?>"
                    />
                    <input type="hidden"
                        name="comment[created_by]"
                        value="<?php echo $this->comment->get('created_by'); ?>"
                    />
                    <input type="hidden" name="comment[state]" value="1" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="task" value="savecomment" />

                    <?php echo Html::input('token'); ?>

                    <div class="form-group">
                        <label for="comment_<?php echo $this->comment->get('id'); ?>_content">
                            <span class="label-text"><?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?></span>
                            <?php
                            $editorId = 'comment_' . $this->comment->get('id') . '_content';
                            echo $this->editor(
                                'comment[content]',
                                $this->comment->get('content'),
                                35,
                                4,
                                $editorId,
                                array('class' => 'form-control minimal no-footer')
                            );
                            ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <?php
                            $anonId = 'comment_' . $this->comment->get('id') . '_anonymous';
                            $anonChecked = $this->comment->get('anonymous')
                                ? ' checked="checked"'
                                : '';
                            ?>
                            <label class="form-check-label comment-anonymous"
                                for="<?php echo $anonId; ?>"
                            >
                                <input class="option form-check-input"
                                    type="checkbox"
                                    name="comment[anonymous]"
                                    id="<?php echo $anonId; ?>"
                                    value="1"
                                    <?php echo $anonChecked; ?>
                                />
                                <?php echo Lang::txt('COM_BLOG_POST_ANONYMOUS'); ?>
                            </label>
                        </div>
                    </div>

                    <p class="submit">
                        <input type="submit" class="btn" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
                    </p>
                </fieldset>
            </form>
        <?php } else { ?>
            <div class="comment-body">
                <?php echo $comment; ?>
            </div>

            <?php
            $commentId = $this->comment->get('id');
            $deleteUrl = Route::url(
                $this->base . '&action=deletecomment&comment=' . $commentId
            );
            $editUrl = Route::url(
                $this->base . '&action=editcomment&comment=' . $commentId
            );
            $replyUrl = Route::url(
                $this->base . '&reply=' . $commentId
            );
            $cancelReplyUrl = Route::url($this->base);
            $abuseUrl = Route::url(
                'index.php?option=com_support&task=reportabuse'
                . '&category=blogcomment&id=' . $commentId
                . '&parent=' . $this->comment->get('entry_id')
            );
            $cancelTxt = Lang::txt('JCANCEL');
            $replyTxt = Lang::txt('COM_BLOG_REPLY');
            $formRel = 'comment-form' . $commentId;
            ?>
            <p class="comment-options">
            <?php if ($this->config->get('access-delete-comment')) { ?>
                <a class="icon-delete delete"
                    data-confirm="<?php echo Lang::txt('COM_BLOG_CONFIRM_DELETE'); ?>"
                    href="<?php echo $deleteUrl; ?>"
                ><!--
                    --><?php echo Lang::txt('JACTION_DELETE'); ?><!--
                --></a>
            <?php } ?>
            <?php if (!$this->comment->isReported()) { ?>
                <?php if ($canEditComment) { ?>
                    <a class="icon-edit edit"
                        href="<?php echo $editUrl; ?>"
                    ><!--
                        --><?php echo Lang::txt('JACTION_EDIT'); ?><!--
                    --></a>
                <?php } ?>
                <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                    <?php if (Request::getInt('reply', 0) == $commentId) { ?>
                    <a class="icon-reply reply active"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $cancelReplyUrl; ?>"
                        rel="<?php echo $formRel; ?>"
                    ><!--
                    --><?php echo $cancelTxt; ?><!--
                --></a>
                    <?php } else { ?>
                    <a class="icon-reply reply"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $replyUrl; ?>"
                        rel="<?php echo $formRel; ?>"
                    ><!--
                    --><?php echo $replyTxt; ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                <a class="icon-abuse abuse"
                    data-txt-flagged="<?php echo Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE'); ?>"
                    href="<?php echo $abuseUrl; ?>"
                ><!--
                    --><?php echo Lang::txt('COM_BLOG_REPORT_ABUSE'); ?><!--
                --></a>
            <?php } ?>
            </p>

            <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                <?php
                $hideClass = (Request::getInt('reply', 0) != $commentId)
                ? ' hide'
                : '';
                $replyToName = !$this->comment->get('anonymous')
                ? $name
                : Lang::txt('JANONYMOUS');
                $replyEditorId = 'comment_' . $commentId . '_content';
                ?>
            <div class="addcomment comment-add<?php echo $hideClass; ?>"
                id="<?php echo $formRel; ?>"
            >
                <form id="cform<?php echo $commentId; ?>"
                    action="<?php echo $cancelReplyUrl; ?>"
                    method="post"
                    enctype="multipart/form-data"
                >
                    <fieldset>
                        <legend>
                            <span>
                                <?php echo Lang::txt('COM_BLOG_REPLYING_TO', $replyToName); ?>
                            </span>
                        </legend>

                        <input type="hidden" name="comment[id]" value="0" />
                        <input type="hidden"
                            name="comment[entry_id]"
                            value="<?php echo $this->comment->get('entry_id'); ?>"
                        />
                        <input type="hidden"
                            name="comment[parent]"
                            value="<?php echo $commentId; ?>"
                        />
                        <input type="hidden" name="comment[created]" value="" />
                        <input type="hidden"
                            name="comment[created_by]"
                            value="<?php echo User::get('id'); ?>"
                        />
                        <input type="hidden" name="comment[state]" value="1" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="task" value="savecomment" />

                        <?php echo Html::input('token'); ?>

                        <div class="form-group">
                            <label for="<?php echo $replyEditorId; ?>">
                                <span class="label-text">
                                    <?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?>
                                </span>
                                <?php
                                echo $this->editor(
                                    'comment[content]',
                                    '',
                                    35,
                                    4,
                                    $replyEditorId,
                                    array('class' => 'form-control minimal no-footer')
                                );
                                ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <label class="form-check-label"
                                    id="comment-anonymous-label"
                                    for="comment-anonymous"
                                >
                                    <input class="option form-check-input"
                                        type="checkbox"
                                        name="comment[anonymous]"
                                        id="comment-anonymous"
                                        value="1"
                                    />
                                    <?php echo Lang::txt('COM_BLOG_POST_ANONYMOUS'); ?>
                                </label>
                            </div>
                        </div>

                        <p class="submit">
                            <input type="submit" class="btn" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
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
                ->whereIn('state', array(
                    \Components\Blog\Models\Comment::STATE_PUBLISHED,
                    \Components\Blog\Models\Comment::STATE_FLAGGED
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
                ->display();
        }
        ?>
    </li>