<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

// The plain form behind the ajax uploader on the product and collection
// edit pages: it is what the <noscript> iframe shows, and where a non-ajax
// upload lands to report how it went.
Html::behavior('framework', true);

if ($this->getError()) {
    echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}

$action = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=upload&tmpl=component'
);
?>
<form action="<?php echo $action; ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend><?php echo Lang::txt('COM_STOREFRONT_PRODUCT_IMAGE'); ?></legend>

        <?php if ($this->file) : ?>
            <p><?php echo Lang::txt('COM_STOREFRONT_FILE'); ?>: <?php echo $this->escape($this->file); ?></p>
        <?php endif; ?>

        <?php if ($this->id) : ?>
            <p>
                <input type="file" name="upload" id="upload" />
                <input type="submit" value="<?php echo Lang::txt('COM_STOREFRONT_UPLOAD'); ?>" />
            </p>
        <?php else : ?>
            <p class="warning"><?php echo Lang::txt('COM_STOREFRONT_UPLOAD_ADDED_LATER'); ?></p>
        <?php endif; ?>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="upload" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="type" value="<?php echo $this->escape($this->type); ?>" />
        <input type="hidden" name="id" value="<?php echo (int) $this->id; ?>" />
        <input type="hidden" name="curfile" value="<?php echo $this->escape($this->file); ?>" />
    </fieldset>

    <?php echo Html::input('token'); ?>
</form>
