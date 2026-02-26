<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->exists()) {
    return;
}
?>
<div class="grid pictureframe js">
    <div class="col span3">
        <div id="project-image-box" class="project-image-box">
            <img
                id="project-image-content"
                src="<?php echo Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=media&alias=' . $this->model->get('alias')
                    . '&media=master'
                ); ?>"
                alt=""
            />
        </div>
        <?php if ($this->model->get('picture')) { ?>
            <?php
            $routeUrl = Route::url(
                'index
    . php?option='
                . $this->option
                . '&task=deleteimg&alias='
                . $this->model->get('alias')
            );
            $langTxt2 = Lang::txt('JACTION_DELETE');
            ?>
        <p class="actionlink"><a href="<?php echo $routeUrl; ?>" id="deleteimg">[ <?php echo $langTxt2; ?> ]</a></p>
        <?php } ?>
    </div>
    <div
        class="col span9 omega"
        id="ajax-upload"
        data-action="<?php echo Route::url(
            'index.php?option=' . $this->option
            . '&alias=' . $this->model->get('alias')
            . '&task=doajaxupload&no_html=1'
        ); ?>">
        <div class="form-group">
            <label for="uploader">
                <?php
                $langTxt3 = Lang::txt('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE');
                $langTxt4 = Lang::txt('COM_PROJECTS_UPLOAD_NEW_IMAGE');
                ?>
                <?php echo $langTxt4; ?> <span class="hint"><?php echo $langTxt3; ?></span>
                <span id="status-box"></span>
                <input name="upload" type="file" class="option uploader form-control-file" id="uploader" />
            </label>
        </div>
        <input type="button" value="<?php echo Lang::txt('COM_PROJECTS_UPLOAD'); ?>" class="btn" id="upload-file" />
    </div>
</div>