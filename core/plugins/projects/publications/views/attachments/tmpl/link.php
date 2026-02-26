<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$details = $row->title ? $row->path : null;
$viewer  = $this->data->viewer;

?>
    <li>
        <span class="item-options">
        <?php if ($viewer == 'edit') { ?>
            <?php
                $editItemUrl = Route::url(
                    $data->editUrl . '&action=edititem&aid='
                    . $data->id . '&p=' . $data->props
                );
                $deleteItemUrl = Route::url(
                    $data->editUrl . '&action=deleteitem&aid='
                    . $data->id . '&p=' . $data->props
                );
                $editTitle = strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT_LINK_TITLE'));
                $removeTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE');
            ?>
            <span>
                <a href="<?php echo $editItemUrl; ?>"
                    class="showinbox item-edit"
                    title="<?php echo $editTitle; ?>">&nbsp;</a>
                <a href="<?php echo $deleteItemUrl; ?>"
                    class="item-remove"
                    title="<?php echo $removeTitle; ?>">&nbsp;</a>
            </span>
        <?php } ?>
        </span>
        <span class="item-title link-type">
             <a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
            <span class="item-details"><?php echo $details; ?></span>
        </span>
    </li>