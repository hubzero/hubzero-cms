<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     canModerate: mixed,
 *     canStar: mixed,
 *     group: mixed,
 *     online: mixed,
 *     plugin: mixed,
 *     row: mixed,
 * } $this
 */

$status = '';
if (!$this->row->wasViewed()) {
    $status = 'new';

    $this->row->markAsViewed();
}

$creator = User::getInstance($this->row->log->get('created_by'));

$name = Lang::txt('JANONYMOUS');

$online = false;

// Is it not anonymous?
if (!$this->row->log->get('anonymous')) {
    // Get their full name
    $name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_GROUPS_ACTIVITY_UNKNOWN'))));

    // Can we see their profile?
    if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) {
        $name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
    }

    if (isset($this->online) && in_array($this->row->log->get('created_by'), $this->online)) {
        $online = true;
    }
}

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity';
?>
<li
    data-time="<?php echo $this->row->get('created'); ?>"
    data-id="<?php echo $this->row->get('id'); ?>"
    data-log_id="<?php echo $this->row->get('log_id'); ?>"
    data-context="<?php echo $this->row->log->get('scope'); ?>"
    data-action="<?php echo $this->row->log->get('action'); ?>"
    id="activity<?php echo $this->row->get('id'); ?>"
    class="activity <?= $this->row->get('scope') ?> <?= $status . ($this->row->get('starred') ? ' starred' : '') ?>">

    <?php
    $tooltipsClass = $online ? ' tooltips" title="' . Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE') : '';
    ?>
    <div class="activity-actor-picture<?= $tooltipsClass ?>">
        <?php if ($creator->get('public')) { ?>
            <a class="user-img-wrap" href="<?php echo Route::url($creator->link()); ?>" title="<?php echo $name; ?>">
                <img src="<?= $creator->picture() ?>"
                    alt="<?= Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', $name) ?>" />
                <?php if ($online) { ?>
                    <span class="online"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE'); ?></span>
                <?php } ?>
            </a>
        <?php } else { ?>
            <span class="user-img-wrap">
                <img src="<?= $creator->picture() ?>"
                    alt="<?= Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', $name) ?>" />
                <?php if ($online) { ?>
                    <span class="online"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE'); ?></span>
                <?php } ?>
            </span>
        <?php } ?>
    </div><!-- / .activity-actor-picture -->

    <?php
    $classes = 'activity-content ' . $this->escape($this->row->log->get('action')) . ' '
        . $this->escape(str_replace('.', '-', $this->row->log->get('scope')));
    ?>
    <div class="<?= $classes ?>">
        <div class="activity-body">
            <div class="activity-details">
                <span class="activity-actor"><?php echo $name; ?></span>
                <span class="activity-action"><?php echo $this->escape($this->row->log->get('action')); ?></span>
                <?php
                $activityChannelText = ($this->row->get('scope') == 'group_managers')
                    ? Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_MANAGERS')
                    : Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_ALL');
                ?>
                <span class="activity-channel"><?= $activityChannelText ?></span>
                <span class="activity-context"><?php
                    $scope = explode('.', $this->row->log->get('scope'));
                    echo $this->escape($scope[0]);
                ?></span>
                <?php
                $datetime = Date::of($this->row->get('created'))->format('Y-m-d\TH:i:s\Z');
                ?>
                <span class="activity-time"><time datetime="<?= $datetime ?>"><?php
                    $dt = Date::of($this->row->get('created'));
                    $ct = Date::of('now');

                    $lapsed = $ct->toUnix() - $dt->toUnix();

                if ($lapsed < 30) {
                    echo Lang::txt('PLG_GROUPS_ACTIVITY_JUST_NOW');
                } elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y')) {
                    echo $dt->toLocal('M j, Y');
                } elseif ($lapsed > 86400) {
                    echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
                } else {
                    echo $dt->relative();
                }
                ?></time></span>
            </div><!-- / .activity-details -->

            <div class="activity-event">
                <?php
                if (substr($this->row->log->get('scope'), 0, strlen('project')) == 'project') {
                    // @TODO: Find a better way to associate comments to their parent scope (group, projects)
                    $project = new \Components\Projects\Models\Project(
                        $this->row->log->details->get('projectid', $this->row->log->get('scope_id')),
                    );

                    if ($project) {
                        $projectDetailUrl = $this->row->log->details->get('url');
                        // The stored URL is only followed to a site path or an http(s) address
                        $projectDetailUrl = (
                            !empty($projectDetailUrl) && preg_match('#^(https?://|/)#i', $projectDetailUrl)
                        )
                            ? $projectDetailUrl
                            : Route::url('index.php?option=com_projects&alias=' . $project->get('alias'));
                        ?>
                        <div class="activity-source icon-project">
                            <?php
                            $linkUrl = $this->escape($projectDetailUrl);
                            ?>
                            <a href="<?= $linkUrl ?>"><?= $this->escape(stripslashes($project->get('title'))) ?></a>
                        </div>
                        <?php
                    }
                }
                ?>

                <?php
                $content = $this->row->log->get('description');
                $short = null;

                $attachments = $this->row->log->details->get('attachments');
                $attachments = $attachments ?: [];

                $attached = count($attachments);

                if (strlen(strip_tags($content)) > 150) {
                    $short = Hubzero\Utility\Str::truncate($content, 150, ['html' => true]);
                    ?>
                    <div class="activity-event-preview">
                        <?php echo $short; ?>
                        <p>
                            <a class="more-content" href="#activity-event-content<?php echo $this->row->get('id'); ?>">
                                <?php echo Lang::txt('PLG_GROUPS_ACTIVITY_MORE'); ?>
                            </a>
                        </p>
                    </div>
                    <?php
                }
                ?>
                <div class="activity-event-content<?= ($short) ? ' hide' : '' ?>"
                    id="activity-event-content<?= $this->row->get('id') ?>">
                    <?php echo $content; ?>
                </div>

                <?php if ($attached) { ?>
                    <div class="activity-attachments">
                        <?php
                        foreach ($attachments as $attachment) {
                            $attachment = new Plugins\Groups\Activity\Models\Attachment($attachment);
                            $attachment->setUploadDir('/site/groups/' . $this->group->get('gidNumber') . '/uploads');

                            if (!$attachment->exists()) {
                                continue;
                            }

                            if (!trim($attachment->get('description'))) {
                                $attachment->set('description', $attachment->get('filename'));
                            }

                            $link = 'index.php?option=com_groups&cn=' . $this->group->get('cn')
                                . '&active=File:/uploads/'
                                . ($attachment->get('subdir') ? $attachment->get('subdir') . '/' : '')
                                . $attachment->get('filename');

                            if ($attachment->isImage()) {
                                ?>
                                <a class="attachment img" rel="lightbox" href="<?php echo Route::url($link); ?>">
                                    <img src="<?= Route::url($link) ?>"
                                        alt="<?= $this->escape($attachment->get('description')) ?>"
                                        width="<?= ($attachment->width() > 400) ? 400 : $attachment->width() ?>" />
                                    <p class="attachment-meta">
                                        <?php
                                        $attachmentSizeText = Hubzero\Utility\Number::formatBytes($attachment->size());
                                        ?>
                                        <span class="attachment-size"><?= $attachmentSizeText ?></span>
                                        <?php
                                        $attachmentActionText = Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD');
                                        ?>
                                        <span class="attachment-action"><?= $attachmentActionText ?></span>
                                    </p>
                                </a>
                                <?php
                            } else {
                                ?>
                                <a class="attachment <?= Filesystem::extension($attachment->get('filename')) ?>"
                                    href="<?= Route::url($link) ?>"
                                    title="<?= $this->escape($attachment->get('description')) ?>">
                                    <p class="attachment-description">
                                        <?= $this->escape($attachment->get('description')) ?>
                                    </p>
                                    <p class="attachment-meta">
                                        <?php
                                        $attachmentSizeText = Hubzero\Utility\Number::formatBytes($attachment->size());
                                        ?>
                                        <span class="attachment-size"><?= $attachmentSizeText ?></span>
                                        <?php
                                        $attachmentActionText = Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD');
                                        ?>
                                        <span class="attachment-action"><?= $attachmentActionText ?></span>
                                    </p>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div><!-- / .activity-attachments -->
                <?php } ?>
            </div><!-- / .activity-event -->

            <div class="activity-options">
                <?php if ($this->group->published == 1) { ?>
                <ul class="activity-options-main">
                    <?php
                    if (
                        $this->row->log->get('scope') == 'activity.comment' && !$this->row->log->get('parent')
                    ) { ?>
                        <li>
                            <?php if (Request::getInt('reply', 0) == $this->row->get('id')) { ?>
                                <a
                                    class="icon-reply reply tooltips active"
                                    data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>"
                                    data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
                                    title="<?php echo Lang::txt('JCANCEL'); ?>"
                                    href="<?php echo Route::url($base); ?>"
                                    rel="comment-form<?php echo $this->row->get('id'); ?>"><!--
                                    --><?php echo Lang::txt('JCANCEL'); ?><!--
                                --></a>
                            <?php } else { ?>
                                <a
                                    class="icon-reply reply tooltips"
                                    data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>"
                                    data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
                                    title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
                                    href="<?= Route::url($base . '&action=reply&activity=' . $this->row->get('id')) ?>"
                                    rel="comment-form<?php echo $this->row->get('id'); ?>"><!--
                                    --><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?><!--
                                --></a>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if ($this->canStar && !$this->row->log->get('parent')) { ?>
                        <li>
                            <?php
                            $titleText = ($this->row->get('starred'))
                                ? Lang::txt('Unstar this')
                                : Lang::txt('Star this');
                            $starUrl = Route::url($base . '&action=star&activity=' . $this->row->get('id'));
                            $unstarUrl = Route::url($base . '&action=unstar&activity=' . $this->row->get('id'));
                            $actionUrl = Route::url(
                                $base . '&action=' . ($this->row->get('starred') ? 'un' : '') . 'star&activity='
                                    . $this->row->get('id'),
                            );
                            ?>
                            <button type="submit" form="hz-post-action"
                                data-id="activity<?php echo $this->row->get('id'); ?>"
                                class="icon-starred tooltips"
                                formaction="<?= $actionUrl ?>"
                                data-hrf-active="<?= $unstarUrl ?>"
                                data-hrf-inactive="<?= $starUrl ?>"
                                data-txt-active="<?php echo Lang::txt('Unstar this'); ?>"
                                data-txt-inactive="<?php echo Lang::txt('Star this'); ?>"
                                title="<?= $titleText ?>"><!--
                                <?php
                                // (this block sits inside the comment that closes on the next line)
                                $starText = ($this->row->get('starred'))
                                    ? Lang::txt('Unstar this')
                                    : Lang::txt('Star this');
                                ?>
                                --><?= $starText ?><!--
                            --></button>
                        </li>
                    <?php } ?>
                    <?php if ($this->canModerate || $this->plugin->isOwnContent($this->row->log)) { ?>
                    <li>
                        <?php
                        $removeUrl = Route::url($base . '&action=remove&activity=' . $this->row->get('id'));
                        $dataTxtConfirm = Lang::txt('PLG_GROUPS_ACTIVITY_CONFIRM_DELETE');
                        $buttonText = Lang::txt('PLG_GROUPS_ACTIVITY_DELETE');
                        ?>
                        <button type="submit" form="hz-post-action" formaction="<?= $removeUrl ?>"
                            class="icon-delete tooltips" title="<?= Lang::txt('PLG_GROUPS_ACTIVITY_DELETE') ?>"
                            data-id="activity<?= $this->row->get('id') ?>"
                            data-txt-confirm="<?= $dataTxtConfirm ?>"><?= $buttonText ?></button>
                    </li>
                    <?php } ?>
                    <?php /*<li>
                        <a data-id="activity<?php echo $this->row->get('id'); ?>" class="icon-options tooltips"
                            href="#moreoptions<?php echo $this->row->get('id'); ?>" title="<?php echo
                            Lang::txt('PLG_GROUPS_ACTIVITY_OPTIONS'); ?>"><?php echo
                            Lang::txt('PLG_GROUPS_ACTIVITY_OPTIONS'); ?></a>
                        <ul class="activity-options-more" id="moreoptions<?php echo $this->row->get('id'); ?>">
                            <li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo
                                Route::url($base . '&action=unsubscribe&scope=' . $this->row->get('scope')); ?>">Hide
                                all like this</a></li>
                            <li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo
                                Route::url($base . '&action=share&activity=' . $this->row->get('id'));
                                ?>">Share</a></li>
                        </ul>
                    </li>*/ ?>
                </ul>
                <?php } ?>
            </div><!-- / .activity-options -->
            
        </div><!-- / .activity-body -->

        <?php if ($this->group->published == 1 && $this->row->log->get('scope') == 'activity.comment') { ?>
            <div class="comment-add<?= Request::getInt('reply', 0) != $this->row->get('id') ? ' hide' : '' ?>"
                id="comment-form<?= $this->row->get('id') ?>">
                <form id="cform<?= $this->row->get('id') ?>" action="<?= Route::url($base) ?>" method="post"
                    enctype="multipart/form-data">
                    <fieldset>
                        <?php
                        $spanText = Lang::txt(
                            'PLG_GROUPS_ACTIVITY_REPLYING_TO',
                            (!$this->row->log->get('anonymous') ? $name : Lang::txt('JANONYMOUS')),
                        );
                        ?>
                        <legend><span><?= $spanText ?></span></legend>

                        <input type="hidden" name="activity[id]" value="0" />
                        <input type="hidden" name="activity[action]" value="created" />
                        <input type="hidden" name="activity[scope]" value="<?= $this->row->log->get('scope') ?>" />
                        <input type="hidden" name="activity[scope_id]"
                            value="<?= $this->row->log->get('scope_id') ?>" />
                        <input type="hidden" name="activity[parent]" value="<?= $this->row->log->get('id') ?>" />
                        <input type="hidden" name="activity[created]" value="" />
                        <input type="hidden" name="activity[created_by]" value="<?php echo User::get('id'); ?>" />

                        <input type="hidden" name="option" value="com_groups" />
                        <input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
                        <input type="hidden" name="active" value="activity" />
                        <input type="hidden" name="action" value="post" />

                        <?php echo Html::input('token'); ?>

                        <div class="form-group">
                            <label for="comment-<?php echo $this->row->get('id'); ?>-content">
                                <span class="label-text"><?= Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_COMMENTS') ?></span>
                                <?= $this->editor(
                                    'activity[description]',
                                    '',
                                    35,
                                    4,
                                    'field_' . $this->row->get('id') . '_comment',
                                    ['class' => 'form-control minimal no-footer'],
                                ) ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="upload-label" for="activity-<?php echo $this->row->get('id'); ?>-file">
                                <span class="label-text"><?= Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_FILE') ?></span>
                                <input type="file" class="inputfile form-control-file" name="activity_file"
                                    id="activity-<?= $this->row->get('id') ?>-file"
                                    data-multiple-caption="<?= Lang::txt('{count} files selected') ?>"
                                    multiple="multiple" />
                            </label>
                        </div>

                        <p class="submit">
                            <input type="submit" class="btn btn-secondary"
                                value="<?= Lang::txt('PLG_GROUPS_ACTIVITY_SUBMIT') ?>" />
                        </p>
                    </fieldset>
                </form>
            </div><!-- / .comment-add -->
        <?php } ?>

        <div class="activity-processor">
            <div class="spinner"><div></div></div>
            <div class="msg"></div>
        </div><!-- / .activity-processor -->
    </div><!-- / .activity-content -->

    <?php
    if ($this->row->log->get('scope') == 'activity.comment' || $this->row->log->get('scope') == 'project.comment') {
        $recipient = Hubzero\Activity\Recipient::all();

        $r = $recipient->getTableName();
        $l = Hubzero\Activity\Log::blank()->getTableName();

        // Replies in the managers' channel are recorded for managers only
        $scopes = ['group'];
        if (in_array(User::get('id'), $this->group->get('managers'))) {
            $scopes[] = 'group_managers';
        }

        $rows = $recipient
            ->select($r . '.*')
            ->including('log')
            ->join($l, $l . '.id', $r . '.log_id')
            ->whereIn($r . '.scope', $scopes)
            ->whereEquals($r . '.scope_id', $this->group->get('gidNumber'))
            ->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
            ->whereEquals($l . '.parent', $this->row->log->get('id'))
            ->order('id', 'asc')
            ->rows();

        if ($rows->count()) {
            ?>
            <ul class="activity-comments">
                <?php
                foreach ($rows as $row) {
                    $this
                        ->view('default_item')
                        ->set('group', $this->group)
                        ->set('canModerate', $this->canModerate)
                        ->set('canStar', $this->canStar)
                        ->set('plugin', $this->plugin)
                        ->set('online', $this->online)
                        ->set('row', $row)
                        ->display();
                }
                ?>
            </ul>
            <?php
        }
    }
    ?>

</li>
