<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));

$groupCn   = $this->group->get('cn');
$eventId   = $this->event->id;
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
$downloadUrl = Route::url(
    $calendarBase . '&action=download&event_id=' . $eventId
);

$isOwnerOrManager = $this->user->get('id') == $this->event->created_by
    || $this->authorized == 'manager';

$hasRegistration = isset($this->event->registerby)
    && $this->event->registerby
    && $this->event->registerby != '0000-00-00 00:00:00';

$registrantCount = count($this->registrants);
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
        <?php echo $this->event->title; ?>
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
            <li>
                <a href="<?php echo $registerUrl; ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($isOwnerOrManager) : ?>
            <li class="active">
                <a href="<?php echo $registrantsUrl; ?>">
                    <span><?php
                        echo Lang::txt(
                            'Registrants (' . $registrantCount . ')'
                          );
                            ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<table class="group-registrants">
    <thead>
        <tr>
            <th colspan="3">
                <a href="<?php echo $downloadUrl; ?>">
                    Download Registrants (.csv)
                </a>
            </th>
        </tr>
        <tr>
            <th><?php echo Lang::txt('Name'); ?></th>
            <th><?php echo Lang::txt('Email'); ?></th>
            <th><?php echo Lang::txt('Register Date'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($this->registrants) > 0) : ?>
            <?php foreach ($this->registrants as $registrant) : ?>
                <tr>
                    <td><?php
                        echo $registrant->last_name
                            . ', '
                            . $registrant->first_name;
                    ?></td>
                    <td><?php echo $registrant->email; ?></td>
                    <td><?php
                        echo Date::of($registrant->registered)
                            ->toLocal('l, F d, Y @ g:i a');
                    ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">
                    Currently there are no event registrants.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
