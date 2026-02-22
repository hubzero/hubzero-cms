<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
    <legend><span><?php echo Lang::txt('COM_MEMBERS_FIELD_PASSWORD'); ?></span></legend>

    <?php if (is_object($this->password)) : ?>
        <div class="input-wrap">
            <?php echo Lang::txt('COM_MEMBERS_PASSWORD_CURRENT'); ?>:
            <?php
            $val = ($this->profile->get('password')) ? 'value="'
                . $this->profile->get('password')
                . '"' : 'placeholder="'
                . Lang::txt('no local password set')
                . '"'
            ;
            ?>
            <input type="text" name="currentpassword" disabled="disabled" <?php echo $val; ?> />
        </div>
    <?php endif; ?>
    <div class="input-wrap">
        <label for="newpass"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW'); ?>:</label>
        <?php
        $val = Route::url(
            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=checkpass&no_html=1',
            false
        );
        ?>
        <?php
        $dataValues = 'user_id=' . $this->profile->get('id', 0)
            . '&option=' . $this->option
            . '&controller=' . $this->controller
            . '&task=checkpass&no_html=1';
        ?>
        <input
            type="password"
            name="newpass"
            id="newpass"
            value=""
            autocomplete="off"
            data-href="<?php echo $val; ?>"
            data-values="<?php echo $dataValues; ?>" />
        <p class="warning"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW_WARNING'); ?></p>
        <?php if (count($this->password_rules) > 0) : ?>
            <?php $this->css('password.css'); ?>
            <div><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?>:</div>
            <ul id="passrules" class="passrules">
                <?php foreach ($this->password_rules as $rule) : ?>
                    <?php if (!empty($rule)) : ?>
                        <?php
                        $isError = $this->validated
                            && is_array($this->validated)
                            && in_array($rule, $this->validated);
                        ?>
                        <?php if ($isError) : ?>
                            <li class="pass-error"><?php echo $rule; ?></li>
                        <?php elseif ($this->validated) : ?>
                            <li class="pass-passed"><?php echo $rule; ?></li>
                        <?php else : ?>
                            <li class="pass-empty"><?php echo $rule; ?></li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php /*<div class="input-wrap">
        <label id="field_password2-lbl" for="field_password2"><?php echo Lang::txt('Confirm Password'); ?></label>
        <input
            type="password"
            name="password2"
            id="field_password2"
            value=""
            autocomplete="off"
            class="inputbox validate-password"/>
    </div>
    <div class="input-wrap" data-hint="<?php echo Lang::txt('Number of password resets since last reset date'); ?>">
        <label id="field_resetCount-lbl" for="field_resetCount"><?php echo Lang::txt('Password Reset Count'); ?></label>
        <input type="text" name="resetCount" id="field_resetCount" value="0" class="readonly" readonly="readonly"/>
    </div> */?>
    <?php if (is_object($this->password)) : ?>
        <div class="input-wrap">
            <label title="shadowLastChange"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_LAST_CHANGE'); ?>:</label>
            <?php
            if (is_object($this->password) && $this->password->get('shadowLastChange')) {
                $shadowLastChange = $this->password->get('shadowLastChange') * 86400;
                echo date("Y-m-d", $shadowLastChange);
                echo " ({$this->password->get('shadowLastChange')})";
                echo " - " . intval((time() / 86400) - ($shadowLastChange / 86400)) . " days ago";
            } else {
                echo Lang::txt('COM_MEMBERS_NEVER');
            }
            ?>
        </div>
        <div class="input-wrap">
            <label title="shadowMax" class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_MAX'); ?>:</label>
            <input
                type="text"
                name="shadowMax"
                value="<?php echo $this->escape($this->password->get('shadowMax')); ?>"/>
        </div>
        <div class="input-wrap">
            <label
                title="shadowWarning"
                class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_WARNING'); ?>:</label>
            <input
                type="text"
                name="shadowWarning"
                value="<?php echo $this->escape($this->password->get('shadowWarning')); ?>"/>
        </div>
        <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?>">
            <label title="shadowExpire"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE'); ?>:</label>
            <input
                type="text"
                name="shadowExpire"
                value="<?php echo $this->escape($this->password->get('shadowExpire')); ?>"/>
            <span class="hint"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?></span>
        </div>
        <!-- Add reset for user secret -->
        <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_PASSWORD_RESET_SECRET_HINT'); ?>">
            <label for="cbResetSecret"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RESET_SECRET'); ?>:</label>
            <input type="checkbox" name="resetSecret" id="cbResetSecret" value="1" class="checkbox-toggle" />
            <span class="hint"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RESET_SECRET_HINT'); ?></span>
        </div>
    <?php endif; ?>
</fieldset>
