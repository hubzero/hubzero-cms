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

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_PACKAGE') . ': ' . $this->packageName, 'packages');

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.edit')) {
    Toolbar::custom('install', 'download', 'download', 'COM_INSTALLER_INSTALL_BUTTON', false);
    Toolbar::spacer();
}
Toolbar::cancel();

$authors = array();
if ($this->installedPackage) {
    $packageAuthors = $this->installedPackage->getAuthors();
    if ($packageAuthors) {
        foreach ($packageAuthors as $author) {
            $authors[] = $author['name'] . ' &lt' . $author['email'] . '&gt';
        }
    }
}
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
            <div class="col span7">
                <fieldset class="adminform">
                    <legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

                    <div class="input-wrap">
                        <?php $versionsLabel = Lang::txt('COM_INSTALLER_PACKAGES_AVAILABLE_VERSIONS'); ?>
                        <label for="field-version"><?php echo $versionsLabel; ?>:</label>
                        <select name="packageVersion" id="field-version">
                            <?php foreach ($this->versions as $version) : ?>
                                <?php
                                $versionVal = $this->escape($version->getVersion());
                                $isSelected = ($this->installedPackage->getVersion() == $version->getVersion());
                                $selectedAttr = $isSelected ? 'selected="true"' : '';
                                $prettyVersion = $this->escape($version->getFullPrettyVersion());
                                ?>
                                <option value="<?php echo $versionVal; ?>"
                                    <?php echo $selectedAttr; ?>><?php echo $prettyVersion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="col span5">
                <table class="meta">
                    <tbody>
                        <tr>
                            <?php $installedVersionLabel = Lang::txt('COM_INSTALLER_PACKAGES_INSTALLED_VERSION'); ?>
                            <th scope="row"><?php echo $installedVersionLabel; ?>:</th>
                            <td><?php echo $this->installedPackage->getFullPrettyVersion(); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_RELEASE_DATE'); ?>:</th>
                            <td><?php echo $this->installedPackage->getReleaseDate()->format("Y-m-d H:i:s"); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_TYPE'); ?>:</th>
                            <td><?php echo $this->installedPackage->getType(); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_AUTHORS'); ?>:</th>
                            <td><?php echo implode(', ', $authors); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="packageName" value="<?php echo $this->escape($this->packageName); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="task" value="install" />

    <?php echo Html::input('token'); ?>
</form>
