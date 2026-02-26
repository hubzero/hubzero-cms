<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$hasFiles = $this->files && count($this->files) > 0;
$filesUrl = Route::url($this->model->link('files'));
$headingText = $hasFiles
    ? Lang::txt('PLG_PROJECTS_FILES_RECENTLY_ADDED')
    : Lang::txt('COM_PROJECTS_FILES');
$boxClass = $hasFiles ? '' : ' suggestions';
?>
<div class="sidebox<?php echo $boxClass; ?>">
        <h4><a href="<?php echo $filesUrl; ?>"
            class="hlink"><?php echo $headingText; ?></a>
</h4>
<?php if (!$hasFiles) { ?>
    <p class="s-files">
        <a href="<?php echo $filesUrl; ?>"><?php
            echo Lang::txt('COM_PROJECTS_WELCOME_UPLOAD_FILES');
        ?></a>
    </p>
<?php } else { ?>
    <ul>
        <?php foreach ($this->files as $file) {
            $ext = 'folder';
            $localPath = urlencode($file->get('localPath'));
            $url = $this->model->link('files')
                . '&action=browse&subdir=' . $localPath;
            if ($file->get('type') == 'file') {
                $url = $this->model->link('files')
                    . '&action=download&asset=' . $localPath;
                $ext = $file->get('ext');
            }
            $fileHref = Route::url($url);
            $fileTitle = $this->escape($file->get('name'));
            $icon = $file->drawIcon($ext);
            $shortName = \Components\Projects\Helpers\Html::shortenFileName(
                $file->get('name')
            );
            $fileSize = $file->getSize('formatted');
            $fileDate = Date::of($file->get('date'))->toLocal('M d, Y');
            $authorName = \Components\Projects\Helpers\Html::shortenName(
                $file->get('author')
            );
            ?>
            <li>
                <a href="<?php echo $fileHref; ?>"
                    title="<?php echo $fileTitle; ?>"
                    ><?php echo $icon; ?> <?php echo $shortName; ?></a>
                <span class="block faded mini">
                    <?php echo $fileSize; ?>
                    &middot; <?php echo $fileDate; ?>
                    &middot; <?php echo $authorName; ?>
                </span>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
</div>
