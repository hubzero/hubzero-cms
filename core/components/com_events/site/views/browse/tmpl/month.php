<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     authorized: mixed,
 *     categories: mixed,
 *     day: mixed,
 *     fields: mixed,
 *     month: mixed,
 *     offset: mixed,
 *     rows: mixed,
 *     title: mixed,
 *     year: mixed,
 * } $this
 */

$this
     ->css()
     ->js();

$view = $this;

// The attribute that marks the current value in the form fields below
$categorySelected = function ($value) use ($view) {
    if ($view->get('category') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if ($this->authorized) { ?>
    <div id="content-header-extra">
        <ul id="useroptions">
            <?php
            $addUrl = Route::url('index.php?option=' . $this->option . '&task=add');
            ?>
            <li class="last">
                <a class="icon-add add btn" href="<?= $addUrl ?>"><?= Lang::txt('EVENTS_ADD_EVENT') ?></a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
    <?php } ?>
</header><!-- / #content-header -->

<nav>
    <ul class="sub-menu">
        <?php
        $linkUrl = Route::url('index.php?option=' . $this->option . '&year=' . $this->year);
        ?>
        <li<?= $this->task == 'year' ? ' class="active"' : '' ?>>
            <a href="<?= $linkUrl ?>"><span><?= Lang::txt('EVENTS_CAL_LANG_REP_YEAR') ?></span></a>
        </li>
        <?php
        $linkUrl = Route::url('index.php?option=' . $this->option . '&year=' . $this->year . '&month=' . $this->month);
        ?>
        <li<?= $this->task == 'month' ? ' class="active"' : '' ?>>
            <a rel="nofollow" href="<?= $linkUrl ?>"><span><?= Lang::txt('EVENTS_CAL_LANG_REP_MONTH') ?></span></a>
        </li>
        <?php
        $weekUrl = Route::url(
            'index.php?option=' . $this->option . '&year=' . $this->year . '&month=' . $this->month . '&day='
                . $this->day . '&task=week',
        );
        ?>
        <li<?= $this->task == 'week' ? ' class="active"' : '' ?>>
            <a rel="nofollow" href="<?= $weekUrl ?>"><span><?= Lang::txt('EVENTS_CAL_LANG_REP_WEEK') ?></span></a>
        </li>
        <?php
        $linkUrl = Route::url(
            'index.php?option=' . $this->option . '&year=' . $this->year . '&month=' . $this->month . '&day='
                . $this->day,
        );
        ?>
        <li<?= $this->task == 'day' ? ' class="active"' : '' ?>>
            <a rel="nofollow" href="<?= $linkUrl ?>"><span><?= Lang::txt('EVENTS_CAL_LANG_REP_DAY') ?></span></a>
        </li>
    </ul>
</nav>

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
        <p id="toggle-prior"><strong><a id="toggle-prior-anchor" href="#"
            onClick="return false;">Show Past Events</a></strong></p>
        <?php if (count($this->rows) > 0) { ?>
            <ul class="events">
                <?php
                foreach ($this->rows as $row) {
                    $this
                         ->view('item')
                         ->set('option', $this->option)
                         ->set('task', $this->task)
                         ->set('row', $row)
                         ->set('fields', $this->fields)
                         ->set('categories', $this->categories)
                         ->set('showdate', 1)
                         ->display();
                }
                ?>
            </ul>
        <?php } else { ?>
            <p class="warning">
                <?= Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' <strong>'
                    . \Components\Events\Helpers\Html::getDateFormat($this->year, $this->month, '', 3) . '</strong>' ?>
            </p>
        <?php } ?>
        </div><!-- / .subject -->
        <div class="aside">
        <?php
        $actionUrl = Route::url(
            'index.php?option=' . $this->option . '&year=' . $this->year . '&month=' . $this->month,
        );
        ?>
        <form action="<?= $actionUrl ?>" method="get" id="event-categories">
            <fieldset>
                <legend class="sr-only"><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></legend>
                <label for="event-category"><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></label>
                <div class="hz-input-combo">
                    <select name="category" id="event-category">
                        <option value=""><?php echo Lang::txt('EVENTS_ALL_CATEGORIES'); ?></option>
                    <?php
                    if ($this->categories) {
                        foreach ($this->categories as $id => $title) {
                            ?>
                        <option value="<?= $id ?>"<?= $categorySelected($id) ?>>
                            <?= $this->escape(stripslashes($title)) ?>
                        </option>
                            <?php
                        }
                    }
                    ?>
                    </select>
                    <input type="submit" value="<?php echo Lang::txt('EVENTS_GO'); ?>" />
                </div>
            </fieldset>
        </form>

        <div class="calendarwrap">
            <p class="datenav">
                <?php
                $this_date = new \Components\Events\Helpers\EventsDate();
                $this_date->setDate($this->year, $this->month, 0);

                $prev_year = clone($this_date);
                $prev_year->addMonths(-12);
                $next_year = clone($this_date);
                $next_year->addMonths(+12);
                $database = App::get('db');
                $sql = "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events` as e
								WHERE `scope`='event'
								AND `state`=1
								AND `approved`=1";
                $database->setQuery($sql);
                $rows = $database->loadObjectList();
                $first_event_time = new DateTime($rows[0]->min ? $rows[0]->min : '');
                $last_event_time = new DateTime($rows[0]->max ? $rows[0]->max : '');
                $this_datetime = new DateTime($this->year . '-01-01');
                //get a DateTime for the first day of the year and check if there's an event earlier
                if ($this_datetime > $first_event_time) {
                    $prev = Route::url('index.php?option=' . $this->option . '&' . $prev_year->toDateURL($this->task));
                    $prev_text = Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
                } else {
                    $prev = "javascript:void(0);";
                    $prev_text = Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' '
                        . Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
                }
                //get a DateTime for the first day of the next year and see if there's an event after
                $this_datetime->add(new DateInterval("P1Y"));
                if ($this_datetime <= $last_event_time) {
                    $next = Route::url('index.php?option=' . $this->option . '&' . $next_year->toDateURL($this->task));
                    $next_text = Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
                } else {
                    $next = "javascript:void(0);";
                    $next_text = Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
                }

                ?>
                <a class="prv" rel="nofollow" href="<?= $prev ?>" title="<?= $prev_text ?>"
                    aria-label="<?= $prev_text ?>">&lsaquo;</a>
                <a class="nxt" rel="nofollow" href="<?= $next ?>" title="<?= $next_text ?>"
                    aria-label="<?= $next_text ?>">&rsaquo;</a>
                <?php echo $this->year; ?>
            </p>
        </div><!-- / .calendarwrap -->

        <div class="calendarwrap">
            <?php
            $this
                 ->view('calendar')
                 ->set('option', $this->option)
                 ->set('task', $this->task)
                 ->set('year', $this->year)
                 ->set('month', $this->month)
                 ->set('day', $this->day)
                 ->set('offset', $this->offset)
                 ->set('shownav', 1)
                 ->display();
            ?>
        </div><!-- / .calendarwrap -->
    </div><!-- / .aside -->
    </div>
</section><!-- / .main section -->
