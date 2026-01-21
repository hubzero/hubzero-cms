<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

$repoName = Arr::getValue($this->config, 'name', '');
Toolbar::title(
    Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY') . ': ' . $repoName,
    'packages'
);

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.edit')) {
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=save'
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>">
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
                        <?php
                        $nameLabel = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_NAME');
                        $requiredTxt = Lang::txt('JOPTION_REQUIRED');
                        $nameVal = $this->escape(Arr::getValue($this->config, 'name', ''));
                        ?>
                        <label for="field-name">
                            <?php echo $nameLabel; ?>
                            <span class="required"><?php echo $requiredTxt; ?></span>
                        </label>
                        <input name="name" id="field-name" type="text"
                            class="required" value="<?php echo $nameVal; ?>"></input>
                    </div>

                    <div class="input-wrap">
                        <?php $aliasLabel = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_ALIAS'); ?>
                        <label for="field-alias">
                            <?php echo $aliasLabel; ?>
                            <span class="required"><?php echo $requiredTxt; ?></span>
                        </label>
                        <?php $aliasVal = isset($this->alias) ? $this->escape($this->alias) : ''; ?>
                        <input name="alias" id="field-alias" type="text"
                            class="required" value="<?php echo $aliasVal; ?>"></input>
                    </div>

                    <div class="input-wrap">
                        <?php $descLabel = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_DESCRIPTION'); ?>
                        <label for="field-description">
                            <?php echo $descLabel; ?>
                            <span class="required"><?php echo $requiredTxt; ?></span>
                        </label>
                        <?php $descVal = $this->escape(Arr::getValue($this->config, 'description', '')); ?>
                        <input name="description" id="field-description" type="text"
                            class="required" value="<?php echo $descVal; ?>"></input>
                    </div>

                    <div class="input-wrap">
                        <?php $urlLabel = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_URL'); ?>
                        <label for="field-url">
                            <?php echo $urlLabel; ?>
                            <span class="required"><?php echo $requiredTxt; ?></span>
                        </label>
                        <?php $urlVal = $this->escape(Arr::getValue($this->config, 'url', '')); ?>
                        <input name="url" id="field-url" type="text"
                            class="required" value="<?php echo $urlVal; ?>"></input>
                    </div>

                    <div class="input-wrap">
                        <?php
                        $typeLabel = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_TYPE');
                        $configType = Arr::getValue($this->config, 'type', '');
                        $ghSelected = ($configType == 'github') ? 'true' : '';
                        $glSelected = ($configType == 'gitlab') ? 'true' : '';
                        ?>
                        <label for="field-type"><?php echo $typeLabel; ?></label>
                        <select name="type" id="field-type">
                            <option value="github" selected="<?php echo $ghSelected; ?>">Github</option>
                            <option value="gitlab" selected="<?php echo $glSelected; ?>">Gitlab</option>
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="col span5">
                <?php if (!$this->isNew) : ?>
                    <p class="warning">
                        <?php
                        $removeUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&alias=' . $this->alias
                            . '&task=remove'
                        );
                        ?>
                    <a class="button" href="<?php echo $removeUrl; ?>">
                        <?php echo Lang::txt('Remove Repository'); ?>
                    </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <input type="hidden" name="oldAlias" value="<?php echo $this->escape($this->alias); ?>" />
    <input type="hidden" name="isNew" value="<?php echo $this->isNew ? 'true' : 'false'; ?>" />
    <input type="hidden" name="task" value="save" autocomplete="off" />

    <?php echo Html::input('token'); ?>
</form>
