<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));
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
        <a class="icon-date btn date" title="" href="<?= $linkUrl ?>">
            <?php echo Lang::txt('Back to Calendar'); ?>
        </a>
    </li>
</ul>

<div class="event-title-bar">
    <span class="event-title">
        <?php echo $this->escape($this->event->title); ?>
    </span>
    <?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
        <?php
        $deleteUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                . '&active=calendar&action=delete&event_id=' . $this->event->id,
        );
        ?>
        <button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>" class="delete">Delete</button>
        <?php
        $editUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                . '&active=calendar&action=edit&event_id=' . $this->event->id,
        );
        ?>
        <a class="edit" href="<?= $editUrl ?>">
            Edit
        </a>
    <?php endif; ?>
</div>

<div class="event-sub-menu">
    <ul>
        <li>
            <?php
            $detailsUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                    . '&active=calendar&action=details&event_id=' . $this->event->id,
            );
            ?>
            <a href="<?= $detailsUrl ?>">
                <span><?php echo Lang::txt('Details'); ?></span>
            </a>
        </li>
        <?php
        if (
            isset($this->event->registerby)
            && $this->event->registerby
            && $this->event->registerby != '0000-00-00 00:00:00'
        ) : ?>
            <li>
                <?php
                $registerUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=register&event_id=' . $this->event->id,
                );
                ?>
                <a href="<?= $registerUrl ?>">
                    <span><?php echo Lang::txt('Register'); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
            <li class="active">
                <?php
                $registrantsUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=registrants&event_id=' . $this->event->id,
                );
                ?>
                <a href="<?= $registrantsUrl ?>">
                    <span><?php echo Lang::txt('Registrants (' . count($this->registrants) . ')'); ?></span>
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
                <?php
                $downloadUrl = Route::url(
                    'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                        . '&active=calendar&action=download&event_id=' . $this->event->id,
                );
                ?>
                <a href="<?= $downloadUrl ?>">Download Registrants (.csv)</a>
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
                    <td>
                        <?= $this->escape($registrant->last_name) . ', ' . $this->escape($registrant->first_name) ?>
                    </td>
                    <td><?php echo $this->escape($registrant->email); ?></td>
                    <td><?php echo Date::of($registrant->registered)->toLocal('l, F d, Y @ g:i a'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">Currently there are no event registrants.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
