<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';
if ($this->question->get('created_by') == $this->comment->get('created_by')) {
    $cls .= ' author';
}
$cls .= ($this->comment->isReported()) ? ' abusive' : '';
if ($this->comment->get('state') == 1) {
    $cls .= ' chosen';
}

if (!$this->comment->get('item_type')) {
    $this->comment->set('item_type', 'response');
}
if (!$this->comment->get('item_id')) {
    $this->comment->set(
        'item_id',
        ($this->depth == 1 ? $this->comment->get('id') : $this->item_id)
    );
}

$anchorPrefix = ($this->depth == 1 ? 'a' : 'c');
$commentId = $this->comment->get('id');
$formId = $this->depth
    . $this->comment->get('item_type')
    . $commentId;
?>
    <li
        class="comment <?php echo $cls; ?>"
        id="<?php echo $anchorPrefix . $commentId; ?>"
    >
        <p class="comment-member-photo">
            <img
                src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>"
                alt=""
            />
        </p>
        <div class="comment-content">
            <?php if (!$this->comment->isReported() && $this->comment->get('qid')) { ?>
                <p class="comment-voting voting" id="answers_<?php echo $commentId; ?>">
                    <?php
                    $this->view('_vote')
                         ->set('option', $this->option)
                         ->set('item', $this->comment)
                         ->set('vote', $this->comment->ballot())
                         ->display();
                    ?>
                </p><!-- / .comment-voting -->
            <?php } ?>

            <p class="comment-title">
                <strong>
                    <?php
                    $name = Lang::txt('JANONYMOUS');
                    if (!$this->comment->get('anonymous')) {
                        $name = $this->escape(
                            stripslashes(
                                $this->comment->creator->get('name', $name)
                            )
                        );
                        $viewLevels = User::getAuthorisedViewLevels();
                        if (
                            in_array($this->comment->creator->get('access'), $viewLevels)
                            && !$this->comment->creator->get('block')
                            && $this->comment->creator->get('approved')
                        ) {
                            $name = '<a href="'
                                . Route::url($this->comment->creator->link())
                                . '">' . $name . '</a>';
                        }
                    }
                    echo $name;
                    ?>
                </strong>
                <?php
                $permalinkUrl = Route::url(
                    $this->base . '#' . $anchorPrefix . $commentId
                );
                ?>
                <a
                    class="permalink"
                    href="<?php echo $permalinkUrl; ?>"
                    title="<?php echo Lang::txt('COM_ANSWERS_PERMALINK'); ?>"
                >
                    <span class="comment-date-at">
                        <?php echo Lang::txt('COM_ANSWERS_DATETIME_AT'); ?>
                    </span>
                    <span class="time">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('time'); ?>
                        </time>
                    </span>
                    <span class="comment-date-on">
                        <?php echo Lang::txt('COM_ANSWERS_ON'); ?>
                    </span>
                    <span class="date">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('date'); ?>
                        </time>
                    </span>
                </a>
            </p>

            <div class="comment-body">
                <?php
                $comment  = htmlspecialchars_decode($this->comment->content);
                $componentPath = Component::path('com_redirect');
                if ($componentPath) {
                    $comment = \Components\Redirect\Helpers\Converter::convert($comment);
                } else {
                    $comment = preg_replace(
                        '#<a\s[^>]*href="([^"]*)"[^>]*?>(.*?)</a>#is',
                        "<a href='$1' rel='nofollow'>$2</a>",
                        $comment
                    );
                }

                if ($this->comment->isReported()) {
                    $comment = '<p class="warning">'
                        . Lang::txt('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE')
                        . '</p>';
                }

                echo $comment;
                ?>
            </div>

            <p class="comment-options">
            <?php
            /*
            if ($this->config->get('access-edit-thread')) {
                // || User::get('id') == $this->comment->created_by
                if ($this->config->get('access-delete-thread')) {
                    $deleteUrl = Route::url(
                        $this->base . '&action=delete&comment='
                            . $commentId
                    );
                    echo '<a class="icon-delete delete" href="'
                        . $deleteUrl . '"><!--
                        -->' . Lang::txt('JACTION_DELETE') . '<!--
                    --></a>';
                }
                if ($this->config->get('access-edit-thread')) {
                    $editUrl = Route::url(
                        $this->base . '&action=edit&comment='
                            . $commentId
                    );
                    echo '<a class="icon-edit edit" href="'
                        . $editUrl . '"><!--
                        -->' . Lang::txt('JACTION_EDIT') . '<!--
                    --></a>';
                }
            }
            */
            ?>
            <?php if (!$this->comment->isReported()) { ?>
                <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                    <?php
                    $cancelTxt = Lang::txt('JCANCEL');
                    $replyTxt = Lang::txt('COM_ANSWERS_REPLY');
                    $dataRel = 'comment-form' . $formId;
                    if (Request::getInt('reply', 0) == $commentId) {
                        $replyUrl = Route::url($this->comment->link());
                        ?>
                    <a
                        class="icon-reply reply active"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $replyUrl; ?>"
                        data-rel="<?php echo $dataRel; ?>"
                    ><!--
                    --><?php echo $cancelTxt; ?><!--
                --></a>
                    <?php } else {
                        $replyUrl = Route::url($this->comment->link('reply'));
                        ?>
                    <a
                        class="icon-reply reply"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $replyUrl; ?>"
                        data-rel="<?php echo $dataRel; ?>"
                    ><!--
                    --><?php echo $replyTxt; ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                    <?php
                    $flaggedTxt = Lang::txt('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE');
                    $reportUrl = Route::url($this->comment->link('report'));
                    ?>
                    <a
                        class="icon-abuse abuse"
                        data-txt-flagged="<?php echo $flaggedTxt; ?>"
                        href="<?php echo $reportUrl; ?>"
                    ><!--
                    --><?php echo Lang::txt('COM_ANSWERS_REPORT_ABUSE'); ?><!--
                --></a>
                <?php
                $canAccept = User::get('id') == $this->question->get('created_by')
                    && $this->question->isOpen()
                    && $this->comment->get('qid')
                    && $this->depth <= 1;
                if ($canAccept) {
                    $acceptUrl = Route::url($this->comment->link('accept'));
                    ?>
                    <a
                        class="icon-success accept"
                        href="<?php echo $acceptUrl; ?>"
                    >
                        <?php echo Lang::txt('COM_ANSWERS_ACCEPT_ANSWER'); ?>
                    </a>
                <?php } ?>
            <?php } ?>
            </p>

        <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
            <?php
            $hideClass = (Request::getInt('reply', 0) != $commentId)
                ? ' hide'
                : '';
            ?>
            <div
                class="addcomment comment-add<?php echo $hideClass; ?>"
                id="comment-form<?php echo $formId; ?>"
            >
                <?php if (User::get('guest')) { ?>
                <p class="warning">
                    <?php
                    $returnUrl = base64_encode(
                        Route::url($this->base, false, true)
                    );
                    $loginUrl = Route::url(
                        'index.php?option=com_users&view=login&return='
                            . $returnUrl
                    );
                    $loginLink = '<a href="' . $loginUrl . '">'
                        . Lang::txt('COM_ANSWERS_LOGIN') . '</a>';
                    echo Lang::txt(
                        'COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER',
                        $loginLink
                    );
                    ?>
                </p>
                <?php } else { ?>
                <form
                    id="cform<?php echo $formId; ?>"
                    action="<?php echo Route::url($this->base); ?>"
                    method="post"
                    enctype="multipart/form-data"
                >
                    <fieldset>
                        <?php
                        $replyingTo = (!$this->comment->get('anonymous'))
                            ? $name
                            : Lang::txt('JANONYMOUS');
                        ?>
                        <legend>
                            <span>
                                <?php echo Lang::txt('COM_ANSWERS_REPLYING_TO', $replyingTo); ?>
                            </span>
                        </legend>

                        <input type="hidden" name="comment[id]" value="0" />
                        <input
                            type="hidden"
                            name="comment[item_type]"
                            value="<?php echo $this->comment->get('item_type', 'response'); ?>"
                        />
                        <input
                            type="hidden"
                            name="comment[item_id]"
                            value="<?php echo $this->comment->get('item_id'); ?>"
                        />
                        <?php
                        $parentVal = ($this->depth == 1)
                            ? 0
                            : $commentId;
                        ?>
                        <input
                            type="hidden"
                            name="comment[parent]"
                            value="<?php echo $parentVal; ?>"
                        />
                        <input type="hidden" name="comment[created]" value="" />
                        <input
                            type="hidden"
                            name="comment[created_by]"
                            value="<?php echo User::get('id'); ?>"
                        />
                        <input type="hidden" name="comment[state]" value="1" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="controller" value="questions" />
                        <input
                            type="hidden"
                            name="rid"
                            value="<?php echo $this->question->get('id'); ?>"
                        />
                        <input type="hidden" name="task" value="savereply" />

                        <?php echo Html::input('token'); ?>

                        <div class="form-group">
                            <?php $editorId = 'comment_' . $commentId . '_content'; ?>
                            <label for="<?php echo $editorId; ?>">
                                <span class="label-text">
                                    <?php echo Lang::txt('COM_ANSWERS_ENTER_COMMENTS'); ?>
                                </span>
                                <?php
                                echo $this->editor(
                                    'comment[content]',
                                    '',
                                    35,
                                    4,
                                    $editorId,
                                    array('class' => 'form-control minimal no-footer')
                                );
                                ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <?php
                            $anonId = 'comment_' . $commentId . '_anonymous';
                            ?>
                            <label
                                class="comment-anonymous-label"
                                for="<?php echo $anonId; ?>"
                            >
                                <input
                                    class="option form-check-input"
                                    type="checkbox"
                                    name="comment[anonymous]"
                                    id="<?php echo $anonId; ?>"
                                    value="1"
                                />
                                <?php echo Lang::txt('COM_ANSWERS_POST_COMMENT_ANONYMOUSLY'); ?>
                            </label>
                        </div>

                        <p class="submit">
                            <input
                                type="submit"
                                class="btn"
                                value="<?php echo Lang::txt('COM_ANSWERS_SUBMIT'); ?>"
                            />
                        </p>
                    </fieldset>
                </form>
                <?php } ?>
            </div><!-- / .addcomment -->
        <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        if ($this->depth < $this->config->get('comments_depth', 3)) {
            $this->view('_list')
                 ->set('item_id', $this->comment->get('item_id'))
                 ->set('parent', $this->comment->get('id'))
                 ->set('question', $this->question)
                 ->set('option', $this->option)
                 ->set('comments', $this->comment->replies()->where('state', '!=', 2)->rows())
                 ->set('config', $this->config)
                 ->set('depth', $this->depth)
                 ->set('cls', $cls)
                 ->set('base', $this->base)
                 ->display();
        }
        ?>
    </li>
