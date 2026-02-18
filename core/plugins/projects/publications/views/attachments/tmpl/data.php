<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$details = $row->title ? $row->object_name : null;
$viewer  = $this->data->viewer;

?>
    <li>
        <span class="item-options">
        <?php if ($viewer == 'edit') { ?>
            <span>
                <?php
                $deleteUrl = Route::url(
                    $data->editUrl . '&action=deleteitem&aid='
                    . $data->id . '&p=' . $data->props
                );
                ?>
                <a href="<?php echo $deleteUrl; ?>"
                    class="item-remove"
                    title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>"
                >&nbsp;</a>
            </span>
        <?php } ?>
        </span>
        <span class="item-title data-type">
             <a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
            <span class="item-details"><?php echo $details; ?></span>
        </span>
    </li>