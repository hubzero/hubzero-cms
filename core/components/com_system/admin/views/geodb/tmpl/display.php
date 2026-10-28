<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_GEO_CONFIGURATION'), 'config');
Toolbar::preferences($this->option, '550');

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <div class="grid">
        <div class="col span6">
            <p>
                <?php echo Lang::txt('COM_SYSTEM_GEO_HELP'); ?>
            </p>
        </div>
    </div>
</form>
