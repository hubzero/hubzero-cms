<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } else { ?>
    <div class="latest_events_tbl">
        <?php if (count($this->events) > 0) { ?>
            <?php
            $cls = 'even';
            foreach ($this->events as $event) {
                $cls = ($cls == 'even') ? 'odd' : 'even';
                ?>
                <div class="<?php echo $cls; ?>">
                    <div class="event-date">
                        <span class="month"><?php echo Date::of($event->publish_up)->toLocal('M'); ?></span>
                        <span class="day"><?php echo Date::of($event->publish_up)->toLocal('d'); ?></span>
                    </div>
                    <div class="event-title">
                        <?php
                        $eventUrl = Route::url(
                            'index.php?option=com_events&task=details&id=' . $event->id
                        );
                        ?>
                        <a href="<?php echo $eventUrl; ?>">
                            <?php echo $this->escape(html_entity_decode(stripslashes($event->title))); ?>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        <?php } else { ?>
            <div class="odd">
                <p class="mod_events_latest_noevents"><?php echo Lang::txt('MOD_EVENTS_LATEST_NONE_FOUND'); ?></p>
            </div>
        <?php } ?>
    </div>
    <p class="more">
        <?php
        $eventsUrl = Route::url(
            'index.php?option=com_events&year=' . Date::of('now')->format('Y')
            . '&month=' . Date::of('now')->format('m')
        );
        ?>
        <a href="<?php echo $eventsUrl; ?>">
            <?php echo Lang::txt('MOD_EVENTS_LATEST_MORE'); ?>
        </a>
    </p>
<?php }
