<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

    $cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->wish->get('proposed_by') == $this->comment->get('created_by')) {
    $cls .= ' author';
}

    $name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
    if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $creatorLink = Route::url($this->comment->creator->link());
        $name = '<a href="' . $creatorLink . '">' . $name . '</a>';
    }
}

if ($this->comment->isReported()) {
    $cls .= ' abusive';
    $comment = '<p class="warning">' . Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
} else {
    $comment = $this->comment->content;
}

    $this->comment->set('listcategory', $this->wishlist->get('category'));
    $this->comment->set('listreference', $this->wishlist->get('referenceid'));

$permalinkUrl = Route::url($this->wish->link() . '#c' . $this->comment->get('id'));
$permalinkTxt = Lang::txt('COM_WISHLIST_PERMALINK');
?>
    <li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
        <p class="comment-member-photo">
            <img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
        </p>
        <div class="comment-content">
            <p class="comment-title">
                <strong><?php echo $name; ?></strong>
                <a class="permalink"
                    href="<?php echo $permalinkUrl; ?>"
                    title="<?php echo $permalinkTxt; ?>">
                    <span class="comment-date-at"><?php echo Lang::txt('COM_WISHLIST_AT'); ?></span>
                    <span class="time">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('time'); ?>
                        </time>
                    </span>
                    <span class="comment-date-on"><?php echo Lang::txt('COM_WISHLIST_ON'); ?></span>
                    <span class="date">
                        <time datetime="<?php echo $this->comment->created(); ?>">
                            <?php echo $this->comment->created('date'); ?>
                        </time>
                    </span>
                </a>
            </p>

            <div class="comment-body">
                <?php echo $comment; ?>
            </div>

            <?php if ($this->comment->attachments->count() > 0) { ?>
                <div class="comment-attachments">
                    <?php
                    foreach ($this->comment->attachments as $attachment) {
                        if (!trim($attachment->get('description'))) {
                            $attachment->set('description', $attachment->get('filename'));
                        }

                        $link = $attachment->link('download');

                        if ($attachment->exists()) {
                            if ($attachment->isImage()) {
                                $attachUrl = Route::url($link);
                                $attachAlt = $this->escape($attachment->get('description'));
                                if ($attachment->width() > 400) {
                                    $html = '<p><a href="' . $attachUrl . '">'
                                        . '<img src="' . $attachUrl . '"'
                                        . ' alt="' . $attachAlt . '" width="400" /></a></p>';
                                } else {
                                    $html = '<p><img src="' . $attachUrl . '"'
                                        . ' alt="' . $attachAlt . '" /></p>';
                                }
                            } else {
                                $attachUrl  = Route::url($link);
                                $attachDesc = $this->escape($attachment->get('description'));
                                $attachExt  = Filesystem::extension($attachment->get('filename'));
                                $attachSize = Hubzero\Utility\Number::formatBytes($attachment->size());
                                $clickTxt   = Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD');
                                $html  = '<a class="attachment ' . $attachExt . '"'
                                    . ' href="' . $attachUrl . '"'
                                    . ' title="' . $attachDesc . '">';
                                $html .= '<p class="attachment-description">' . $attachDesc . '</p>';
                                $html .= '<p class="attachment-meta">';
                                $html .= '<span class="attachment-size">' . $attachSize . '</span>';
                                $html .= '<span class="attachment-action">' . $clickTxt . '</span>';
                                $html .= '</p>';
                                $html .= '</a>';
                            }
                        } else {
                            $attachDesc    = $this->escape($attachment->get('description'));
                            $attachExt     = Filesystem::extension($attachment->get('filename'));
                            $attachName    = $this->escape($attachment->get('filename'));
                            $notFoundTxt   = Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND');
                            $html  = '<div class="attachment ' . $attachExt . '"'
                                . ' title="' . $attachDesc . '">';
                            $html .= '<p class="attachment-description">' . $attachDesc . '</p>';
                            $html .= '<p class="attachment-meta">';
                            $html .= '<span class="attachment-size">' . $attachName . '</span>';
                            $html .= '<span class="attachment-action">' . $notFoundTxt . '</span>';
                            $html .= '</p>';
                            $html .= '</div>';
                        }

                        echo $html;
                    }
                    ?>
                </div><!-- / .comment-attachments -->
            <?php } ?>

            <p class="comment-options">
            <?php /*
            if ($this->config->get('access-edit-thread')) {
                // || User::get('id') == $this->comment->get('created_by')
                if ($this->config->get('access-delete-thread')) {
                    $delUrl = Route::url(
                        $this->wish->link()
                        . '&action=delete&comment=' . $this->comment->get('id')
                    );
                    ?>
                    <a class="icon-delete delete" href="<?php echo $delUrl; ?>"><!--
                        --><?php echo Lang::txt('COM_WISHLIST_DELETE'); ?><!--
                    --></a>
                    <?php
                }
                if ($this->config->get('access-edit-thread')) {
                    $editUrl = Route::url(
                        $this->wish->link()
                        . '&action=edit&comment=' . $this->comment->get('id')
                    );
                    ?>
                    <a class="icon-edit edit" href="<?php echo $editUrl; ?>"><!--
                        --><?php echo Lang::txt('COM_WISHLIST_EDIT'); ?><!--
                    --></a>
                    <?php
                }
            }
            */ ?>
            <?php if (!$this->comment->isReported()) { ?>
                <?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
                    <?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
                        <?php
                        $replyUrl   = Route::url($this->comment->link());
                        $cancelTxt  = Lang::txt('JCANCEL');
                        $replyTxt   = Lang::txt('COM_WISHLIST_REPLY');
                        $formDataId = 'comment-form' . $this->comment->get('id');
                        ?>
                    <a class="icon-reply reply active"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $replyUrl; ?>"
                        data-rel="<?php echo $formDataId; ?>"><!--
                    --><?php echo $cancelTxt; ?><!--
                --></a>
                    <?php } else { ?>
                        <?php
                        $replyUrl   = Route::url($this->comment->link('reply'));
                        $cancelTxt  = Lang::txt('JCANCEL');
                        $replyTxt   = Lang::txt('COM_WISHLIST_REPLY');
                        $formDataId = 'comment-form' . $this->comment->get('id');
                        ?>
                    <a class="icon-reply reply"
                        data-txt-active="<?php echo $cancelTxt; ?>"
                        data-txt-inactive="<?php echo $replyTxt; ?>"
                        href="<?php echo $replyUrl; ?>"
                        data-rel="<?php echo $formDataId; ?>"><!--
                    --><?php echo $replyTxt; ?><!--
                --></a>
                    <?php } ?>
                <?php } ?>
                    <?php
                    $abuseUrl = Route::url($this->comment->link('report'));
                    $abuseTxt = Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE');
                    $reportTxt = Lang::txt('COM_WISHLIST_REPORT_ABUSE');
                    ?>
                    <a class="icon-abuse abuse"
                        data-txt-flagged="<?php echo $abuseTxt; ?>"
                        href="<?php echo $abuseUrl; ?>"><!--
                    --><?php echo $reportTxt; ?><!--
                --></a>
            <?php } ?>
            </p>

        <?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
            <?php
            $hideClass = (Request::getInt('reply', 0) != $this->comment->get('id')) ? ' hide' : '';
            $formId    = 'comment-form' . $this->comment->get('id');
            $anonName  = !$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS');
            $replyLegend = Lang::txt('COM_WISHLIST_REPLYING_TO', $anonName);
            ?>
            <div class="addcomment comment-add<?php echo $hideClass; ?>"
                id="<?php echo $formId; ?>">
                <?php if (User::isGuest()) { ?>
                <p class="warning">
                    <?php
                    $loginLink = '<a href="'
                        . Route::url(
                            'index.php?option=com_users&view=login&return='
                            . base64_encode(Route::url($this->wish->link(), false, true))
                        ) . '">' . Lang::txt('COM_WISHLIST_LOGIN') . '</a>';
                    echo Lang::txt('COM_WISHLIST_PLEASE_LOGIN_TO_COMMENT', $loginLink);
                    ?>
                </p>
                <?php } else { ?>
                <form id="cform<?php echo $this->comment->get('id'); ?>"
                    action="<?php echo Route::url($this->wish->link()); ?>"
                    method="post"
                    enctype="multipart/form-data">
                    <fieldset>
                        <legend><span><?php echo $replyLegend; ?></span></legend>

                        <input type="hidden"
                            name="comment[item_type]"
                            value="<?php echo $this->comment->get('item_type') ?>" />
                        <input type="hidden"
                            name="comment[item_id]"
                            value="<?php echo $this->comment->get('item_id'); ?>" />
                        <input type="hidden"
                            name="comment[parent]"
                            value="<?php echo $this->comment->get('id'); ?>" />

                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden"
                            name="listid"
                            value="<?php echo $this->escape($this->wish->get('wishlist')); ?>" />
                        <input type="hidden"
                            name="wishid"
                            value="<?php echo $this->escape($this->wish->get('id')); ?>" />
                        <input type="hidden" name="task" value="savereply" />
                        <input type="hidden"
                            name="referenceid"
                            value="<?php echo $this->wishlist->get('referenceid'); ?>" />
                        <input type="hidden" name="cat" value="wish" />

                        <?php echo Html::input('token'); ?>

                        <div class="form-group">
                            <label for="comment_<?php echo $this->comment->get('id'); ?>_content">
                                <span class="label-text"><?php echo Lang::txt('COM_WISHLIST_ENTER_COMMENTS'); ?></span>
                                <?php
                                $editorId = 'comment_' . $this->comment->get('id') . '_content';
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

                        <div class="form-group form-check">
                            <?php $anonId = 'comment-' . $this->comment->get('id') . '-anonymous'; ?>
                            <label class="comment-anonymous-label form-check-label"
                                for="<?php echo $anonId; ?>">
                                <input class="option form-check-input"
                                    type="checkbox"
                                    name="comment[anonymous]"
                                    id="<?php echo $anonId; ?>"
                                    value="1" />
                                <?php echo Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
                            </label>
                        </div>

                        <p class="submit">
                            <input type="submit"
                                class="btn"
                                value="<?php echo Lang::txt('COM_WISHLIST_SUBMIT'); ?>" />
                        </p>
                    </fieldset>
                </form>
                <?php } ?>
            </div><!-- / .addcomment -->
        <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        if ($this->depth < $this->wish->config()->get('comments_depth', 3)) {
            $comments = $this->comment->replies()
                ->whereIn('state', array(
                    \Components\Wishlist\Models\Comment::STATE_PUBLISHED,
                    \Components\Wishlist\Models\Comment::STATE_FLAGGED
                ))
                ->rows();

            $this->view('_list')
                 ->set('parent', $this->comment->get('id'))
                 ->set('cls', $cls)
                 ->set('depth', $this->depth)
                 ->set('option', $this->option)
                 ->set('comments', $comments)
                 ->set('wishlist', $this->wishlist)
                 ->set('wish', $this->wish)
                 ->display();
        }
        ?>
    </li>
