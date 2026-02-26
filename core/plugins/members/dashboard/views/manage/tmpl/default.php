<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();
?>

<?php
$pushUrl = Route::url(
    'index.php?option=com_members&controller=plugins'
    . '&task=manage&plugin=dashboard&action=push'
);
$addUrl = Route::url(
    'index.php?option=com_members&controller=plugins'
    . '&task=manage&plugin=dashboard&action=add'
);
?>
<div class="admin-header">
    <a class="icon-add button push-module" href="<?php echo $pushUrl; ?>">
        <?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
    </a>
    <a class="icon-add button add-module" href="<?php echo $addUrl; ?>">
        <?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
    </a>
    <h3>
        <?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MANAGE'); ?>
    </h3>
</div>

<div class="member_dashboard">

    <div class="modules customizable">
        <?php

        foreach ($this->modules as $module) {
            // create view object
            $this->view('module', 'display')
                ->set('admin', $this->admin)
                ->set('module', $module)
                ->display();
        }
        ?>
    </div>

    <div class="modules-empty">
        <h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_TITLE'); ?></h3>
        <p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_DESC'); ?></p>
    </div>
</div>
