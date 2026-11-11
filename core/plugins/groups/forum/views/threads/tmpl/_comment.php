<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Forum\Models\Attachment;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     category: mixed,
 *     cls: mixed,
 *     comment: mixed,
 *     config: mixed,
 *     depth: mixed,
 *     filters: mixed,
 *     group: mixed,
 *     like: mixed,
 *     likes: mixed,
 *     option: mixed,
 *     thread: mixed,
 * } $this
 */

    $this
        ->css('like.css')
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

    // One record per liker, "name#id", records separated by "/". A display
    // name may contain BOTH delimiters: Sanitize::cleanProperName(), which
    // the profile and admin saves run, strips only ':', '<', '>', '&' and
    // control characters, and the profiles API sets `name` straight from
    // Request::getString() with no filtering at all. So the name is
    // percent-encoded here and decoded at every reader (the loop below and
    // assets/js/like.js), which keeps the format unambiguous whatever the
    // name holds instead of resting on what some validator rejects today.
    $userNameLikesArray .= "/" . rawurlencode((string) $likeObj->userName) . "#" . (int) $likeObj->userId;
}

    $userNameLikesArray = substr($userNameLikesArray, 1);

    $this->comment->set('section', $this->filters['section']);
    $this->comment->set('category', $this->category->get('alias'));

    $this->config->set('access-edit-post', false);
if (User::get('id') == $this->comment->get('created_by')) {
    $this->config->set('access-edit-post', true);
}

    $name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
    if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
    }
}

    $cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->comment->isReported()) {
    $comment = '<p class="warning">' . Lang::txt('PLG_GROUPS_FORUM_COMMENT_REPORTED') . '</p>';
} else {
    $comment = $this->comment->comment;
}
?>
    <li class="comment <?= $cls ?><?= !$this->comment->get('parent') ? ' start' : '' ?>"
        id="c<?= $this->comment->get('id') ?>">
        <p class="comment-member-photo">
            <img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
        </p>
        <div class="comment-content">
            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <a class="permalink" href="<?= Route::url($this->comment->link('anchor')) ?>"
                    title="<?= Lang::txt('PLG_GROUPS_FORUM_PERMALINK') ?>">
                    <span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_FORUM_AT'); ?></span>
                    <span class="time"><time
                        datetime="<?= $this->comment->created() ?>"><?= $this->comment->created('time') ?></time></span>
                    <span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_FORUM_ON'); ?></span>
                    <span class="date"><time
                        datetime="<?= $this->comment->created() ?>"><?= $this->comment->created('date') ?></time></span>
                    <?php if ($this->comment->wasModified()) { ?>
                        &mdash; <?php echo Lang::txt('PLG_GROUPS_FORUM_EDITED'); ?>
                        <span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_FORUM_AT'); ?></span>
                        <?php
                        $datetime = $this->comment->modified();
                        ?>
                        <span class="time"><time
                            datetime="<?= $datetime ?>"><?= $this->comment->modified('time') ?></time></span>
                        <span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_FORUM_ON'); ?></span>
                        <?php
                        $datetime = $this->comment->modified();
                        ?>
                        <span class="date"><time
                            datetime="<?= $datetime ?>"><?= $this->comment->modified('date') ?></time></span>
                    <?php } ?>
                </a>
            </p>
            <div class="comment-body">
                <?php echo $comment; ?>

                <!-- Heart and "Like" link Button -->
                <!-- Only show if people is logged in -->
                <?php if (!User::isGuest()) { ?>
                    <div class="elementInline likeContainer">
                        <a class="icon-heart like <?php echo $userLikesComment ? "userLiked" : ''; ?>" href="#"
                        data-thread="<?php echo $this->thread->get('id'); ?>"
                        data-post="<?php echo $this->comment->get('id'); ?>"
                        data-user="<?php echo User::get('id'); ?>"
                        data-user-name="<?php echo rawurlencode((string) User::get('name')); ?>"
                        data-likes-list="<?php echo $this->escape($userNameLikesArray); ?>"
                        data-count="<?php echo $countLike; ?>"
                        aria-label="<?php echo Lang::txt('PLG_GROUPS_FORUM_LIKE'); ?>"
                        ><span class="sr-only"><?php echo Lang::txt('PLG_GROUPS_FORUM_LIKE'); ?></span></a>
                        <span class="likesStat <?php echo $countLike == 0 ? "noLikes" : ''; ?>">
                            <?php echo ($countLike > 0) ? "View Likes (" . $countLike . ")" : "No Likes"; ?>
                        </span>
                    </div>

                    <div class="clear"></div>

                    <div class="whoLikedPost">
                        <?php if (strlen($userNameLikesArray) > 0) { ?>
                        <div class="names">
                            <?php
                            $nameArray = preg_split("#/#", $userNameLikesArray);
                            $links = [];

                            foreach ($nameArray as $nameString) {
                                // Records are "percentEncodedName#id": the name is encoded
                                // where the list is built above, so neither '#' nor '/' can
                                // appear in it and the split is unambiguous. Decode for
                                // display, then escape -- the decoded name is user-written.
                                $parts    = explode("#", $nameString);
                                $userId   = (int) array_pop($parts);
                                $userName = rawurldecode(implode("#", $parts));

                                if (!$userId) {
                                    continue;
                                }

                                $userProfileUrl = "/members/" . $userId . "/profile";

                                $links[] = '<a href="' . $userProfileUrl . '" target="_blank">'
                                    . $this->escape($userName) . '</a>';
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
                $attachments = $this->comment->attachments()->whereEquals('state', Attachment::STATE_PUBLISHED)->rows();
                foreach ($attachments as $attachment) {
                    if (!trim($attachment->get('description'))) {
                        $attachment->set('description', $attachment->get('filename'));
                    }

                    if ($attachment->exists()) {
                        $link = $attachment->link();
                        //$this->comment->link() . '/' . $attachment->get('post_id') . '/' .
                        //    $attachment->get('filename');

                        if ($attachment->isImage()) {
                            if ($attachment->width() > 400) {
                                $html = '<p><a href="' . Route::url($link) . '"><img src="' . Route::url($link)
                                    . '" alt="' . $this->escape($attachment->get('description'))
                                    . '" width="400" /></a></p>';
                            } else {
                                $html = '<p><img src="' . Route::url($link) . '" alt="'
                                    . $this->escape($attachment->get('description')) . '" /></p>';
                            }
                        } else {
                            $html  = '<a class="attachment ' . Filesystem::extension($attachment->get('filename'))
                                . '" href="' . Route::url($link) . '" title="'
                                . $this->escape($attachment->get('description')) . '">';
                            $html .= '<p class="attachment-description">'
                                . $this->escape($attachment->get('description')) . '</p>';
                            $html .= '<p class="attachment-meta">';
                            $html .= '<span class="attachment-size">'
                                . Hubzero\Utility\Number::formatBytes($attachment->size()) . '</span>';
                            $html .= '<span class="attachment-action">' . Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD')
                                . '</span>';
                            $html .= '</p>';
                            $html .= '</a>';
                        }
                    } else {
                        $html  = '<div class="attachment ' . Filesystem::extension($attachment->get('filename'))
                            . '" title="' . $this->escape($attachment->get('description')) . '">';
                        $html .= '<p class="attachment-description">' . $this->escape($attachment->get('description'))
                            . '</p>';
                        $html .= '<p class="attachment-meta">';
                        $html .= '<span class="attachment-size">' . $this->escape($attachment->get('filename'))
                            . '</span>';
                        $html .= '<span class="attachment-action">' . Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND')
                            . '</span>';
                        $html .= '</p>';
                        $html .= '</div>';
                    }

                    echo $html;
                }
                ?>
            </div><!-- / .comment-attachments -->
        <?php if ($this->group->published == 1) { ?>
            <div class="comment-options">
            <?php if (
            $this->config->get('access-manage-thread')
                 || $this->config->get('access-delete-thread')
                 || $this->config->get('access-edit-thread')
                 || $this->config->get('access-delete-post')
                 || $this->config->get('access-edit-post')
) { ?>
                <?php
                if (
                    $this->comment->get('parent')
                    && (
                        $this->config->get('access-delete-post') || $this->comment->get('created_by') == User::get('id')
                    )
                ) { ?>
                    <button type="submit" form="hz-post-action"
                        formaction="<?= Route::url($this->comment->link('delete')) ?>" class="icon-delete delete"
                        data-id="c<?= $this->comment->get('id') ?>"><?= Lang::txt('PLG_GROUPS_FORUM_DELETE') ?></button>
                <?php } ?>
                <?php
                if (
                    ($this->config->get('access-edit-thread') || $this->config->get('access-edit-post'))
                    && $this->comment->get('created_by') == User::get('id')
                ) { ?>
                    <a class="icon-edit edit" data-id="c<?= $this->comment->get('id') ?>"
                        href="<?= Route::url($this->comment->link('edit')) ?>"><!--
                        --><?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?><!--
                    --></a>
                <?php } ?>
            <?php } ?>

            <?php if (!$this->comment->isReported()) { ?>
                <?php
                if (
                    !$this->thread->get('closed')
                    && $this->config->get('threading') == 'tree'
                    && $this->depth < $this->config->get('threading_depth', 3)
                ) { ?>
                    <?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
                    <a class="icon-reply reply active" data-txt-active="<?= Lang::txt('JCANCEL') ?>"
                        data-txt-inactive="<?= Lang::txt('PLG_GROUPS_FORUM_REPLY') ?>"
                        href="<?= Route::url($this->comment->link()) ?>"
                        rel="comment-form<?= $this->comment->get('id') ?>"><!--
                    --><?php echo Lang::txt('JCANCEL'); ?><!--
                --></a>
                    <?php } else { ?>
                    <a class="icon-reply reply" data-txt-active="<?= Lang::txt('JCANCEL') ?>"
                        data-txt-inactive="<?= Lang::txt('PLG_GROUPS_FORUM_REPLY') ?>"
                        href="<?= Route::url($this->comment->link('reply')) ?>"
                        rel="comment-form<?= $this->comment->get('id') ?>"><!--
                    --><?php echo Lang::txt('PLG_GROUPS_FORUM_REPLY'); ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                <a class="icon-abuse abuse" href="<?= Route::url($this->comment->link('abuse')) ?>"
                    rel="comment-form<?= $this->comment->get('id') ?>"><!--
                    --><?php echo Lang::txt('PLG_GROUPS_FORUM_REPORT_ABUSE'); ?><!--
                --></a>
            <?php } ?>
            </div>

            <?php
            if (
                !$this->thread->get('closed')
                && $this->config->get('threading') == 'tree'
                && $this->depth < $this->config->get('threading_depth', 3)
            ) { ?>
                <div class="comment-add<?= Request::getInt('reply', 0) != $this->comment->get('id') ? ' hide' : '' ?>"
                    id="comment-form<?= $this->comment->get('id') ?>">
            <form id="cform<?= $this->comment->get('id') ?>" action="<?= Route::url($this->thread->link()) ?>"
                method="post" enctype="multipart/form-data">
                        <fieldset>
                            <?php
                            $spanText = Lang::txt(
                                'PLG_GROUPS_FORUM_REPLYING_TO',
                                (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS')),
                            );
                            ?>
                            <legend><span><?= $spanText ?></span></legend>

                            <input type="hidden" name="fields[id]" value="0" />
                            <input type="hidden" name="fields[state]" value="1" />
                            <input type="hidden" name="fields[access]" value="<?= $this->thread->get('access', 0) ?>" />
                            <input type="hidden" name="fields[scope]" value="<?= $this->thread->get('scope') ?>" />
                            <input type="hidden" name="fields[category_id]"
                                value="<?= $this->thread->get('category_id') ?>" />
                            <input type="hidden" name="fields[scope_id]"
                                value="<?= $this->thread->get('scope_id') ?>" />
                            <input type="hidden" name="fields[scope_sub_id]"
                                value="<?= $this->thread->get('scope_sub_id') ?>" />
                            <input type="hidden" name="fields[object_id]"
                                value="<?= $this->thread->get('object_id') ?>" />
                            <input type="hidden" name="fields[parent]" value="<?= $this->comment->get('id') ?>" />
                            <input type="hidden" name="fields[thread]" value="<?= $this->comment->get('thread') ?>" />
                            <input type="hidden" name="fields[created]" value="" />
                            <input type="hidden" name="fields[created_by]" value="<?php echo User::get('id'); ?>" />

                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="cn" value="<?= $this->escape($this->group->get('cn')) ?>" />
                            <input type="hidden" name="active" value="forum" />
                            <input type="hidden" name="action" value="savethread" />

                            <?php echo Html::input('token'); ?>

                            <div class="form-group">
                                <label for="field_<?php echo $this->comment->get('id'); ?>_comment">
                                    <span class="label-text"><?= Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS') ?></span>
                                    <?php
                                        // Original Editor
                                        echo $this->editor(
                                            'fields[comment]',
                                            '',
                                            35,
                                            4,
                                            'field_' . $this->comment->get('id') . '_comment',
                                            ['class' => 'minimal no-footer'],
                                        );
                                    ?>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="upload-label" for="comment-<?php echo $this->comment->get('id'); ?>-file">
                                    <span class="label-text"><?= Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE') ?>:</span>
                                    <input type="file" class="form-control-file" name="upload"
                                        id="comment-<?= $this->comment->get('id') ?>-file" />
                                </label>
                            </div>

                            <?php if ($this->config->get('allow_anonymous')) { ?>
                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label reply-anonymous-label"
                                            for="comment-<?= $this->comment->get('id') ?>-anonymous">
                                            <input class="option form-check-input" type="checkbox"
                                                name="fields[anonymous]"
                                                id="comment-<?= $this->comment->get('id') ?>-anonymous" value="1" />
                                            <?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php } ?>

                            <p class="submit">
                                <input type="submit" class="btn" value="<?= Lang::txt('PLG_GROUPS_FORUM_SUBMIT') ?>" />
                            </p>
                        </fieldset>
                    </form>
                </div><!-- / .addcomment -->
            <?php } ?>
        <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        if ($this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3)) {
            $this
                 ->view('_list')
                 ->set('option', $this->option)
                 ->set('group', $this->group)
                 ->set('comments', $this->comment->get('replies'))
                 ->set('thread', $this->thread)
                 ->set('likes', $this->likes)
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
