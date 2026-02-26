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

// Get settings
$suffix = isset($this->config->params->thumbSuffix) && $this->config->params->thumbSuffix
        ? $this->config->params->thumbSuffix : '-tn';

$format = isset($this->config->params->thumbFormat) && $this->config->params->thumbFormat
        ? $this->config->params->thumbFormat : 'png';

$thumbName = \Components\Projects\Helpers\Html::createThumbName(basename($data->get('fpath')), $suffix, $format);

$filePath = Route::url($pub->link('versionid')) . '/Image:' . urlencode(basename($data->get('fpath')));
$thumbSrc = Route::url($pub->link('versionid')) . '/Image:' . urlencode($thumbName);

// Is this image used for publication thumbail?
$class = $data->get('pubThumb') == 1 ? ' starred' : '';
$over  = $data->get('pubThumb') == 1 ? ' title="' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_IMAGE_DEFAULT') . '"' : '';

?>
    <li class="image-container">
        <span class="item-options">
            <?php if ($data->get('viewer') == 'edit') {
                $editBase = $pub->link('editversion')
                    . '&aid=' . $data->get('id')
                    . '&p=' . $data->get('props');
                $defaultUrl = Route::url(
                    $editBase . '&action=saveitem&makedefault=1'
                );
                $editUrl = Route::url(
                    $editBase . '&action=edititem'
                );
                $deleteUrl = Route::url(
                    $editBase . '&action=deleteitem'
                );
                $defaultTitle = Lang::txt(
                    'PLG_PROJECTS_PUBLICATIONS_IMAGE_MAKE_DEFAULT'
                );
                $editTitle = Lang::txt(
                    'PLG_PROJECTS_PUBLICATIONS_RELABEL'
                );
                $removeTitle = Lang::txt(
                    'PLG_PROJECTS_PUBLICATIONS_REMOVE'
                );
                ?>
            <span>
                <?php if (!$data->get('pubThumb')) { ?>
                <a href="<?php echo $defaultUrl; ?>"
                    class="item-default"
                    title="<?php echo $defaultTitle; ?>"
                    >&nbsp;</a>
                <?php } ?>
                <a href="<?php echo $editUrl; ?>"
                    class="showinbox item-edit"
                    title="<?php echo $editTitle; ?>"
                    >&nbsp;</a>
                <a href="<?php echo $deleteUrl; ?>"
                    class="item-remove"
                    title="<?php echo $removeTitle; ?>"
                    >&nbsp;</a>
            </span>
            <?php } ?>
        </span>
        <span class="item-image<?php echo $class; ?>"
            <?php echo $over; ?>
            ><a class="more-content" href="<?php echo $filePath; ?>"
            ><img alt="" src="<?php echo $thumbSrc; ?>" /></a></span>
        <span class="item-title">
            <?php echo $data->get('title'); ?></span>
        <span class="item-details"><?php echo $details; ?></span>
    </li>