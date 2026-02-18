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
$viewer  = $this->data->viewer;

$db = \App::get('db');
$version = new \Components\Publications\Tables\Version($db);
$version->load($row->object_id);

$row->path = Route::url('index.php?option=com_publications&id=' . $version->publication_id . '&v=' .
$version->version_number); //$row->object_id);
$details = rtrim(Request::base(), '/') . '/' . ltrim($row->path, '/');
?>
    <li>
        <span class="item-options">
            <?php if ($viewer == 'edit') { ?>
                <span>
                    <?php
                        $aidProps = '&aid=' . $data->id . '&p=' . $data->props;
                        $downUrl = Route::url($data->editUrl . '&action=orderdown' . $aidProps);
                        $upUrl = Route::url($data->editUrl . '&action=orderup' . $aidProps);
                        $delUrl = Route::url($data->editUrl . '&action=deleteitem' . $aidProps);
                        $downTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEDOWN');
                        $upTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEUP');
                        $removeTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE');
                    ?>
                    <a href="<?php echo $downUrl; ?>"
                        class="item-movedown"
                        title="<?php echo $downTitle; ?>">&darr;</a>
                    <a href="<?php echo $upUrl; ?>"
                        class="item-moveup"
                        title="<?php echo $upTitle; ?>">&uarr;</a>
                    <a href="<?php echo $delUrl; ?>"
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