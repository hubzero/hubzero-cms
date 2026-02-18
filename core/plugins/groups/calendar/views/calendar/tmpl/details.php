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
$params = new \Hubzero\Config\Registry($this->event->get('params'));
$ignoreDst = $params->get('ignore_dst', 0) == 1 ? true : false;

$calBase = 'index.php?option=' . $this->option
    . '&cn=' . $this->group->get('cn')
    . '&active=calendar';
$eventId = $this->event->get('id');

$backUrl = Route::url(
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
$exportUrl = Route::url(
    $calBase . '&action=export&event_id=' . $eventId
);

$isPublished = $this->group->published == 1;
$isCreator = $this->user->get('id')
    == $this->event->get('created_by');
$isManager = $this->authorized == 'manager';
$canManage = $isPublished && ($isCreator || $isManager);
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <a class="icon-prev btn back"
            title=""
            href="<?php echo $backUrl; ?>">
            <?php echo Lang::txt('Back to Events Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->event->get('title'); ?>
        <?php if (isset($this->calendar)) : ?>
            <span>&ndash;&nbsp;<?php echo $this->calendar->get('title'); ?></span>
        <?php endif; ?>
    </span>
    <?php if ($canManage) : ?>
        <?php if (!isset($this->calendar) || !$this->calendar->get('readonly')) : ?>
            <a class="delete"
                href="<?php echo $deleteUrl; ?>">
                Delete
            </a>
            <a class="edit"
                href="<?php echo $editUrl; ?>">
                Edit
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li class="active">
            <a href="<?php echo $detailsUrl; ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>

        <?php if ($this->event->get('registerby') && $this->event->get('registerby') != '0000-00-00 00:00:00') : ?>
            <li>
                <a href="<?php echo $registerUrl; ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($isCreator || $isManager) : ?>
                <li>
                    <a href="<?php echo $registrantsUrl; ?>">
                        <span><?php echo Lang::txt('Registrants (' . $this->registrants . ')'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<table class="group-event-details">
    <tbody>
        <?php
            $timezone     = timezone_name_from_abbr('', $this->event->get('time_zone') * 3600);
            $publish_up   = $this->event->get('publish_up');
            $publish_down = $this->event->get('publish_down');
            $allday_event = $this->event->get('allday');

            // show alternative event start/ends
            // used for repeating events
            $start = Request::getInt('start', null, 'get');
            $end   = Request::getInt('end', null, 'get');

        if ($start || ($start && $end)) {
            $publish_up   = Date::of($start)->toSql();
            $publish_down = Date::of($end)->toSql();
        }
        ?>
        <?php if ($allday_event) : ?>
            <tr>
                <th class="date"></th>
                <td width="50%">
                    <?php
                        // check to see if its a single date all day event
                        $d1 = Date::of($publish_up);
                        $d2 = Date::of($publish_down)->modify('-24 hours');
                    if ($d1 == $d2 || !$publish_down || $publish_down == '0000-00-00 00:00:00') {
                        echo $d1->format('l, F d, Y', true);
                    } else {
                        echo $d1->format('l, F d, Y', true) . ' - ' . $d2->format('l, F d, Y', true);
                    }
                    ?>
                </td>
                <th class="time"></th>
                <td>
                    <?php echo Lang::txt('All Day Event'); ?>
                </td>
            </tr>
        <?php elseif ($publish_down && $publish_down != '0000-00-00 00:00:00') : ?>
            <tr>
                <th class="date"></th>
                <td colspan="3">
                    <?php
                    $dateFmt = 'l, F d, Y @ h:i a T';
                    $tz = $this->event->get('time_zone');
                    if ($tz) {
                        $startFormatted = Date::of($publish_up)
                            ->toTimezone($tz, $dateFmt, $ignoreDst);
                        $endFormatted = Date::of($publish_down)
                            ->toTimezone($tz, $dateFmt, $ignoreDst);
                    } else {
                        $startFormatted = Date::of($publish_up)
                            ->toLocal($dateFmt);
                        $endFormatted = Date::of($publish_down)
                            ->toLocal($dateFmt);
                    }
                    ?>
                    <?php echo $startFormatted; ?>
                    &mdash;
                    <?php echo $endFormatted; ?>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <th class="date"></th>
                <td width="50%">
                    <?php echo Date::of($publish_up, $this->event->get('time_zone'))->format('l, F d, Y', true); ?>
                </td>
                <th class="time"></th>
                <td>
                    <?php echo Date::of($publish_up, $this->event->get('time_zone'))->format('g:i a T', true); ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('repeating_rule') != '') : ?>
            <tr>
                <th class="repeatig"></th>
                <td colspan="3"><?php echo $this->event->humanReadableRepeatingRule(); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('adresse_info') != '') : ?>
            <tr>
                <th class="location"></th>
                <td colspan="3"><?php echo $this->event->get('adresse_info'); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('contact_info') != '') : ?>
            <tr>
                <th class="author"></th>
                <td colspan="3">
                    <?php echo Plugins\Groups\Calendar\Helper::autoLinkText(
                        $this->event->get('contact_info')
                    ); ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('extra_info') != '') : ?>
            <tr>
                <th class="url"></th>
                <td colspan="3">
                    <a href="<?php echo $this->event->get('extra_info'); ?>" rel="external">
                        <?php echo $this->event->get('extra_info'); ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('content') != '') : ?>
            <tr>
                <th class="details"></th>
                <td colspan="3">
                    <?php echo Plugins\Groups\Calendar\Helper::autoLinkText(
                        nl2br($this->event->get('content'))
                    ); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th class="download"></th>
            <td colspan="4">
                <a class="btn"
                    href="<?php echo $exportUrl; ?>">
                    <?php echo Lang::txt('Export to My Calendar (ics)'); ?>
                </a>
            </td>
        </tr>
    </tbody>
</table>
