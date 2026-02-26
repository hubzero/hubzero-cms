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
            <span>
                <a href="<?php echo Route::url($data->editUrl . '&action=edititem&aid=' . $data->id . '&p=' . $data->props); ?>" class="showinbox item-edit" title="<?php echo strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT_LINK_TITLE')); ?>">&nbsp;</a>
                <button type="submit" form="hz-post-action" formaction="<?php echo Route::url($data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</button>
            </span>
        <?php } ?>
        </span>
        <span class="item-title link-type">
             <a href="<?php echo (preg_match('/^https?:\/\//i', $row->path) ? $this->escape($row->path) : ''); ?>" rel="external"><?php echo $this->escape($title); ?></a>
            <span class="item-details"><?php echo $this->escape($details); ?></span>
        </span>
    </li>