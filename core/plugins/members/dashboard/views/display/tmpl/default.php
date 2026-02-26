<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

// is the dashboard customizable?
$customizable = true;
if ($this->params->get('allow_customization', 1) == 0) :
    $customizable = false;
endif;
?>

<h3 class="section-header">
    <?php echo Lang::txt('PLG_MEMBERS_DASHBOARD'); ?>
</h3>

<?php if ($customizable) : ?>
    <ul id="page_options">
        <li>
            <?php
            $addUrl = Route::url(
                'index.php?option=com_members&id=' . User::get('id')
                . '&active=dashboard&action=add'
            );
            ?>
            <a class="icon-add btn add-module" href="<?php echo $addUrl; ?>">
                <?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
            </a>
        </li>
    </ul>
<?php endif; ?>

<noscript>
    <p class="warning"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT'); ?></p>
</noscript>

<div class="modules-container">
    <div class="modules <?php echo ($customizable) ? 'customizable' : ''; ?>"
        data-userid="<?php echo User::get('id'); ?>"
        data-token="<?php echo Session::getFormToken(); ?>">
        <?php

        foreach ($this->modules as $module) :
            // create view object
            $this->view('module')
                ->set('admin', $this->admin)
                ->set('module', $module)
                ->display();
        endforeach;
        ?>
    </div>
</div>

<div class="modules-empty">
    <h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_TITLE'); ?></h3>
    <p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_DESC'); ?></p>
</div>
