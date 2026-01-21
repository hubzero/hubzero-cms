<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));

$groupCn   = $this->group->get('cn');
$eventId   = $this->event->get('id');
$option    = $this->option;

$calendarBase = 'index.php?option=' . $option
    . '&cn=' . $groupCn . '&active=calendar';

$backUrl = Route::url(
    $calendarBase . '&year=' . $year . '&month=' . $month
);
$deleteUrl = Route::url(
    $calendarBase . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $calendarBase . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $calendarBase . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $calendarBase . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $calendarBase . '&action=registrants&event_id=' . $eventId
);

$isOwnerOrManager = $this->user->get('id') == $this->event->get('created_by')
    || $this->authorized == 'manager';

$hasRegistration = $this->event->get('registerby')
    && $this->event->get('registerby') != '0000-00-00 00:00:00';

$restrictedMsg = Lang::txt(
    'Registration is password protected.'
    . ' Please supply the password you were given'
    . ' with your invite to join the event.'
);
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <a
            class="icon-date btn date"
            title=""
            href="<?php echo $backUrl; ?>"
        >
            <?php echo Lang::txt('Back to Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->event->get('title'); ?>
    </span>
    <?php if ($isOwnerOrManager) : ?>
        <a
            class="delete"
            href="<?php echo $deleteUrl; ?>"
        >
            Delete
        </a>
        <a
            class="edit"
            href="<?php echo $editUrl; ?>"
        >
            Edit
        </a>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li>
            <a href="<?php echo $detailsUrl; ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>
        <?php if ($hasRegistration) : ?>
            <li class="active">
                <a href="<?php echo $registerUrl; ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($isOwnerOrManager) : ?>
                <li>
                    <a href="<?php echo $registrantsUrl; ?>">
                        <span><?php
                            $regCount = $this->registrants;
                            echo Lang::txt(
                                'Registrants (' . $regCount . ')'
                              );
                                ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<form
    action="<?php echo $registerUrl; ?>"
    id="hubForm"
    method="post"
>
    <fieldset>
        <legend><?php echo Lang::txt('Limited Registration'); ?></legend>
        <p class="info">
            <?php echo $restrictedMsg; ?>
        </p>
        <label>
            <?php echo Lang::txt('Password:'); ?>
            <span class="required">Required</span>
            <input type="password" name="passwrd" />
        </label>
    </fieldset>
    <input type="hidden" name="option" value="com_groups" />
    <input
        type="hidden"
        name="cn"
        value="<?php echo $groupCn; ?>"
    />
    <input type="hidden" name="active" value="calendar" />
    <input type="hidden" name="action" value="register" />
    <input
        type="hidden"
        name="event_id"
        value="<?php echo $eventId; ?>"
    />

    <p class="submit">
        <input type="submit" name="event_submit" value="Submit" />
    </p>
</form>
