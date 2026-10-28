<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// If the user is already logged in, redirect to the return or profile page.
if (!User::isGuest()) {
    $return = base64_decode(Request::getString('return', ''));

    if ($return) {
        App::redirect(Route::url($return, false));
        return;
    }

    // Redirect to profile page.
    App::redirect(Route::url('index.php?option=com_members&task=myaccount', false));
    return;
}

$showLogoutDesc = $this->params->get('logoutdescription_show') == 1;
$logoutDesc     = $this->params->get('logout_description');
$logoutImage    = $this->params->get('logout_image');
$showDescBlock  = ($showLogoutDesc && str_replace(' ', '', $logoutDesc) != '') || $logoutImage != '';
$logoutUrl      = Route::url('index.php?option=' . $this->option . '&task=user.logout');
$returnVal      = base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return')));
?>
<div class="logout<?php echo $this->pageclass_sfx?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php if ($showDescBlock) : ?>
    <div class="logout-description">
    <?php endif; ?>

        <?php if ($showLogoutDesc) : ?>
            <?php echo $logoutDesc; ?>
        <?php endif; ?>

        <?php if ($logoutImage != '') :?>
            <img
                src="<?php echo $this->escape($logoutImage); ?>"
                class="logout-image"
                alt="<?php echo Lang::txt('COM_USERS_LOGOUT_IMAGE_ALT'); ?>"
            />
        <?php endif; ?>

    <?php if ($showDescBlock) : ?>
    </div>
    <?php endif; ?>

    <form action="<?php echo $logoutUrl; ?>" method="post">
        <div>
            <button type="submit" class="button"><?php echo Lang::txt('JLOGOUT'); ?></button>
            <input type="hidden" name="return" value="<?php echo $returnVal; ?>" />
            <?php echo Html::input('token'); ?>
        </div>
    </form>
</div>
