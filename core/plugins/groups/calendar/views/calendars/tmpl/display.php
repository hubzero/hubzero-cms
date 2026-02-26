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
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->group->published == 1) { ?>
    <?php
    $backUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->cn
        . '&active=calendar&year=' . $this->year
        . '&month=' . $this->month
    );
    $addCalUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->cn
        . '&active=calendar&action=addcalendar'
    );
    ?>
    <ul id="page_options">
        <li>
            <a
                class="icon-prev btn back"
                title=""
                href="<?php echo $backUrl; ?>"
            >
                <?php echo Lang::txt('Back to Events Calendar'); ?>
            </a>
            <a
                class="icon-add btn add"
                title=""
                href="<?php echo $addCalUrl; ?>"
            >
                <?php echo Lang::txt('Add Calendar'); ?>
            </a>

        </li>
    </ul>
<?php } ?>

<table class="group-calendars">
    <thead>
        <tr>
            <th><?php echo Lang::txt('Name'); ?></th>
            <th><?php echo Lang::txt('Color'); ?></th>
            <th><?php echo Lang::txt('Publish Events?'); ?></th>
            <th><?php echo Lang::txt('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($this->calendars) > 0) : ?>
            <?php foreach ($this->calendars as $calendar) : ?>
                <tr>
                    <td><?php echo $calendar->get('title'); ?></td>
                    <td>
                        <?php
                        $colors = array('red','orange','yellow','green','blue','purple','brown');
                        if (!in_array($calendar->get('color'), $colors)) {
                            $calendar->set('color', '');
                        }
                        ?>
                        <?php
                        $imgBase = Request::base(true)
                            . '/core/plugins/groups/calendar/assets/img/swatch-';
                        ?>
                        <?php if ($calendar->get('color')) : ?>
                            <?php $colorSrc = $imgBase . $calendar->get('color') . '.png'; ?>
                            <img
                                src="<?php echo $colorSrc; ?>"
                                alt="<?php echo $calendar->get('color'); ?>"
                            />
                        <?php else : ?>
                            <img
                                src="<?php echo $imgBase; ?>gray.png"
                                alt="gray"
                            />
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if ($calendar->get('published') == 1) {
                            echo '<span class="yes">Yes</span>';
                        } else {
                            echo '<span class="no">No</span>';
                        }
                        ?>
                    </td>
                    <?php
                    $calId = $calendar->get('id');
                    $calBase = 'index.php?option=' . $this->option
                        . '&cn=' . $this->group->cn
                        . '&active=calendar';
                    $editUrl = Route::url(
                        $calBase . '&action=editcalendar&calendar_id=' . $calId
                    );
                    $refreshUrl = Route::url(
                        $calBase . '&action=refreshcalendar&calendar_id=' . $calId
                    );
                    $deleteUrl = Route::url(
                        $calBase . '&action=deletecalendar&calendar_id=' . $calId
                    );
                    ?>
                    <td>
                        <a
                            class="edit"
                            href="<?php echo $editUrl; ?>"
                        >
                            Edit
                        </a> &nbsp;|
                        <a class="delete" href="javascript:void(0);">
                            Delete
                        </a>
                        <?php if ($calendar->get('url')) : ?>
                             &nbsp;|
                            <a
                                class="refresh"
                                href="<?php echo $refreshUrl; ?>"
                            >
                                Refresh
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="delete-confirm">
                    <td colspan="4">
                        <form
                            action="<?php echo $deleteUrl; ?>"
                            method="post"
                        >
                            <h3>Delete Calendar</h3>
                            <p>What do you want to do with the events associated with this calendar?</p>
                            <select name="events">
                                <option value="keep">Delete Calendar &amp; Set Events as Uncategorized</option>
                                <option value="delete">Delete Calendar &amp; Delete Events</option>
                            </select>
                            <input class="btn btn-danger" type="submit" value="Delete" />
                            <input class="btn btn-secondary delete-cancel" type="reset" value="Cancel" />
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <?php if ($calendar->get('url')) : ?>
                            <span class="calendar-url">
                                <span>Calendar URL:</span>
                                <?php echo $calendar->get('url'); ?>
                            </span>
                            <br />
                            <span class="calendar-url">
                                <span>Last Fetched:</span>
                                <?php
                                if (
                                    !$calendar->get('last_fetched') || $calendar->get('last_fetched') == '0000-00-00
                                00:00:00'
                                ) {
                                    echo 'Never';
                                } else {
                                    echo Date::of($calendar->get('last_fetched'))->toLocal('m/d/Y @ g:ia');
                                }
                                ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4">Currently there are no calendars for this group.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>