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

if ($this->comment->isReported()) {
    $this->comment->set('anonymous', 1);
    $comment = '<p class="warning">' . Lang::txt('PLG_COURSES_DISCUSSIONS_CONTENT_REPORTED') . '</p>';
} else {
    $comment = $this->comment->comment;
    if ($this->search) {
        $comment = preg_replace('#' . $this->search . '#i', "<span class=\"highlight\">\\0</span>", $comment);
    }
}

    $name = Lang::txt('JANONYMOUS');
if (!$this->comment->get('anonymous')) {
    $name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
    if (in_array($this->comment->creator->get('access'), User::getAuthorisedviewLevels())) {
        $name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
    }
}

    $cls = isset($this->cls) ? $this->cls : 'odd';
if (
    !$this->comment->get('anonymous')
    && $this->course->offering()->member($this->comment->get('created_by'))->get('id')
) {
    if (!$this->course->offering()->member($this->comment->get('created_by'))->get('student')) {
        $cls .= ' '
            . strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias'));
    } elseif (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section')) {
        $cls .= ' '
            . strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias'));
    }
}
    $cls .= ' ' . $this->comment->get('treename');

if ($this->unit) {
    $this->base .= '&unit=' . $this->unit;
}
if ($this->lecture) {
    $this->base .= '&b=' . $this->lecture;
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
                <a class="permalink" href="<?= Route::url($this->base . '#c' . $this->comment->get('id')) ?>"
                    title="<?= Lang::txt('PLG_COURSES_DISCUSSIONS_PERMALINK') ?>">
                    <span class="comment-date-at">@</span>
                    <span class="time"><time
                        datetime="<?= $this->comment->created() ?>"><?= $this->comment->created('time') ?></time></span>
                    <span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
                    <span class="date"><time
                        datetime="<?= $this->comment->created() ?>"><?= $this->comment->created('date') ?></time></span>
                    <?php if ($this->comment->wasModified()) { ?>
                        &mdash; <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDITED'); ?>
                        <span class="comment-date-at">@</span>
                        <?php
                        $datetime = $this->comment->modified();
                        ?>
                        <span class="time"><time
                            datetime="<?= $datetime ?>"><?= $this->comment->modified('time') ?></time></span>
                        <span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
                        <?php
                        $datetime = $this->comment->modified();
                        ?>
                        <span class="date"><time
                            datetime="<?= $datetime ?>"><?= $this->comment->modified('date') ?></time></span>
                    <?php } ?>
                </a>
                <?php
                if (
                    !$this->comment->get('anonymous')
                    && $this->course->offering()->member($this->comment->get('created_by'))->get('id')
                ) { ?>
                    <?php
                    if (
                        !$this->course->offering()->member($this->comment->get('created_by'))->get('student')
                    ) { ?>
                    <span class="role <?= strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias')) ?>">
                        <?= $this->escape(
                            stripslashes(
                                $this->course->offering()->member($this->comment->get('created_by'))->get('role_title'),
                            ),
                        ) ?>
                    </span>
                    <?php } elseif (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section')) { ?>
                        <span class="role <?= strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias')) ?>">
                            <?= $this->escape(
                                stripslashes(
                                    $this->course->offering()->member($this->comment->get('created_by'))->get(
                                        'role_title',
                                    ),
                                ),
                            ) ?>
                        </span>
                    <?php } ?>
                <?php } ?>
            </p>

            <div class="comment-body">
                <?php echo $comment; ?>
            </div>

            <p class="comment-options">
                <?php if ($this->config->get('access-edit-thread')) { ?>
                    <?php if ($this->config->get('access-delete-thread')) { ?>
                        <button type="submit" form="hz-post-action"
                            formaction="<?= Route::url($this->comment->link('delete')) ?>" class="icon-delete delete"
                            data-id="c<?= $this->comment->get('id') ?>"><!--
                            --><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE'); ?><!--
                        --></button>
                    <?php } ?>
                    <?php if ($this->config->get('access-edit-thread')) { ?>
                        <a class="icon-edit edit" data-id="c<?= $this->comment->get('id') ?>"
                            href="<?= Route::url($this->comment->link('edit')) ?>"><!--
                            --><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT'); ?><!--
                        --></a>
                    <?php } ?>
                <?php } ?>
                <?php if (!$this->comment->isReported()) { ?>
                    <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                        <?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
                        <a class="icon-reply reply active" data-txt-active="<?= Lang::txt('JCANCEL') ?>"
                            data-txt-inactive="<?= Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY') ?>"
                            href="<?= Route::url($this->comment->link('base')) ?>"
                            rel="comment-form<?= $this->comment->get('id') ?>"><!--
                        --><?php echo Lang::txt('JCANCEL'); ?><!--
                    --></a>
                        <?php } else { ?>
                        <a class="icon-reply reply" data-txt-active="<?= Lang::txt('JCANCEL') ?>"
                            data-txt-inactive="<?= Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY') ?>"
                            href="<?= Route::url($this->comment->link('reply')) ?>"
                            rel="comment-form<?= $this->comment->get('id') ?>"><!--
                        --><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY'); ?><!--
                    --></a>
                        <?php } ?>
                    <?php } ?>
                    <a class="icon-abuse abuse" href="<?= Route::url($this->comment->link('abuse')) ?>"
                        rel="comment-form<?= $this->comment->get('id') ?>"><!--
                        --><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPORT_ABUSE'); ?><!--
                    --></a>
                <?php } ?>
            </p>

            <?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
                <div class="comment-add<?= Request::getInt('reply', 0) != $this->comment->get('id') ? ' hide' : '' ?>"
                    id="comment-form<?= $this->comment->get('id') ?>">
                    <form id="cform<?= $this->comment->get('id') ?>"
                        action="<?= Route::url($this->comment->link('base')) ?>" method="post"
                        enctype="multipart/form-data">
                        <fieldset id="commentform<?php echo $this->comment->get('id'); ?>">
                            <legend><span><?= Lang::txt('PLG_COURSES_DISCUSSIONS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS'))) ?></span></legend>

                            <input type="hidden" name="fields[id]" value="0" />
                            <input type="hidden" name="fields[state]" value="1" />
                            <input type="hidden" name="fields[scope]" value="<?= $this->post->get('scope') ?>" />
                            <input type="hidden" name="fields[category_id]"
                                value="<?= $this->post->get('category_id') ?>" />
                            <input type="hidden" name="fields[scope_id]" value="<?= $this->post->get('scope_id') ?>" />
                            <input type="hidden" name="fields[scope_sub_id]"
                                value="<?= $this->post->get('scope_sub_id') ?>" />
                            <input type="hidden" name="fields[object_id]"
                                value="<?= $this->post->get('object_id') ?>" />
                            <input type="hidden" name="fields[parent]" value="<?= $this->comment->get('id') ?>" />
                            <input type="hidden" name="fields[thread]" value="<?= $this->comment->get('thread') ?>" />
                            <input type="hidden" name="fields[created]" value="" />
                            <input type="hidden" name="fields[created_by]" value="<?php echo User::get('id'); ?>" />
                            <input type="hidden" name="depth" value="<?php echo($this->depth + 1); ?>" />

                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                            <input type="hidden" name="offering" value="<?= $this->course->offering()->alias() ?>" />
                            <input type="hidden" name="active" value="discussions" />
                            <input type="hidden" name="action" value="savethread" />
                            <input type="hidden" name="return" value="<?= base64_encode(Route::url($this->base)) ?>" />

                            <?php echo Html::input('token'); ?>

                            <label for="comment_<?php echo $this->comment->get('id'); ?>_reply">
                                <span
                                    class="label-text"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS') ?></span>
                                <?= $this->editor(
                                    'fields[comment]',
                                    '',
                                    35,
                                    5,
                                    'comment_' . $this->comment->get('id') . '_reply',
                                    ['class' => 'minimal no-footer'],
                                ) ?>
                            </label>

                            <label class="upload-label" for="comment-<?php echo $this->comment->get('id'); ?>-file">
                                <span class="label-text"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_ATTACH_FILE') ?>:</span>
                                <input type="file" name="upload" id="comment-<?= $this->comment->get('id') ?>-file" />
                            </label>

                            <label class="reply-anonymous-label"
                                for="comment-<?= $this->comment->get('id') ?>-anonymous">
                                <?php if ($this->config->get('comments_anon', 1)) { ?>
                                    <input class="option" type="checkbox" name="fields[anonymous]"
                                        id="comment-<?= $this->comment->get('id') ?>-anonymous" value="1" />
                                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
                                <?php } else { ?>
                                    &nbsp; <input class="option" type="hidden" name="fields[anonymous]" value="0" />
                                <?php } ?>
                            </label>

                            <p class="submit">
                                <input type="submit" value="<?= Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT') ?>" />
                            </p>
                        </fieldset>
                    </form>
                </div><!-- / .addcomment -->
            <?php } ?>
        </div><!-- / .comment-content -->
        <?php
        if ($this->depth < $this->config->get('comments_depth', 3)) {
            $this
                ->view('list')
                ->set('parent', $this->comment->get('id'))
                ->set('thread', $this->comment->get('thread'))
                ->set('option', $this->option)
                ->set('comments', $this->comment->get('replies'))
                ->set('post', $this->post)
                ->set('unit', $this->unit)
                ->set('lecture', $this->lecture)
                ->set('config', $this->config)
                ->set('depth', $this->depth)
                ->set('cls', $cls)
                ->set('base', $this->base)
                ->set('attach', $this->attach)
                ->set('course', $this->course)
                ->set('search', $this->search)
                ->display();
        }
        ?>
    </li>
