<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$userLocalizer = new Plugins\Groups\Calendar\Helpers\UserLocalizer();
$timezone = $userLocalizer->getTimezone();
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php
$isPublishedMember = $this->group->published == 1
    && in_array(User::get('id'), $this->members);
?>
<?php if ($isPublishedMember) : ?>
    <?php
    $addTitle = Lang::txt('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT');
    $addUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->cn
        . '&active=calendar&action=add'
    );
    ?>
    <ul id="page_options">
        <li>
            <a
                class="icon-add btn add"
                title="<?php echo $addTitle; ?>"
                href="<?php echo $addUrl; ?>"
            >
                <?php echo $addTitle; ?>
            </a>
            <?php if ($this->authorized == 'manager') : ?>
                <?php
                $manageTitle = Lang::txt('Manage Calendars');
                $manageUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&cn=' . $this->group->cn
                    . '&active=calendar&action=calendars'
                );
                ?>
                <a
                    class="icon-date btn date"
                    title="<?php echo $manageTitle; ?>"
                    href="<?php echo $manageUrl; ?>"
                >
                    <?php echo $manageTitle; ?>
                </a>
            <?php endif; ?>
        </li>
    </ul>
<?php endif; ?>

<?php
$canQuickCreate = $this->params->get('allow_quick_create', 1)
    && in_array(User::get('id'), $this->group->get('members'));
$quickCreate = $canQuickCreate ? true : 0;
$calendarBase = Route::url(
    'index.php?option=com_groups&cn='
    . $this->group->get('cn') . '&active=calendar'
);
?>
<div id="calendar"
    data-base="<?php echo $calendarBase; ?>"
    data-month="<?php echo $this->month; ?>"
    data-year="<?php echo $this->year; ?>"
    data-event-quickcreate="<?php echo $quickCreate; ?>"></div>

<select name="calendar" id="calendar-picker">
    <option value="0"><?php echo Lang::txt('All Calendars'); ?></option>
    <?php foreach ($this->calendars as $calendar) : ?>
        <?php
        $sel = ($calendar->get('id') == $this->calendar)
            ? 'selected="selected"' : '';
        $color = $calendar->get('color')
            ? strtolower($calendar->get('color'))
            : 'gray';
        $imgBase = Request::base(true);
        $imgPath = $imgBase
            . '/core/plugins/groups/calendar/assets/img/swatch-'
            . $color . '.png';
        ?>
        <option
            <?php echo $sel; ?>
            data-img="<?php echo $imgPath; ?>"
            value="<?php echo $calendar->get('id'); ?>"
            class="calendar-picker-option"
        ><?php echo $calendar->get('title'); ?></option>
    <?php endforeach; ?>
</select>


<div class="subject group-calendar-subject event-list">
    <div class="container">
        <h3><?php echo Lang::txt('Events List'); ?></h3>
        <?php if ($this->eventsCount > 0) : ?>
            <ol class="calendar-entries">
                <?php foreach ($this->events as $event) : ?>
                    <?php
                        $params = new \Hubzero\Config\Registry($event->get('params'));
                        $ignoreDst = false;
                        $ignoreDst = $params->get('ignore_dst') == 1 ? true : false;
                    ?>
                    <li>
                        <h4 class="entry-title">
                            <a href="<?php echo $event->link(); ?>">
                                <?php echo $event->get('title'); ?>
                            </a>
                        </h4>
                        <dl class="entry-meta">
                            <?php
                            $calendarName = $event->calendar()->get('id')
                                ? $event->calendar()->get('title')
                                : 'Uncategorized';
                            $hasPublishDown = $event->get('publish_down')
                                && $event->get('publish_down')
                                    != '0000-00-00 00:00:00';
                            ?>
                            <dd class="calendar">
                                in <?php echo $calendarName; ?>
                            </dd>
                            <?php if ($hasPublishDown) : ?>
                                <dd class="start-and-end">
                                    <?php
                                        echo Date::of(
                                            $event->get('publish_up')
                                        )->toTimezone(
                                            $timezone,
                                            'l, F d, Y @ g:i a',
                                            $ignoreDst
                                        );
                                    ?>
                                    &mdash;
                                    <?php
                                        echo Date::of(
                                            $event->get('publish_down')
                                        )->toTimezone(
                                            $timezone,
                                            'l, F d, Y @ g:i a',
                                            $ignoreDst
                                        );
                                    ?>
                                </dd>
                            <?php else : ?>
                                <dd class="date">
                                    <?php
                                        echo Date::of(
                                            $event->get('publish_up')
                                        )->toTimezone(
                                            $timezone,
                                            'l, F d, Y @ g:i a',
                                            $ignoreDst
                                        );
                                    ?>
                                <dd>
                                <dd class="time">
                                    <?php
                                        echo Date::of(
                                            $event->get('publish_up')
                                        )->toTimezone(
                                            $timezone,
                                            'l, F d, Y @ g:i a',
                                            $ignoreDst
                                        );
                                    ?>
                                <dd>
                            <?php endif; ?>
                        </dl>
                        <div class="entry-content">
                            <p>
                                <?php
                                    $content = strip_tags($event->get('content'));
                                    echo ($content) ? Hubzero\Utility\Str::truncate($content, 500) : '<em>no
                                    content</em>';
                                ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>

            <?php
                $pageNav = $this->pagination(
                    $this->eventsCount,
                    $this->filters['start'],
                    $this->filters['limit']
                );
                $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
                $pageNav->setAdditionalUrlParam('active', 'calendar');
                echo $pageNav->render();
            ?>
        <?php else : ?>
            <p class="warning"><?php echo Lang::txt('PLG_GROUPS_CALENDAR_NO_ENTRIES_FOUND'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
if ($this->params->get('allow_subscriptions', 1)) {
    $this->view('subscribe')
        ->set('calendar', $this->calendar)
        ->set('calendars', $this->calendars)
        ->set('group', $this->group)
        ->display();
}
