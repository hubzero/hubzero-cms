<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     authorized: mixed,
 *     calendar: mixed,
 *     event: mixed,
 *     group: mixed,
 *     option: mixed,
 *     registrants: mixed,
 *     user: mixed,
 * } $this
 */

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));
$params = new \Hubzero\Config\Registry($this->event->get('params'));
$ignoreDst = $params->get('ignore_dst', 0) == 1 ? true : false;
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <?php
        $linkUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=calendar&year=' . $year
                . '&month=' . $month,
        );
        ?>
        <a class="icon-prev btn back" title="" href="<?= $linkUrl ?>">
            <?php echo Lang::txt('Back to Events Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->escape($this->event->get('title')); ?>
        <?php if (isset($this->calendar)) : ?>
            <span>&ndash;&nbsp;<?php echo $this->escape($this->calendar->get('title')); ?></span>
        <?php endif; ?>
    </span>
    <?php
    if (
        $this->group->published == 1
        && ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager')
    ) : ?>
        <?php if (!isset($this->calendar) || !$this->calendar->get('readonly')) : ?>
            <?php
            $deleteUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                    . '&active=calendar&action=delete&event_id=' . $this->event->get('id'),
            );
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>" class="delete">Delete</button>
            <?php
            $editUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                    . '&active=calendar&action=edit&event_id=' . $this->event->get('id'),
            );
            ?>
            <a class="edit" href="<?= $editUrl ?>">
                Edit
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li class="active">
            <?php
            $detailsUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                    . '&active=calendar&action=details&event_id=' . $this->event->get('id'),
            );
            ?>
            <a href="<?= $detailsUrl ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>

        <?php if ($this->event->get('registerby') && $this->event->get('registerby') != '0000-00-00 00:00:00') : ?>
            <li>
                <?php
                $registerUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=register&event_id=' . $this->event->get('id'),
                );
                ?>
                <a href="<?= $registerUrl ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
            <?php if ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
                <li>
                    <?php
                    $registrantsUrl = Route::url(
                        'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                            . '&active=calendar&action=registrants&event_id=' . $this->event->get('id'),
                    );
                    ?>
                    <a href="<?= $registrantsUrl ?>">
                        <span><?php echo Lang::txt('Registrants (' . $this->registrants . ')'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="clear"></div>
</div>

<table class="group-event-details" role="presentation">
    <tbody>
        <?php
            $ignoreDst    = false;
            $timezone     = $this->event->get('time_zone') ? $this->event->get('time_zone') : \Config::get('offset');
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
                <th scope="row" class="date"><span class="sr-only visually-hidden">Date:</span></th>
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
                <th scope="row" class="time"><span class="sr-only visually-hidden">Time:</span></th>
                <td>
                    <?php echo Lang::txt('All Day Event'); ?>
                </td>
            </tr>
        <?php elseif ($publish_down && $publish_down != '0000-00-00 00:00:00') : ?>
            <tr>
                <th scope="row" class="date"><span class="sr-only visually-hidden">Date:</span></th>
                <td colspan="3">
                    <?= $this->event->get('time_zone')
                        ? Date::of($publish_up)->toTimezone(
                            $this->event->get('time_zone'),
                            'l, F d, Y @ h:i a T',
                            $ignoreDst,
                        )
                        : Date::of($publish_up)->toLocal('l, F d, Y @ h:i a T') ?>
                    &mdash;
                    <?= $this->event->get('time_zone')
                        ? Date::of($publish_down)->toTimezone(
                            $this->event->get('time_zone'),
                            'l, F d, Y @ h:i a T',
                            $ignoreDst,
                        )
                        : Date::of($publish_down)->toLocal('l, F d, Y @ h:i a T') ?>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <th scope="row" class="date"><span class="sr-only visually-hidden">Date:</span></th>
                <td width="50%">
                    <?php echo Date::of($publish_up)->toTimezone($timezone, 'l, F d, Y'); ?>
                </td>
                <th scope="row" class="time"><span class="sr-only visually-hidden">Time:</span></th>
                <td>
                    <?php echo Date::of($publish_up)->toTimezone($timezone, 'g:i a T'); ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('repeating_rule') != '') : ?>
            <tr>
                <th scope="row" class="repeatig"><span class="sr-only visually-hidden">Repeats:</span></th>
                <td colspan="3"><?php echo $this->event->humanReadableRepeatingRule(); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('adresse_info') != '') : ?>
            <tr>
                <th scope="row" class="location"><span class="sr-only visually-hidden">Location:</span></th>
                <td colspan="3"><?php echo $this->escape($this->event->get('adresse_info')); ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('contact_info') != '') : ?>
            <tr>
                <th scope="row" class="author"><span class="sr-only visually-hidden">Contact:</span></th>
                <td colspan="3">
                    <?= Plugins\Groups\Calendar\Helper::autoLinkText(
                        $this->escape($this->event->get('contact_info')),
                    ) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('extra_info') != '') : ?>
            <tr>
                <th scope="row" class="url"><span class="sr-only visually-hidden">Website:</span></th>
                <td colspan="3">
                    <?php
                    $linkUrl = preg_match('#^https?://#i', $this->event->get('extra_info'))
                        ? $this->escape($this->event->get('extra_info'))
                        : '';
                    ?>
                    <a href="<?= $linkUrl ?>" rel="external">
                        <?php echo $this->escape($this->event->get('extra_info')); ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($this->event->get('content') != '') : ?>
            <tr>
                <th scope="row" class="details"><span class="sr-only visually-hidden">Details:</span></th>
                <td colspan="3">
                    <?= Plugins\Groups\Calendar\Helper::autoLinkText(
                        nl2br($this->escape($this->event->get('content'))),
                    ) ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <th scope="row" class="download"><span class="sr-only visually-hidden">Download:</span></th>
            <td colspan="4">
                <?php
                $exportUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=export&event_id=' . $this->event->get('id'),
                );
                ?>
                <a class="btn" href="<?= $exportUrl ?>"><?= Lang::txt('Export to My Calendar (ics)') ?></a>
            </td>
        </tr>
    </tbody>
</table>
