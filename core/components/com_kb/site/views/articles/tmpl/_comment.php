<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->article->get('created_by') == $this->comment->get('created_by')) {
    $cls .= ' author';
}
$cls .= ($this->comment->isReported()) ? ' abusive' : '';
if ($this->comment->get('state') == 1) {
    $cls .= ' chosen';
}

$name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(
        stripslashes($this->comment->creator->get('name', $name))
    );
    $creatorAccess = $this->comment->creator->get('access');
    if (in_array($creatorAccess, User::getAuthorisedViewLevels())) {
        $creatorUrl = Route::url($this->comment->creator->link());
        $name = '<a href="' . $creatorUrl . '">' . $name . '</a>';
    }
}

if ($this->comment->isReported()) {
    $comment = '<p class="warning">'
        . Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE')
        . '</p>';
} else {
    $comment  = $this->comment->content;
}

$permalinkUrl = Route::url(
    $this->base . '#c' . $this->comment->get('id')
);
$permalinkTitle = Lang::txt('COM_KB_PERMALINK');
$atTxt = Lang::txt('COM_KB_DATETIME_AT');
$onTxt = Lang::txt('COM_KB_DATETIME_ON');
$commentCreated = $this->comment->created();
$commentTime = $this->comment->created('time');
$commentDate = $this->comment->created('date');
$commentId = $this->comment->get('id');
$commentEntryId = $this->comment->get('entry_id');
$commentsDepth = $this->article->param('comments_depth', 3);
?>
    <li class="comment <?php echo $cls; ?>" id="c<?php echo $commentId; ?>">
        <?php
        $picSrc = $this->comment->creator->picture(
            $this->comment->get('anonymous')
        );
        ?>
        <p class="comment-member-photo">
            <img src="<?php echo $picSrc; ?>" alt="" />
        </p>
        <div class="comment-content">
        <?php if (!$this->comment->isReported() && $commentEntryId) { ?>
            <p class="comment-voting voting"
                id="answers_<?php echo $commentId; ?>">
                <?php
                $view = $this->view('_vote')
                             ->set('option', $this->option)
                             ->set('item', $this->comment)
                             ->set('type', 'comment')
                             ->set('vote', '')
                             ->set('id', '');
                if (!User::isGuest()) {
                    $view->set('vote', $this->comment->get('vote'));
                    if ($this->comment->get('created_by') == User::get('id')) {
                        $view->set('id', $commentId);
                    }
                }
                $view->display();
                ?>
            </p><!-- / .comment-voting -->
        <?php } ?>

            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <a class="permalink"
                    href="<?php echo $permalinkUrl; ?>"
                    title="<?php echo $permalinkTitle; ?>">
                    <span class="comment-date-at">
                        <?php echo $atTxt; ?>
                    </span>
                    <span class="time">
                        <time datetime="<?php echo $commentCreated; ?>">
                            <?php echo $commentTime; ?>
                        </time>
                    </span>
                    <span class="comment-date-on">
                        <?php echo $onTxt; ?>
                    </span>
                    <span class="date">
                        <time datetime="<?php echo $commentCreated; ?>">
                            <?php echo $commentDate; ?>
                        </time>
                    </span>
                </a>
            </p>

            <div class="comment-body">
                <?php echo $comment; ?>
            </div>

            <p class="comment-options">
            <?php /*if ($this->config->get('access-edit-thread')) {
                // || User::get('id') == $this->comment->created_by
                ?>
                <?php if ($this->config->get('access-delete-thread')) {
                    $delUrl = Route::url(
                        $this->base . '&action=delete&comment=' . $commentId
                    );
                    ?>
                    <a class="icon-delete delete"
                        href="<?php echo $delUrl; ?>"><!--
                        --><?php echo Lang::txt('COM_KB_DELETE'); ?><!--
                    --></a>
                <?php } ?>
                <?php if ($this->config->get('access-edit-thread')) {
                    $editUrl = Route::url(
                        $this->base . '&action=edit&comment=' . $commentId
                    );
                    ?>
                    <a class="icon-edit edit"
                        href="<?php echo $editUrl; ?>"><!--
                        --><?php echo Lang::txt('COM_KB_EDIT'); ?><!--
                    --></a>
                <?php } ?>
            <?php }*/ ?>
            <?php if (!$this->comment->get('reports')) { ?>
                <?php if ($this->depth < $commentsDepth && $this->article->commentsOpen()) { ?>
                    <?php
                    $cancelTxt = Lang::txt('JCANCEL');
                    $replyTxt = Lang::txt('COM_KB_REPLY');
                    $commentLink = Route::url(
                        $this->comment->link()
                    );
                    $commentReplyLink = Route::url(
                        $this->comment->link('reply')
                    );
                    $dataRel = 'comment-form' . $commentId;
                    ?>
                    <?php if (Request::getInt('reply', 0) == $commentId) { ?>
                    <a class="icon-reply reply active"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $commentLink; ?>"
                        data-rel="<?php echo $dataRel; ?>"><!--
                    --><?php echo $cancelTxt; ?><!--
                --></a>
                    <?php } else { ?>
                    <a class="icon-reply reply"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $commentReplyLink; ?>"
                        data-rel="<?php echo $dataRel; ?>"><!--
                    --><?php echo $replyTxt; ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                    <?php
                    $reportUrl = Route::url(
                        $this->comment->link('report')
                    );
                    $flaggedTxt = Lang::txt(
                        'COM_KB_COMMENT_REPORTED_AS_ABUSIVE'
                    );
                    ?>
                    <a class="icon-abuse abuse"
                        data-txt-flagged="<?php echo $flaggedTxt; ?>"
                        href="<?php echo $reportUrl; ?>"><!--
                    --><?php echo Lang::txt('COM_KB_REPORT_ABUSE'); ?><!--
                --></a>
            <?php } ?>
            </p>

        <?php if ($this->depth < $commentsDepth && $this->article->commentsOpen()) { ?>
            <?php
            $replyMatch = Request::getInt('reply', 0) != $commentId;
            $hideClass = $replyMatch ? ' hide' : '';
            $formId = 'comment-form' . $commentId;
            ?>
            <div class="addcomment comment-add<?php echo $hideClass; ?>"
                id="<?php echo $formId; ?>">
                <?php if (User::isGuest()) { ?>
                <p class="warning">
                    <?php
                    $returnUrl = base64_encode(
                        Route::url($this->base, false, true)
                    );
                    $loginUrl = Route::url(
                        'index.php?option=com_users&view=login&return='
                        . $returnUrl
                    );
                    echo Lang::txt('COM_KB_MUST_LOG_IN', $loginUrl);
                    ?>
                </p>
                <?php } else { ?>
                    <?php $cformId = 'cform' . $commentId; ?>
                <form id="<?php echo $cformId; ?>"
                    action="<?php echo Route::url($this->base); ?>"
                    method="post" enctype="multipart/form-data">
                    <fieldset>
                        <?php
                        $anonName = !$this->comment->get('anonymous')
                            ? $name
                            : Lang::txt('JANONYMOUS');
                        $replyingTxt = Lang::txt(
                            'COM_KB_REPLYING_TO',
                            $anonName
                        );
                        ?>
                        <legend>
                            <span><?php echo $replyingTxt; ?></span>
                        </legend>

                        <input type="hidden" name="comment[id]"
                            value="0" />
                        <input type="hidden" name="comment[parent]"
                            value="<?php echo $this->escape($commentId); ?>" />
                        <input type="hidden" name="comment[entry_id]"
                            value="<?php echo $this->escape($commentEntryId); ?>" />
                        <input type="hidden" name="comment[created]"
                            value="" />
                        <input type="hidden" name="comment[created_by]"
                            value="<?php echo $this->escape(User::get('id')); ?>" />
                        <input type="hidden" name="comment[state]"
                            value="1" />

                        <input type="hidden" name="option"
                            value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="controller"
                            value="articles" />
                        <?php $salias = $this->article->get('salias'); ?>
                        <input type="hidden" name="section"
                            value="<?php echo $this->escape($salias); ?>" />
                        <?php $calias = $this->article->get('calias'); ?>
                        <input type="hidden" name="category"
                            value="<?php echo $this->escape($calias); ?>" />
                        <?php $alias = $this->article->get('alias'); ?>
                        <input type="hidden" name="alias"
                            value="<?php echo $this->escape($alias); ?>" />
                        <input type="hidden" name="task"
                            value="savecomment" />

                        <?php echo Html::input('token'); ?>

                        <?php
                        $editorId = 'comment_' . $commentId . '_content';
                        $enterTxt = Lang::txt('COM_KB_ENTER_COMMENTS');
                        ?>
                        <label for="<?php echo $editorId; ?>">
                            <span class="label-text">
                                <?php echo $enterTxt; ?>
                            </span>
                            <?php
                            echo $this->editor(
                                'comment[content]',
                                '',
                                35,
                                4,
                                $editorId,
                                array('class' => 'minimal no-footer')
                            );
                            ?>
                        </label>

                        <?php
                        $anonId = 'comment_' . $commentId . '_anonymous';
                        ?>
                        <label class="comment-anonymous-label"
                            for="<?php echo $anonId; ?>">
                            <input class="option" type="checkbox"
                                name="comment[anonymous]"
                                id="<?php echo $anonId; ?>"
                                value="1" />
                            <?php echo Lang::txt('COM_KB_FIELD_ANONYMOUS'); ?>
                        </label>

                        <p class="submit">
                            <input type="submit"
                                value="<?php echo Lang::txt('COM_KB_SUBMIT'); ?>" />
                        </p>
                    </fieldset>
                </form>
                <?php } ?>
            </div><!-- / .addcomment -->
        <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        if ($this->depth < $commentsDepth) {
            $comments = $this->comment->replies()
                ->whereIn('state', array(
                    \Components\Kb\Models\Comment::STATE_PUBLISHED,
                    \Components\Kb\Models\Comment::STATE_FLAGGED
                ))
                ->rows();

            $this->view('_list')
                 ->set('parent', $commentId)
                 ->set('cls', $cls)
                 ->set('depth', $this->depth)
                 ->set('option', $this->option)
                 ->set('article', $this->article)
                 ->set('comments', $comments)
                 ->set('base', $this->base)
                 ->display();
        }
        ?>
    </li>
