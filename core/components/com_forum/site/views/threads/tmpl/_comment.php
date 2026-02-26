<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

    $this->css('like.css')
        ->js('like.js');

    $likeArray = $this->like;
    $countLike = count($likeArray);
    $currentUserId = User::get('id');

    $userLikesComment = false;
    $userNameLikesArray = "";

foreach ($likeArray as $likeObj) {
    if ($currentUserId == $likeObj->userId) {
        $userLikesComment = true;
    }

    $userNameLikesArray .= "/" . ($likeObj->userName)
        . "#" . ($likeObj->userId);
}

    $userNameLikesArray = substr($userNameLikesArray, 1);

    $this->comment->set('section', $this->filters['section']);
    $this->comment->set('category', $this->category->get('alias'));

    $name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(
        stripslashes($this->comment->creator->get('name', $name))
    );
    $viewLevels = User::getAuthorisedViewLevels();
    if (in_array($this->comment->creator->get('access'), $viewLevels)) {
        $creatorUrl = Route::url($this->comment->creator->link());
        $name = '<a href="' . $creatorUrl . '">' . $name . '</a>';
    }
}

    $cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->comment->isReported()) {
    $comment = '<p class="warning">'
        . Lang::txt('COM_FORUM_CONTENT_FLAGGED') . '</p>';
} else {
    $comment = $this->comment->comment;
}
$commentId = $this->comment->get('id');
$anchorUrl = Route::url($this->comment->link('anchor'));
$permalinkTxt = Lang::txt('COM_FORUM_PERMALINK');
?>
    <li class="comment <?php echo $cls; ?><?php if (!$this->comment->get('parent')) {
        echo ' start';
                       } ?>" id="c<?php echo $commentId; ?>">
        <p class="comment-member-photo">
            <?php
            $picSrc = $this->comment->creator->picture(
                $this->comment->get('anonymous')
            );
            ?>
            <img src="<?php echo $picSrc; ?>" alt="" />
        </p>
        <div class="comment-content">
            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <a class="permalink"
                    href="<?php echo $anchorUrl; ?>"
                    title="<?php echo $permalinkTxt; ?>">
                    <span class="comment-date-at">
                        <?php echo Lang::txt('COM_FORUM_AT'); ?>
                    </span>
                    <span class="time">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('time'); ?>
                        </time>
                    </span>
                    <span class="comment-date-on">
                        <?php echo Lang::txt('COM_FORUM_ON'); ?>
                    </span>
                    <span class="date">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('date'); ?>
                        </time>
                    </span>
                    <?php if ($this->comment->wasModified()) { ?>
                        &mdash; <?php echo Lang::txt('COM_FORUM_EDITED'); ?>
                        <span class="comment-date-at">
                            <?php echo Lang::txt('COM_FORUM_AT'); ?>
                        </span>
                        <span class="time">
                            <time datetime="<?php echo $this->comment->modified(); ?>">
                                <?php echo $this->comment->modified('time'); ?>
                            </time>
                        </span>
                        <span class="comment-date-on">
                            <?php echo Lang::txt('COM_FORUM_ON'); ?>
                        </span>
                        <span class="date">
                            <time datetime="<?php echo $this->comment->modified(); ?>">
                                <?php echo $this->comment->modified('date'); ?>
                            </time>
                        </span>
                    <?php } ?>
                </a>
            </p>
            <div class="comment-body">
                <?php echo $comment; ?>

                <!-- Heart and "Like" link Button -->
                <!-- Only show if people is logged in -->
                <?php if (!User::isGuest()) { ?>
                    <div class="elementInline likeContainer">
                        <a class="icon-heart like <?php if ($userLikesComment) {
                            echo "userLiked";
                                                  } ?>" href="#"
                        data-thread="<?php echo $this->thread->get('id'); ?>"
                        data-post="<?php echo $commentId; ?>"
                        data-user="<?php echo User::get('id'); ?>"
                        data-user-name="<?php echo User::get('name'); ?>"
                        data-likes-list="<?php echo $userNameLikesArray; ?>"
                        data-count="<?php echo $countLike; ?>"
                        ></a>
                        <span class="likesStat <?php if ($countLike == 0) {
                            echo "noLikes";
                                               } ?>">
                            <?php
                            echo ($countLike > 0)
                                ? "View Likes (" . $countLike . ")"
                                : "No Likes";
                            ?>
                        </span>
                    </div>
                    <div class="clear"></div>

                    <div class="whoLikedPost">
                        <?php if (strlen($userNameLikesArray) > 0) { ?>
                            <div class="names">
                                <?php
                                    $nameArray = preg_split("#/#", $userNameLikesArray);
                                    $links = array();
                                foreach ($nameArray as $nameString) {
                                    $nameArray = explode("#", $nameString);
                                    $userName = $nameArray[0];
                                    $userId =  $nameArray[1];
                                    $userProfileUrl = "/members/$userId/profile";

                                    $links[] = "<a href=$userProfileUrl"
                                        . " target='_blank'>$userName</a>";
                                }
                                    echo join(", ", $links) . " liked this";
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

            <div class="comment-attachments">
                <?php
                $publishedState = \Components\Forum\Models\Attachment::STATE_PUBLISHED;
                $attachments = $this->comment->attachments()
                    ->whereEquals('state', $publishedState)
                    ->rows();
                foreach ($attachments as $attachment) {
                    if (!trim($attachment->get('description'))) {
                        $attachment->set(
                            'description',
                            $attachment->get('filename')
                        );
                    }

                    if ($attachment->exists()) {
                        $link = $attachment->link();
                        $linkUrl = Route::url($link);
                        $descEsc = $this->escape(
                            $attachment->get('description')
                        );

                        if ($attachment->isImage()) {
                            if ($attachment->width() > 400) {
                                $html = '<p><a href="' . $linkUrl
                                    . '" rel="external">'
                                    . '<img src="' . $linkUrl
                                    . '" alt="' . $descEsc
                                    . '" width="400" /></a></p>';
                            } else {
                                $html = '<p><img src="' . $linkUrl
                                    . '" alt="' . $descEsc . '" /></p>';
                            }
                        } else {
                            $ext = Filesystem::extension(
                                $attachment->get('filename')
                            );
                            $html  = '<a class="attachment ' . $ext . '"'
                                . ' href="' . $linkUrl . '"'
                                . ' title="' . $descEsc . '">';
                            $html .= '<p class="attachment-description">'
                                . $attachment->get('description') . '</p>';
                            $html .= '<p class="attachment-meta">';
                            $size = Hubzero\Utility\Number::formatBytes(
                                $attachment->size()
                            );
                            $html .= '<span class="attachment-size">'
                                . $size . '</span>';
                            $dlTxt = Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD');
                            $html .= '<span class="attachment-action">'
                                . $dlTxt . '</span>';
                            $html .= '</p>';
                            $html .= '</a>';
                        }
                    } else {
                        $ext = Filesystem::extension(
                            $attachment->get('filename')
                        );
                        $descEsc = $this->escape(
                            $attachment->get('description')
                        );
                        $html  = '<div class="attachment ' . $ext . '"'
                            . ' title="' . $descEsc . '">';
                        $html .= '<p class="attachment-description">'
                            . $attachment->get('description') . '</p>';
                        $html .= '<p class="attachment-meta">';
                        $html .= '<span class="attachment-size">'
                            . $attachment->get('filename') . '</span>';
                        $notFoundTxt = Lang::txt(
                            'JLIB_HTML_ERROR_FILE_NOT_FOUND'
                        );
                        $html .= '<span class="attachment-action">'
                            . $notFoundTxt . '</span>';
                        $html .= '</p>';
                        $html .= '</div>';
                    }

                    echo $html;
                }
                ?>
            </div><!-- / .comment-attachments -->
            <?php
            $canManage = $this->config->get('access-manage-thread');
            $isParent = !$this->comment->get('parent');
            $isReply = (bool) $this->comment->get('parent');
            $isCreator = $this->comment->get('created_by') == User::get('id');
            $canDeleteThread = $this->config->get('access-delete-thread');
            $canEditThread = $this->config->get('access-edit-thread');
            $canDeletePost = $this->config->get('access-delete-post');
            $canEditPost = $this->config->get('access-edit-post');
            $showOptions = $canManage
                || ($isParent && $isCreator && ($canDeleteThread || $canEditThread))
                || ($isReply && $isCreator && ($canDeletePost || $canEditPost));
            if ($showOptions) { ?>
                <p class="comment-options">
                    <?php
                    $canDelete = ($isParent && $canDeleteThread)
                        || ($isReply && $canDeletePost);
                    if ($canDelete) {
                        $confirmTxt = Lang::txt('COM_FORUM_CONFIRM_DELETE');
                        $deleteUrl = Route::url($this->comment->link('delete'));
                        ?>
                        <a class="icon-delete delete"
                            data-txt-confirm="<?php echo $confirmTxt; ?>"
                            data-id="c<?php echo $commentId; ?>"
                            href="<?php echo $deleteUrl; ?>"><!--
                            --><?php echo Lang::txt('JACTION_DELETE'); ?><!--
                        --></a>
                    <?php } ?>
                    <?php
                    $canEdit = ($isParent && $canEditThread)
                        || ($isReply && $canEditPost);
                    if ($canEdit) {
                        $editUrl = Route::url($this->comment->link('edit'));
                        ?>
                        <a class="icon-edit edit"
                            data-id="c<?php echo $commentId; ?>"
                            href="<?php echo $editUrl; ?>"><!--
                            --><?php echo Lang::txt('JACTION_EDIT'); ?><!--
                        --></a>
                    <?php } ?>
                    <?php if (!$this->comment->isReported()) { ?>
                        <?php
                        $threadingConfig = $this->config->get('threading');
                        $maxDepth = $this->config->get('threading_depth', 3);
                        $canReply = !$this->thread->get('closed')
                            && $threadingConfig == 'tree'
                            && $this->depth < $maxDepth;
                        if ($canReply) {
                            $replyId = Request::getInt('reply', 0);
                            $cancelTxt = Lang::txt('JCANCEL');
                            $replyTxt = Lang::txt('COM_FORUM_REPLY');
                            $relAttr = 'comment-form' . $commentId;
                            if ($replyId == $commentId) {
                                $replyUrl = Route::url($this->comment->link());
                                ?>
                            <a class="icon-reply reply active"
                                data-txt-active="<?php echo $cancelTxt; ?>"
                                data-txt-inactive="<?php echo $replyTxt; ?>"
                                href="<?php echo $replyUrl; ?>"
                                rel="<?php echo $relAttr; ?>"><!--
                                --><?php echo $cancelTxt; ?><!--
                            --></a>
                            <?php } else {
                                $replyUrl = Route::url(
                                    $this->comment->link('reply')
                                );
                                ?>
                            <a class="icon-reply reply"
                                data-txt-active="<?php echo $cancelTxt; ?>"
                                data-txt-inactive="<?php echo $replyTxt; ?>"
                                href="<?php echo $replyUrl; ?>"
                                rel="<?php echo $relAttr; ?>"><!--
                                --><?php echo $replyTxt; ?><!--
                            --></a>
                            <?php } ?>
                        <?php } ?>
                        <?php
                        $flaggedTxt = Lang::txt('COM_FORUM_CONTENT_FLAGGED');
                        $abuseUrl = Route::url($this->comment->link('abuse'));
                        $reportTxt = Lang::txt('COM_FORUM_REPORT_ABUSE');
                        ?>
                        <a class="icon-abuse abuse"
                            data-txt-flagged="<?php echo $flaggedTxt; ?>"
                            href="<?php echo $abuseUrl; ?>"><!--
                            --><?php echo $reportTxt; ?><!--
                        --></a>
                    <?php } ?>
                </p>
            <?php } ?>

            <?php
            $threadingConfig = $this->config->get('threading');
            $maxDepth = $this->config->get('threading_depth', 3);
            $canReplyHere = !User::isGuest()
                && !$this->thread->get('closed')
                && $threadingConfig == 'tree'
                && $this->depth < $maxDepth;
            if ($canReplyHere) {
                $replyId = Request::getInt('reply', 0);
                $hideClass = ($replyId != $commentId) ? ' hide' : '';
                $threadUrl = Route::url($this->thread->link());
                ?>
                <div class="addcomment comment-add<?php echo $hideClass; ?>"
                    id="comment-form<?php echo $commentId; ?>">
                    <form id="cform<?php echo $commentId; ?>"
                        action="<?php echo $threadUrl; ?>"
                        method="post"
                        enctype="multipart/form-data">
                        <fieldset>
                            <?php
                            $replyToName = !$this->comment->get('anonymous')
                                ? $name
                                : Lang::txt('JANONYMOUS');
                            $legendTxt = Lang::txt(
                                'COM_FORUM_REPLYING_TO',
                                $replyToName
                            );
                            ?>
                            <legend>
                                <span><?php echo $legendTxt; ?></span>
                            </legend>

                            <input type="hidden" name="fields[id]" value="0" />
                            <input type="hidden" name="fields[state]" value="1" />
                            <?php $threadAccess = $this->thread->get('access', 0); ?>
                            <input type="hidden"
                                name="fields[access]"
                                value="<?php echo $threadAccess; ?>" />
                            <?php $threadScope = $this->thread->get('scope'); ?>
                            <input type="hidden"
                                name="fields[scope]"
                                value="<?php echo $threadScope; ?>" />
                            <?php $threadCatId = $this->thread->get('category_id'); ?>
                            <input type="hidden"
                                name="fields[category_id]"
                                value="<?php echo $threadCatId; ?>" />
                            <?php $threadScopeId = $this->thread->get('scope_id'); ?>
                            <input type="hidden"
                                name="fields[scope_id]"
                                value="<?php echo $threadScopeId; ?>" />
                            <?php $threadScopeSubId = $this->thread->get('scope_sub_id'); ?>
                            <input type="hidden"
                                name="fields[scope_sub_id]"
                                value="<?php echo $threadScopeSubId; ?>" />
                            <?php $threadObjectId = $this->thread->get('object_id'); ?>
                            <input type="hidden"
                                name="fields[object_id]"
                                value="<?php echo $threadObjectId; ?>" />
                            <input type="hidden"
                                name="fields[parent]"
                                value="<?php echo $commentId; ?>" />
                            <?php $commentThread = $this->comment->get('thread'); ?>
                            <input type="hidden"
                                name="fields[thread]"
                                value="<?php echo $commentThread; ?>" />
                            <input type="hidden"
                                name="fields[created]" value="" />
                            <input type="hidden"
                                name="fields[created_by]"
                                value="<?php echo User::get('id'); ?>" />

                            <input type="hidden"
                                name="option"
                                value="<?php echo $this->option; ?>" />
                            <input type="hidden"
                                name="controller"
                                value="<?php echo $this->controller; ?>" />
                            <input type="hidden" name="task" value="save" />

                            <?php echo Html::input('token'); ?>

                            <div class="form-group">
                                <?php
                                $fieldId = 'field_' . $commentId . '_comment';
                                $commentsTxt = Lang::txt('COM_FORUM_FIELD_COMMENTS');
                                ?>
                                <label for="<?php echo $fieldId; ?>">
                                    <span class="label-text">
                                        <?php echo $commentsTxt; ?>
                                    </span>
                                    <?php
                                    echo $this->editor(
                                        'fields[comment]',
                                        '',
                                        35,
                                        4,
                                        $fieldId,
                                        array('class' => 'form-control minimal no-footer')
                                    );
                                    ?>
                                </label>
                            </div>

                            <div class="form-group">
                                <?php
                                $fileFieldId = 'comment-' . $commentId . '-file';
                                $attachTxt = Lang::txt('COM_FORUM_ATTACH_FILE');
                                ?>
                                <label class="upload-label"
                                    for="<?php echo $fileFieldId; ?>">
                                    <span class="label-text">
                                        <?php echo $attachTxt; ?>:
                                    </span>
                                    <input type="file"
                                        class="form-control-file"
                                        name="upload"
                                        id="<?php echo $fileFieldId; ?>" />
                                </label>
                            </div>

                            <?php if ($this->config->get('allow_anonymous')) { ?>
                                <div class="form-group">
                                    <div class="form-check">
                                        <?php
                                        $anonFieldId = 'comment-' . $commentId . '-anonymous';
                                        ?>
                                        <label class="form-check-label reply-anonymous-label"
                                            for="<?php echo $anonFieldId; ?>">
                                            <input class="option form-check-input"
                                                type="checkbox"
                                                name="fields[anonymous]"
                                                id="<?php echo $anonFieldId; ?>"
                                                value="1" />
                                            <?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } ?>

                            <p class="submit">
                                <input type="submit"
                                    class="btn"
                                    value="<?php echo Lang::txt('JSUBMIT'); ?>" />
                            </p>
                        </fieldset>
                    </form>
                </div><!-- / .addcomment -->
            <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        $threadingConfig = $this->config->get('threading');
        $maxDepth = $this->config->get('threading_depth', 3);
        if ($threadingConfig == 'tree' && $this->depth < $maxDepth) {
            $this->view('_list')
                 ->set('option', $this->option)
                 ->set('controller', $this->controller)
                 ->set('comments', $this->comment->get('replies'))
                 ->set('thread', $this->thread)
                 ->set('parent', $this->comment->get('id'))
                 ->set('config', $this->config)
                 ->set('depth', $this->depth)
                 ->set('cls', $cls)
                 ->set('filters', $this->filters)
                 ->set('category', $this->category)
                 ->display();
        }
        ?>
    </li>
