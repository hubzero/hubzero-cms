<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$viewer  = $this->data->viewer;

$db = App::get('db');
$version = new \Components\Publications\Tables\Version($db);
$version->load($row->object_id);

$row->path = Route::url('index.php?option=com_publications&id=' . $version->publication_id . '&v=' . $version->version_number); //$row->object_id);
$details = rtrim(Request::base(), '/') . '/' . ltrim($row->path, '/');
?>
    <li>
        <span class="item-options">
            <?php if ($viewer == 'edit') { ?>
                <span>
                    <?php
                    $orderdownUrl = Route::url(
                        $data->editUrl . '&action=orderdown&aid=' . $data->id . '&p=' . $data->props,
                    );
                    ?>
                    <a href="<?= $orderdownUrl ?>" class="item-movedown" title="<?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEDOWN') ?>">&darr;</a>
                    <?php
                    $orderupUrl = Route::url(
                        $data->editUrl . '&action=orderup&aid=' . $data->id . '&p=' . $data->props,
                    );
                    ?>
                    <a href="<?= $orderupUrl ?>" class="item-moveup" title="<?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEUP') ?>">&uarr;</a>
                    <?php
                    $deleteitemUrl = Route::url(
                        $data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props,
                    );
                    ?>
                    <button type="submit" form="hz-post-action" formaction="<?= $deleteitemUrl ?>" class="item-remove" title="<?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE') ?>">&nbsp;</button>
                </span>
            <?php } ?>
        </span>
        <span class="item-title link-type">
            <a href="<?php echo $row->path; ?>" rel="external"><?php echo $this->escape($title); ?></a>
            <span class="item-details"><?php echo $this->escape($details); ?></span>
        </span>
    </li>