<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();


use Components\Tools\Models\Orm\Handler;

$handlerBase = DS . trim($this->fileparams->get('handler_base_path', 'srv/projects/{project}/files/{file}'), DS);
if (!strstr($handlerBase, '{')) {
    $handlerBase .= '/{project}/files/{file}';
}
$handlerBase = str_replace(
    array('{project}', '{file}'),
    array($this->model->get('alias'), $this->item->get('localPath')),
    $handlerBase
);

$me = ($this->item->get('email') == User::get('email')
    || $this->item->get('author') == User::get('name'))  ? 1 : 0;
$when = $this->item->get('date') ? \Components\Projects\Helpers\Html::formatTime($this->item->get('date')) : 'N/A';
$subdirPath = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

$link = Route::url($this->model->link('files') . '&action=' . (($this->item->get('converted')) ? 'open' : 'download') .
$subdirPath . '&asset=' . urlencode($this->item->get('name')));

// Do not display Google native extension
$name = $this->item->get('name');
if ($this->item->get('remote')) {
    $native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
    if (in_array($this->item->get('ext'), $native)) {
        $name = preg_replace("/." . $this->item->get('ext') . "\z/", "", $this->item->get('name'));

        // Attempt to build external URLs to Google services
        if (isset($this->params['remoteConnections'])) {
            if (isset($this->params['remoteConnections'][$this->item->get('localPath')])) {
                $remote = $this->params['remoteConnections'][$this->item->get('localPath')];

                if ($remote->service == 'google') {
                    switch ($this->item->get('ext')) {
                        case 'gdoc':
                            $link = 'https://docs.google.com/document/d/' . $remote->remote_id;
                            break;
                        case 'gslides':
                            $link = 'https://docs.google.com/presentation/d/' . $remote->remote_id;
                            break;
                        case 'gsheet':
                            $link = 'https://docs.google.com/spreadsheets/d/' . $remote->remote_id;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }
}
$ext = $this->item->get('type') == 'file' ? $this->item->get('ext') : 'folder';
?>
<tr class="mini faded mline">
    <?php
    if ($this->model->access('content')) {
        ?>
    <td>
        <?php
            $checkasset = "";
        if ($this->item->get('type') == 'folder') {
            $checkasset = ' dirr';
        } else {
            if ($this->item->get('untracked')) {
                $checkasset .= ' untracked';
            }
            if ($this->item->get('converted')) {
                $checkasset .= ' remote service-google';
            }
        }
        ?>
        <input type="checkbox"
            value="<?php echo urlencode($this->item->get('name')); ?>"
            name="<?php echo $this->item->get('type') == 'file' ? 'asset[]' : 'folder[]'; ?>"
            class="checkasset js<?php echo $checkasset ?>"/>
    </td>
    <?php } ?>
    <td class="middle_valign nobsp is-relative">
        <?php echo $this->item->drawIcon($ext); ?>
        <?php if ($this->item->get('type') == 'file') { ?>
            <?php
            $handlers = Handler::getLaunchUrlsForFile($handlerBase);
            $dropdownClass = 'file-action-dropdown'
                . ($handlers ? ' hasMultiple' : '');
            $fileClass = 'preview file:' . urlencode($name);
            $convertedAttr = $this->item->get('converted')
                ? ' rel="noopener noreferrer external" target="_blank"'
                : '';
            $shortName = \Components\Projects\Helpers\Html::shortenFileName($name, 60);
            ?>
            <div class="<?php echo $dropdownClass; ?>">
                <a href="<?php echo $link; ?>"
                    class="<?php echo $fileClass; ?>"
                    <?php echo $convertedAttr; ?>>
                    <?php echo $shortName; ?>
                </a>
                <?php if ($handlers && count($handlers) > 0) : ?>
                    <?php foreach ($handlers as $handler) : ?>
                    <a href="<?php echo Route::url($handler['url']); ?>">
                        <?php echo $handler['prompt']; ?>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php } else { ?>
            <?php
            $dirUrl = Route::url(
                $this->model->link('files')
                . '/&action=browse&subdir='
                . urlencode($this->item->get('localPath'))
            );
            $dirClass = 'dir:' . urlencode($name);
            $dirTitle = Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR')
                . ' ' . $name;
            $dirShortName = \Components\Projects\Helpers\Html::shortenFileName($name, 60);
            ?>
            <a href="<?php echo $dirUrl; ?>"
                class="<?php echo $dirClass; ?>"
                title="<?php echo $dirTitle; ?>"
                ><?php echo $dirShortName; ?></a>
        <?php } ?>
    </td>
    <td class="shrinked middle_valign"></td>
    <td class="shrinked middle_valign"><?php echo $this->item->getSize(true); ?></td>
    <td class="shrinked middle_valign">
    <?php if (!$this->item->get('untracked')) { ?>
        <?php if ($this->item->get('type') == 'file' && $this->params['versionTracking'] == '1') { ?>
            <?php
            $historyUrl = Route::url(
                $this->model->link('files')
                . '&action=history' . $subdirPath
                . '&asset=' . urlencode($this->item->get('name'))
            );
            $historyTitle = Lang::txt('PLG_PROJECTS_FILES_HISTORY_TOOLTIP');
            ?>
            <a href="<?php echo $historyUrl; ?>"
                title="<?php echo $historyTitle; ?>"
                ><?php echo $when; ?></a>
        <?php } else { ?>
            <?php echo $when; ?>
        <?php } ?>
    <?php } elseif ($this->item->get('untracked')) {
        echo Lang::txt('PLG_PROJECTS_FILES_UNTRACKED');
    } ?>
    </td>
    <?php if ($this->repo->getAdapterName() == 'git') { ?>
        <?php
        $authorDisplay = $me
            ? Lang::txt('PLG_PROJECTS_FILES_ME')
            : $this->item->get('author');
        ?>
        <td class="shrinked middle_valign">
            <?php echo $authorDisplay; ?>
        </td>
    <?php } ?>
    <td class="shrinked middle_valign nojs">
        <?php if ($this->model->access('content')) { ?>
            <?php
            $assetParam = '&asset='
                . urlencode($this->item->get('name'));
            $deleteUrl = Route::url(
                $this->model->link('files')
                . '&action=delete' . $subdirPath . $assetParam
            );
            $moveUrl = Route::url(
                $this->model->link('files')
                . '&action=move' . $subdirPath . $assetParam
            );
            $deleteTitle = Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP');
            $moveTitle = Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP');
            ?>
            <a href="<?php echo $deleteUrl; ?>"
                title="<?php echo $deleteTitle; ?>"
                class="i-delete">&nbsp;</a>
            <a href="<?php echo $moveUrl; ?>"
                title="<?php echo $moveTitle; ?>"
                class="i-move">&nbsp;</a>
        <?php } ?>
    </td>
    <?php if ($this->publishing) { ?>
        <td class="shrinked"></td>
    <?php } ?>
</tr>
