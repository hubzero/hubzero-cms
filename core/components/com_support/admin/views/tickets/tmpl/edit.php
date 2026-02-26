<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

// No direct access.
defined('_HZEXEC_') or die();

$user = User::getInstance();

$unknown  = true;
$name     = '';
$usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
$notify   = array();

if ($this->row->get('login')) {
    if ($this->row->get('name')) {
        $gids = array();
        foreach (User::getInstance($this->row->submitter->get('id'))->accessgroups() as $g) {
            $gids[] = $g->group_id;
        }
        $usertype = implode(', ', $gids);

        $submitterUrl = Route::url(
            'index.php?option=com_members&task=edit&id=' . $this->row->submitter->get('id')
        );
        $escapedName  = $this->escape(stripslashes($this->row->get('name')));
        $escapedLogin = $this->escape(stripslashes($this->row->get('login')));
        $name = '<a rel="profile" href="' . $submitterUrl . '">'
            . $escapedName . ' (' . $escapedLogin . ')</a>';
        $unknown = false;

        $notify[] = $escapedName . ' (' . $escapedLogin . ')';
    }
}

if (!$name) {
    if ($this->row->get('name')) {
        $name = $this->escape($this->row->get('name')) . ' (' . $this->escape($this->row->get('email')) . ')';
    } else {
        $name = $this->escape($this->row->get('email'));
    }
    $notify[] = $name;
}

if ($this->row->isOwned()) {
    if ($this->row->assignee->get('name')) {
        $assigneeName  = $this->escape(stripslashes($this->row->assignee->get('name')));
        $assigneeLogin = $this->escape(stripslashes($this->row->assignee->get('username')));
        $notify[] = $assigneeName . ' (' . $assigneeLogin . ')';
    }
}

$lastactivity = Lang::txt('COM_SUPPORT_NOT_APPLICAPABLE');
if ($this->row->comments->count() > 0) {
    $last = $this->row->comments->last();
    $lastCreated  = $last->get('created');
    $lastFormatted = Date::of($lastCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
    $lastactivity = '<time datetime="' . $lastCreated . '">' . $lastFormatted . '</time>';
    $this->row->comments->rewind();
}

$cc = array();

$no_html = Request::getInt('no_html', 0);
if (!$no_html) {
    $text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

    $toolbarTitle = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . $text;
    Toolbar::title($toolbarTitle, 'support.png');
    Toolbar::save();
    Toolbar::apply();
    Toolbar::cancel();
    Toolbar::spacer();
    Toolbar::help('ticket');

    Html::behavior('tooltip');
    $this->css()
        ->js('ticket.js');
}
?>
<?php
$formAction  = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$formNameId  = (!$no_html) ? 'name="adminForm" id="item-form"' : 'name="ajaxForm" id="ajax-form"';
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    <?php echo $formNameId; ?>
    enctype="multipart/form-data"
>
    <?php if (!$no_html) { ?>
    <div class="grid">
    <div class="col span8">
        <fieldset>
            <?php
            $legendText = Lang::txt('COM_SUPPORT_TICKET')
                . ($this->row->get('id') ? ' #' . $this->row->get('id') : '');
            ?>
            <legend><span><?php echo $legendText; ?></span></legend>
    <?php } else { ?>
                <dl class="ticket-info <?php echo $this->row->get('severity'); ?>">
                    <dt>#</dt>
                    <dd><?php echo $this->row->get('id'); ?></dd>
                    <dt>Type:</dt>
                    <dd>Issue</dd>
                    <dt><?php echo Lang::txt('COM_SUPPORT_TICKET_STATUS'); ?>:</dt>
                    <?php
                    $statusClass = (!$this->row->isOpen()) ? 'closed' : 'open';
                    $statusLabel = (!$this->row->isOpen())
                        ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED')
                        : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN');
                    ?>
                    <dd class="ticket-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></dd>
                    <dt><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY'); ?>:</dt>
                    <?php
                    $severityKey = 'COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($this->row->get('severity'));
                    ?>
                    <dd class="ticket-severity <?php echo $this->row->get('severity'); ?>">
                        <?php echo Lang::txt($severityKey); ?>
                    </dd>
                </dl>
    <?php } ?>
            <div class="ticket<?php echo ($no_html) ? '-body' : ''; ?>" id="t<?php echo $this->row->get('id'); ?>">
                <p class="ticket-member-photo">
                    <img src="<?php echo $this->row->submitter()->picture($unknown); ?>" alt="" />
                </p>
                <div class="ticket-head">
                    <strong>
                        <?php echo $name; ?>
                    </strong>
                    <?php
                    $permalinkUrl = Route::url(
                        'index.php?option=com_support&controller=tickets&task=edit&id='
                        . $this->row->get('id')
                    );
                    $permalinkTitle = Lang::txt('COM_SUPPORT_PERMALINK');
                    $ticketCreated  = $this->row->get('created');
                    $timeFormatted  = Date::of($ticketCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                    $dateFormatted  = Date::of($ticketCreated)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    ?>
                    <a
                        class="permalink"
                        href="<?php echo $permalinkUrl; ?>"
                        title="<?php echo $permalinkTitle; ?>"
                    >
                        <span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
                        <span class="time">
                            <time datetime="<?php echo $ticketCreated; ?>"><?php echo $timeFormatted; ?></time>
                        </span>
                        <span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
                        <span class="date">
                            <time datetime="<?php echo $ticketCreated; ?>"><?php echo $dateFormatted; ?></time>
                        </span>
                    </a>
                </div>
                <?php
                $blockquoteCite = $this->escape($this->row->get('login', $this->row->get('name')));
                ?>
                <blockquote class="ticket-content" cite="<?php echo $blockquoteCite; ?>">
                    <p><?php echo $this->row->content; ?></p>

                    <?php if ($this->row->attachments->count()) { ?>
                        <div class="comment-attachments">
                            <?php
                            foreach ($this->row->attachments as $attachment) {
                                if (!trim($attachment->get('description'))) {
                                    $attachment->set('description', $attachment->get('filename'));
                                }

                                if ($attachment->isImage()) {
                                    $attLink = Route::url($attachment->link());
                                    $attDesc = $attachment->get('description');
                                    if ($attachment->width() > 400) {
                                        $img = '<p><a href="' . $attLink . '">'
                                            . '<img src="' . $attLink . '" alt="' . $attDesc . '"'
                                            . ' width="400" /></a></p>';
                                    } else {
                                        $img = '<p><img src="' . $attLink . '" alt="' . $attDesc . '" /></p>';
                                    }
                                    echo $img;
                                } else {
                                    $attLink  = Route::url($attachment->link());
                                    $attDesc  = $attachment->get('description');
                                    echo '<p class="attachment"><a href="' . $attLink . '"'
                                        . ' title="' . $attDesc . '">' . $attDesc . '</a></p>';
                                }
                            }
                            ?>
                        </div><!-- / .comment-body -->
                    <?php } ?>
                </blockquote><!-- / .ticket-content -->
                <div class="ticket-details">
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_EMAIL'); ?>:</th>
                                <td>
                                    <a href="mailto:<?php echo $this->row->get('email'); ?>">
                                        <?php echo $this->escape($this->row->get('email')); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE'); ?>:
                                </th>
                                <td><?php echo $this->escape($usertype); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_OS'); ?>:</th>
                                <td>
                                    <?php echo $this->escape($this->row->get('os')); ?>
                                    /
                                    <?php echo $this->escape($this->row->get('browser')); ?>
                                    (<?php echo ($this->row->get('cookies'))
                                        ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED')
                                        : Lang::txt('COM_SUPPORT_COOKIES_DISABLED'); ?>)
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_IP'); ?>:</th>
                                <td>
                                    <?php echo $this->escape($this->row->get('ip')); ?>
                                    (<?php echo $this->escape($this->row->get('hostname')); ?>)
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_REFERRER'); ?>:
                                </th>
                                <td><?php echo $this->escape($this->row->get('referrer', ' ')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_INSTANCES'); ?>:
                                </th>
                                <td><?php echo $this->escape($this->row->get('instances')); ?></td>
                            </tr>
                        <?php if ($uas = $this->row->get('uas')) { ?>
                            <tr>
                                <td colspan="2"><?php echo $this->escape($this->row->get('uas')); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div><!-- / .ticket-details -->
            </div><!-- / .ticket -->
    <?php if (!$no_html) { ?>
        </fieldset>
    </div>
    <div class="col span4">
        <?php
        $isOpenClass = (!$this->row->isOpen()) ? 'closed' : 'open';
        $isOpenLabel = (!$this->row->isOpen())
            ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED')
            : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN');
        ?>
        <dl class="ticket-status <?php echo $isOpenClass; ?>">
            <dt><?php echo Lang::txt('COM_SUPPORT_TICKET_STATUS'); ?></dt>
            <dd><?php echo $isOpenLabel; ?></dd>
        </dl>

        <table class="meta">
            <tbody>
                <tr>
                    <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY'); ?></th>
                    <td>
                        <?php
                        $sevKey = 'COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($this->row->get('severity'));
                        echo Lang::txt($sevKey);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_OWNER'); ?></th>
                    <td><?php
                    if ($this->row->isOwned()) {
                        if ($this->row->assignee->get('id')) {
                            $assigneeUrl  = Route::url(
                                'index.php?option=com_members&task=edit&id='
                                . $this->row->assignee->get('id')
                            );
                            $assigneeDisp = $this->escape(stripslashes($this->row->assignee->get('name')));
                            echo '<a rel="profile" href="' . $assigneeUrl . '">' . $assigneeDisp . '</a>';
                        } else {
                            echo $this->escape($this->row->get('owner'));
                        }
                    } else {
                        echo Lang::txt('COM_SUPPORT_NONE');
                    }
                    ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_LAST_ACTIVITY'); ?></th>
                    <td><?php echo $lastactivity; ?></td>
                </tr>
            </tbody>
        </table>

        <div class="ticket-watch">
            <?php if ($this->row->isWatching()) { ?>
                <div id="watching">
                    <p><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_IN_LIST'); ?></p>
                    <?php
                    $stopWatchUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=edit&id=' . $this->row->get('id')
                        . '&watch=stop'
                    );
                    ?>
                    <p>
                        <a class="stop-watching btn" href="<?php echo $stopWatchUrl; ?>">
                            <?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_STOP_WATCHING'); ?>
                        </a>
                    </p>
                </div>
            <?php } else { ?>
                <?php
                $startWatchUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=edit&id=' . $this->row->get('id')
                    . '&watch=start'
                );
                ?>
                <p>
                    <a class="start-watching btn" href="<?php echo $startWatchUrl; ?>">
                        <?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_START_WATCHING'); ?>
                    </a>
                </p>
            <?php } ?>
            <p><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_ABOUT'); ?></p>
        </div>
    </div>
    </div>
    <?php } ?>

    <?php if ($no_html) { ?>
    <div class="ticket-comments">
    <?php } ?>

    <?php if ($this->row->comments->count() > 0) { ?>
        <?php if (!$no_html) { ?>
        <div class="grid">
        <div class="col span8">
        <?php } ?>
            <fieldset>
                <legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENTS'); ?></span></legend>

                <ol class="comments">
                <?php
                foreach ($this->row->comments as $comment) {
                    $access = 'public';
                    if ($comment->isPrivate()) {
                        $access = 'private';
                    }

                    if ($comment->get('created_by') == $this->row->get('login') && !$comment->isPrivate()) {
                        $access = 'submitter';
                    }

                    $name = Lang::txt('COM_SUPPORT_UNKNOWN');
                    $cite = $name;

                    if ($comment->creator->get('id')) {
                        $cite = $this->escape(stripslashes($comment->creator->get('name')));
                        $creatorUrl = Route::url(
                            'index.php?option=com_members&task=edit&id[]=' . $comment->creator->get('id')
                        );
                        $creatorLogin = $this->escape($comment->creator->get('username'));
                        $name = '<a href="' . $creatorUrl . '">'
                            . $cite . ' (' . $creatorLogin . ')</a>';
                    }

                    if ($comment->changelog()->format() != 'html') {
                        $cc = $comment->changelog()->get('cc');
                    }
                    $commentCreated    = $comment->get('created');
                    $commentTimeLocal  = Date::of($commentCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                    $commentDateLocal  = Date::of($commentCreated)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    $commentPermalink  = 'index.php?option=com_support&amp;controller=tickets'
                        . '&amp;task=edit&amp;id=' . $this->row->get('id') . '#c' . $comment->get('id');
                    $commentPermaTitle = Lang::txt('COM_SUPPORT_PERMALINK');
                    $profileImgAlt     = Lang::txt('COM_SUPPORT_PROFILE_IMAGE');
                    ?>
                    <li class="<?php echo $access . ' comment'; ?>" id="c<?php echo $comment->get('id'); ?>">
                        <p class="comment-member-photo">
                            <span class="comment-anchor"></span>
                            <img
                                src="<?php echo $comment->creator->picture(); ?>"
                                alt="<?php echo $profileImgAlt; ?>"
                            />
                        </p>
                        <p class="comment-head">
                            <strong>
                                <?php echo $name; ?>
                            </strong>
                            <a
                                class="permalink"
                                href="<?php echo $commentPermalink; ?>"
                                title="<?php echo $commentPermaTitle; ?>"
                            >
                                <span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
                                <span class="time">
                                    <time datetime="<?php echo $this->escape($commentCreated); ?>">
                                        <?php echo $commentTimeLocal; ?>
                                    </time>
                                </span>
                                <span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
                                <span class="date">
                                    <time datetime="<?php echo $this->escape($commentCreated); ?>">
                                        <?php echo $commentDateLocal; ?>
                                    </time>
                                </span>
                            </a>
                        </p>
                        <blockquote class="comment-content" cite="<?php echo $cite; ?>">
                        <?php if ($content = $comment->comment) { ?>
                            <p><?php echo $content; ?></p>
                        <?php } else { ?>
                            <p class="comment-none">
                                <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_NO_CONTENT'); ?>
                            </p>
                        <?php } ?>
                        <?php if ($comment->attachments->count()) { ?>
                            <div class="comment-attachments">
                                <?php
                                foreach ($comment->attachments as $attachment) {
                                    if (!trim($attachment->get('description'))) {
                                        $attachment->set('description', $attachment->get('filename'));
                                    }

                                    if ($attachment->isImage()) {
                                        $attLink = Route::url($attachment->link());
                                        $attDesc = $attachment->get('description');
                                        if ($attachment->width() > 400) {
                                            $img = '<p><a href="' . $attLink . '">'
                                                . '<img src="' . $attLink . '" alt="' . $attDesc . '"'
                                                . ' width="400" /></a></p>';
                                        } else {
                                            $img = '<p><img src="' . $attLink
                                                . '" alt="' . $attDesc . '" /></p>';
                                        }
                                        echo $img;
                                    } else {
                                        $attLink = Route::url($attachment->link());
                                        $attDesc = $attachment->get('description');
                                        echo '<p class="attachment"><a href="' . $attLink . '"'
                                            . ' title="' . $attDesc . '">' . $attDesc . '</a></p>';
                                    }
                                }
                                ?>
                            </div><!-- / .comment-body -->
                        <?php } ?>
                        </blockquote><!-- / .comment-content -->
                        <div class="comment-changelog">
                            <?php echo $comment->changelog()->render(); ?>
                        </div><!-- / .changelog -->
                    </li>
                    <?php
                }
                ?>
                </ol>
            </fieldset>
        <?php if (!$no_html) { ?>
        </div>
        <div class="col span4">
            <p>
                <a class="new button" href="#commentform">
                    <?php echo Lang::txt('COM_SUPPORT_TICKET_ADD_COMMENT'); ?>
                </a>
            </p>
        </div>
        </div>
        <?php } ?>
    <?php } // end if (count($comments) > 0) ?>

    <?php if (!$no_html) { ?>
    <div class="grid">
    <div class="col span8">
    <?php } ?>
        <fieldset id="commentform">
            <legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS'); ?></span></legend>

            <div class="new ticket">
                <p class="ticket-member-photo">
                    <span class="ticket-anchor"></span>
                    <img
                        src="<?php echo $user->picture(0); ?>"
                        alt="<?php echo Lang::txt('COM_SUPPORT_PROFILE_IMAGE'); ?>"
                    />
                </p>

                <fieldset class="ticket-head">
                    <strong>
                        <?php
                        $userProfileUrl = Route::url(
                            'index.php?option=com_members&task=edit&id=' . $this->escape($user->get('id'))
                        );
                        ?>
                        <a rel="profile" href="<?php echo $userProfileUrl; ?>">
                            <?php echo $this->escape($user->get('name')); ?>
                            (<?php echo $this->escape($user->get('username')); ?>)
                        </a>
                    </strong>
                    <span class="permalink">
                        <span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
                        <span class="time">
                            <time><?php echo Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time>
                        </span>
                        <span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
                        <span class="date">
                            <time><?php echo Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
                        </span>
                    </span>

                    <?php
                    $accessTitle = Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION');
                    $accessLabel = Lang::txt('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS');
                    ?>
                    <label
                        for="comment-field-access"
                        class="private hasTip"
                        title="<?php echo $accessTitle; ?>"
                    >
                        <input type="checkbox" name="access" id="comment-field-access" value="1" />
                        <span><?php echo $accessLabel; ?></span>
                    </label>
                </fieldset><!-- / .ticket-head -->

                <div class="ticket-content">
                    <?php
                    $results = Event::trigger('support.onTicketComment', array($this->row));
                    echo implode("\n", $results);
                    ?>
                    <fieldset>
                        <div class="input-wrap">
                            <label for="comment-field-template">
                                <select name="messages" id="comment-field-template">
                                    <option value="custom">
                                        <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_CUSTOM'); ?>
                                    </option>
                                    <?php
                                    $hi = array();
                                    foreach ($this->lists['messages'] as $message) {
                                        $msgTitle = $this->escape(stripslashes($message->title));
                                        ?>
                                        <option value="m<?php echo $message->id; ?>">
                                            <?php echo $msgTitle; ?>
                                        </option>
                                        <?php
                                        $msgVal = $this->escape($message->transformMessage($this->row->get('id')));
                                        $hi[] = '<input type="hidden"'
                                            . ' name="m' . $message->id . '"'
                                            . ' id="m' . $message->id . '"'
                                            . ' value="' . $msgVal . '" />';
                                    }
                                    ?>
                                </select>
                                <?php echo implode("\n", $hi); ?>
                            </label>

                            <label for="comment-field-content">
                                <span class="label">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS'); ?>
                                </span>
                                <textarea
                                    name="comment"
                                    id="comment-field-comment"
                                    cols="75"
                                    rows="15"
                                ><?php echo $this->comment->get('comment'); ?></textarea>
                            </label>

                            <?php if ($this->config->get('email_terse')) { ?>
                                <label for="email_terse">
                                    <input
                                        class="option"
                                        type="checkbox"
                                        name="email_terse"
                                        id="email_terse"
                                        value="1"
                                        checked="checked"
                                    />
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_TERSE'); ?>
                                </label>
                            <?php } ?>
                        </div>

                        <!--
                        <div class="grid">
                            <div class="col span6">
                                <div class="input-wrap">
                                    <label for="comment-field-upload">
                                        <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>
                                        <input type="file" name="upload" id="comment-field-upload" />
                                    </label>
                                </div>
                            </div>
                            <div class="col span6">
                                <div class="input-wrap">
                                    <label for="comment-field-description">
                                        <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION'); ?>
                                        <input type="text" name="description"
                                            id="comment-field-description" value="" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        -->
                        <fieldset>
                            <legend><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
                            <?php
                            $tmp = Request::getString('tmp_dir', ('-' . time()), 'post');
                            if (!$no_html) {
                                $this->js('jquery.fileuploader.js', 'system');
                            }
                            $uploadAction = Route::url(
                                'index.php?option=com_support&controller=media&task=upload'
                                . '&no_html=1&ticket=' . $this->row->get('id') . '&comment=' . $tmp
                            );
                            $listAction = Route::url(
                                'index.php?option=com_support&controller=media&task=list'
                                . '&no_html=1&ticket=' . $this->row->get('id') . '&comment=' . $tmp
                            );
                            $uploadInstructions = Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_INSTRUCTIONS');
                            ?>
                            <div
                                id="ajax-uploader"
                                data-action="<?php echo $uploadAction; ?>"
                                data-list="<?php echo $listAction; ?>"
                                data-instructions="<?php echo $uploadInstructions; ?>"
                            >
                                <noscript>
                                    <div class="input-wrap">
                                        <label for="upload">
                                            <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>:
                                            <input type="file" name="upload" id="upload" />
                                        </label>
                                    </div>

                                    <div class="input-wrap">
                                        <label for="field-description">
                                            <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION'); ?>:
                                            <input type="text" name="description" id="field-description" value="" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                            <div class="field-wrap file-list" id="ajax-uploader-list">
                                <?php
                                $this->view('list', 'media')
                                    ->set('model', $this->comment)
                                    ->set('comment', $tmp)
                                    ->set('ticket', $this->row->get('id'))
                                    ->display();
                                ?>
                            </div>
                            <input
                                type="hidden"
                                name="tmp_dir"
                                id="comment-tmp_dir"
                                value="<?php echo $tmp; ?>"
                            />
                        </fieldset>

                        <div class="input-wrap">
                            <label for="comment-field-message">
                                <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?>
                                <?php
                                $mc = Event::trigger('hubzero.onGetMultiEntry', array(
                                    array(
                                        'members',   // The component to call
                                        'cc',        // Name of the input field
                                        'comment-field-message', // ID of the input field
                                        '',          // CSS class(es) for the input field
                                        implode(', ', $cc) // The value of the input field
                                    )
                                ));
                                if (count($mc) > 0) {
                                    $ccInstructions = Lang::txt(
                                        'COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE'
                                    );
                                    echo '<span class="hint">' . $ccInstructions . '</span>' . $mc[0];
                                } else {
                                    $ccInstructions = Lang::txt(
                                        'COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'
                                    );
                                    $ccValue = implode(', ', $cc);
                                    ?>
                                    <span class="hint"><?php echo $ccInstructions; ?></span>
                                    <input
                                        type="text"
                                        name="cc"
                                        id="comment-field-message"
                                        value="<?php echo $ccValue; ?>"
                                    />
                                <?php } ?>
                            </label>
                        </div>

                        <div class="grid">
                            <div class="col span6">
                                <div class="input-wrap">
                                    <label for="email_submitter">
                                        <input
                                            class="option"
                                            type="checkbox"
                                            name="email_submitter"
                                            id="email_submitter"
                                            value="1"
                                            checked="checked"
                                        />
                                        <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col span6">
                                <div class="input-wrap">
                                    <label for="email_owner">
                                        <input
                                            class="option"
                                            type="checkbox"
                                            name="email_owner"
                                            id="email_owner"
                                            value="1"
                                            checked="checked"
                                        />
                                        <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div><!-- / .ticket-content -->

                <fieldset class="ticket-details">
                    <div class="input-wrap">
                        <label for="tags">
                            <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_TAGS'); ?>
                            <?php
                            $tf = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                array(array('tags', 'tags', 'actags', '', $this->row->tags('string')))
                            );

                            if (count($tf) > 0) {
                                echo $tf[0];
                            } else { ?>
                                <input
                                    type="text"
                                    name="tags"
                                    id="tags"
                                    value="<?php echo $this->escape($this->row->tags('string', null)); ?>"
                                />
                            <?php } ?>
                        </label>
                    </div>

                    <div class="grid">
                        <div class="col span6">
                            <div class="input-wrap">
                                <label for="ticket-field-group">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:<br />
                                    <?php
                                    $group = '';
                                    if ($this->row->get('group_id')) {
                                        if ($g = \Hubzero\User\Group::getInstance($this->row->get('group_id'))) {
                                            $group = $g->get('cn');
                                        }
                                    }

                                    $gc = Event::trigger(
                                        'hubzero.onGetSingleEntryWithSelect',
                                        array(array(
                                            'groups', 'ticket[group_id]', 'acgroup', '', $group, '', 'owner'
                                        ))
                                    );
                                    if (count($gc) > 0) {
                                        echo $gc[0];
                                    } else { ?>
                                    <input
                                        type="text"
                                        name="ticket[group_id]"
                                        value="<?php echo $this->escape($this->row->get('group_id')); ?>"
                                        id="acgroup"
                                        size="30"
                                        autocomplete="off"
                                    />
                                    <?php } ?>
                                </label>
                            </div>
                        </div>
                        <div class="col span6">
                            <div class="input-wrap">
                                <label for="ticketowner">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER'); ?>
                                    <?php echo $this->lists['owner']; ?>
                                </label>
                            </div>
                        </div>
                    </div>

                <?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
                    <div class="input-wrap">
                        <label for="ticket-field-category">
                            <?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
                            <select name="ticket[category]" id="ticket-field-category">
                                <option value=""><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
                                <?php
                                foreach ($this->lists['categories'] as $category) {
                                    $catAlias    = $this->escape($category->alias);
                                    $catTitle    = $this->escape(stripslashes($category->title));
                                    $catSelected = ($category->alias == $this->row->get('category'))
                                        ? ' selected="selected"' : '';
                                    ?>
                                <option value="<?php echo $catAlias; ?>"<?php echo $catSelected; ?>>
                                    <?php echo $catTitle; ?>
                                </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </label>
                    </div>
                <?php } ?>

                    <div class="input-wrap">
                        <label for="target_date">
                            <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_TARGET_DATE'); ?>
                        </label>
                        <?php
                        $targetDate = $this->row->get('target_date');
                        $hasTarget  = ($targetDate && $targetDate != '0000-00-00 00:00:00');
                        $targetVal  = $hasTarget
                            ? $this->escape(Date::of($targetDate)->toLocal('Y-m-d H:i:s'))
                            : '';
                        echo Html::input(
                            'calendar',
                            'ticket[target_date]',
                            $targetVal,
                            array('id' => 'field-target_date')
                        );
                        ?>
                    </div>

                    <div class="grid">
                        <div class="col span6">
                            <div class="input-wrap">
                                <label for="ticket-field-severity">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEVERITY'); ?>
                                    <select name="ticket[severity]" id="ticket-field-severity">
                                        <?php
                                        $sevs = \Components\Support\Helpers\Utilities::getSeverities();
                                        foreach ($sevs as $severity) {
                                            $sevSelected = ($severity == $this->row->get('severity'))
                                                ? ' selected="selected"' : '';
                                            $sevLabel = Lang::txt(
                                                'COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)
                                            );
                                            ?>
                                            <option
                                                value="<?php echo $severity; ?>"
                                                <?php echo $sevSelected; ?>
                                            ><?php echo $sevLabel; ?></option>
                                        <?php } ?>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="col span6">
                            <div class="input-wrap">
                                <label for="ticket-field-status">
                                    <?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?>:
                                    <select name="ticket[status]" id="ticket-field-status">
                                        <optgroup label="<?php
                                            echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN');
                                        ?>">
                                            <?php
                                            foreach (
                                                \Components\Support\Models\Status::allOpen()->rows() as $status
                                            ) {
                                                $stId  = $status->get('id');
                                                $stSel = ($this->row->isOpen()
                                                    && $this->row->get('status') == $stId)
                                                    ? ' selected="selected"' : '';
                                                $stTitle = $this->escape($status->get('title'));
                                                ?>
                                                <option
                                                    value="<?php echo $stId; ?>"
                                                    <?php echo $stSel; ?>
                                                ><?php echo $stTitle; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                        <optgroup label="<?php
                                            echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED');
                                        ?>">
                                            <?php
                                            $closedSel = (!$this->row->isOpen()
                                                && $this->row->get('status') == 0)
                                                ? ' selected="selected"' : '';
                                            ?>
                                            <option
                                                value="0"
                                                <?php echo $closedSel; ?>
                                            ><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
                                            <?php
                                            foreach (
                                                \Components\Support\Models\Status::allClosed()->rows() as $status
                                            ) {
                                                $stId  = $status->get('id');
                                                $stSel = (!$this->row->isOpen()
                                                    && $this->row->get('status') == $stId)
                                                    ? ' selected="selected"' : '';
                                                $stTitle = $this->escape($status->get('title'));
                                                ?>
                                                <option
                                                    value="<?php echo $stId; ?>"
                                                    <?php echo $stSel; ?>
                                                ><?php echo $stTitle; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset><!-- / .ticket-details -->
            </div>
        </fieldset>
    <?php if (!$no_html) { ?>
    </div>
    <div class="col spn4">
        <p><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?></p>
    </div>
    </div>
    <?php } ?>

    <input type="hidden" name="started" value="<?php echo Date::toSql(); ?>" />

    <input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="username" value="<?php echo User::get('username'); ?>" />

    <?php if ($no_html) { ?>
        <p class="submit"><input type="submit" value="<?php echo Lang::txt('Save'); ?>" /></p>
        <input type="hidden" name="no_html" value="1" />
        <input type="hidden" name="task" value="apply" />
    </div>
    <?php } else { ?>
        <input type="hidden" name="task" value="save" />
    <?php } ?>

    <?php echo Html::input('token'); ?>
</form>
