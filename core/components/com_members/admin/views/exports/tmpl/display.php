<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_EXPORT'), 'export.png');

?>
<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <div class="grid">
        <div class="col span4">
            <?php
            $href = Route::url(
                'index.php?option='
                . $this->option
                . '&controller='
                . $this->controller
                . '&task=run&delimiter=,'
            );
            ?>
            <a class='permissions button' href="<?php echo $href; ?>">Download CSV of all users</a>
        </div>
    </div>

    <?php echo Html::input('token'); ?>
</form>
