<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_PACKAGE') . ': ' . 'ADD NEW PACKAGE', 'packages');

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.create')) {
    Toolbar::custom('install', 'download', 'download', 'COM_INSTALLER_INSTALL_BUTTON', false);
    Toolbar::spacer();
}
Toolbar::cancel();
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=install'
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <div class="grid">
        <?php if ($this->getError()) : ?>
            <div class="col span12">
                <p class="error"><?php echo $this->getError(); ?></p>
            </div>
        <?php else : ?>
            <div class="col span5">
                <fieldset class="adminform">
                    <legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

                    <div class="input-wrap">
                        <?php $packagesLabel = Lang::txt('COM_INSTALLER_PACKAGES_AVAILABLE_PACKAGES'); ?>
                        <label for="field-packageName"><?php echo $packagesLabel; ?>:</label>
                        <select name="packageName" id="field-packageName">
                            <?php foreach ($this->availablePackages as $package) : ?>
                                <?php $pkgName = $this->escape($package->getName()); ?>
                                <?php $prettyName = $this->escape($package->getPrettyName()); ?>
                                <option name="<?php echo $pkgName; ?>"
                                    value="<?php echo $pkgName; ?>"><?php echo $prettyName; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="packageVersion" value="dev-master" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="task" value="install" />

    <?php echo Html::input('token'); ?>
</form>
