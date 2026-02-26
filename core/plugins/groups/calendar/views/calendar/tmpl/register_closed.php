<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));

$calBase = 'index.php?option=' . $this->option
    . '&cn=' . $this->group->get('cn')
    . '&active=calendar';
$eventId = $this->event->get('id');

$calendarUrl = Route::url(
    'index.php?option=' . $this->option
    . '&cn=' . $this->group->cn
    . '&active=calendar&year=' . $year
    . '&month=' . $month
);
$deleteUrl = Route::url(
    $calBase . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $calBase . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $calBase . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $calBase . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $calBase . '&action=registrants&event_id=' . $eventId
);

$isOwnerOrManager = $this->user->get('id') == $this->event->get('created_by')
    || $this->authorized == 'manager';
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <a class="icon-date btn date"
            title=""
            href="<?php echo $calendarUrl; ?>">
            <?php echo Lang::txt('Back to Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->event->get('title'); ?>
    </span>
    <?php if ($isOwnerOrManager) : ?>
        <a class="delete" href="<?php echo $deleteUrl; ?>">
            Delete
        </a>
        <a class="edit" href="<?php echo $editUrl; ?>">
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
        <?php
        $registerby = $this->event->get('registerby');
        $hasRegistration = $registerby
            && $registerby != '0000-00-00 00:00:00';
        ?>
        <?php if ($hasRegistration) : ?>
            <li class="active">
                <a href="<?php echo $registerUrl; ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($isOwnerOrManager) : ?>
                <li>
                    <a href="<?php echo $registrantsUrl; ?>">
                        <?php
                        $registrantsLabel = Lang::txt(
                            'Registrants (' . $this->registrants . ')'
                        );
                        ?>
                        <span><?php echo $registrantsLabel; ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<form action="index.php" id="hubForm">
    <p class="warning">
        <?php echo Lang::txt('Registration is closed for this event.'); ?>
    </p>
</form>