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

$data = $this->data;
$pub  = $data->get('pub');

$details = $data->get('localPath');
$details .= $data->getSize() ? ' | ' . $data->getSize('formatted') : '';
if ($data->get('viewer') != 'freeze') {
    $details .= !$data->exists() ? ' | ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MISSING_FILE') : '';
}
?>
    <li>
        <span class="item-options">
            <?php if ($data->get('viewer') == 'edit') { ?>
            <span>
                <?php if ($data->exists()) { ?>
                <a href="<?php echo $data->get('downloadUrl'); ?>"
                    class="item-download"
                    title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a>
                <?php } ?>
                <?php
                    $editUrl = Route::url(
                        $pub->link('editversion') . '&action=edititem&aid='
                        . $data->get('id') . '&p=' . $data->get('props')
                    );
                    $deleteUrl = Route::url(
                        $pub->link('editversion') . '&action=deleteitem&aid='
                        . $data->get('id') . '&p=' . $data->get('props')
                    );
                    $relabelTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELABEL');
                    $removeTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE');
                ?>
                <a href="<?php echo $editUrl; ?>"
                    class="showinbox item-edit"
                    title="<?php echo $relabelTitle; ?>">&nbsp;</a>
                <a href="<?php echo $deleteUrl; ?>"
                    class="item-remove"
                    title="<?php echo $removeTitle; ?>">&nbsp;</a>
            </span>
            <?php } elseif ($data->exists()) { ?>
                <?php $downloadTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>
                <span><a href="<?php echo $data->get('downloadUrl'); ?>"
                    class="item-download"
                    title="<?php echo $downloadTitle; ?>">&nbsp;</a></span>
            <?php } ?>
        </span>
        <span class="item-title" id="<?php echo 'file-' . $data->get('id'); ?>">
            <?php echo $data::drawIcon($data->get('ext')); ?> <?php echo $data->get('title'); ?>
        </span>
        <span class="item-details"><?php echo $details; ?></span>
    </li>
