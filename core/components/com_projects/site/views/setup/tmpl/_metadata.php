<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->exists()) {
    return;
}
?>
<div class="info_blurb grid">
    <div class="col span1">
        <?php
        $routeUrl = Route::url(
            'index
    . php?option='
            . $this->option
            . '&alias='
            . $this->model->get('alias')
            . '&task=media'
        );
        ?>
        <img src="<?php echo $routeUrl; ?>" alt="" />
    </div>
    <div class="col span6">
        <?php
        $langTxt2 = Lang::txt('COM_PROJECTS_PROJECT');
        ?>
        <?php echo '<span class="prominent">' . $langTxt2 . '</span>: ' . $this->escape($this->model->get('title')); ?>
        (<span><?php echo $this->model->get('alias'); ?></span>)
        <?php
        $langTxt3 = Lang::txt('COM_PROJECTS_CREATED');
        ?>
        <span class="block faded"><?php echo $langTxt3 . ' ' . $this->model->created('date'); ?></span>
    </div>
    <div class="col span5 omega">
    </div>
</div>
