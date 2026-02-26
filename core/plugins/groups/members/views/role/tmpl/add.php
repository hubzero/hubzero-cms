<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <?php
        $browseUrl = Route::url(
            'index.php?option=' . $this->option
            . '&cn=' . $this->group->get('cn')
            . '&active=members'
        );
        ?>
        <a class="icon-browse btn" href="<?php echo $browseUrl; ?>">
            <?php echo Lang::txt('PLG_GROUPS_MEMBERS'); ?>
        </a>
    </li>
</ul>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&cn=' . $this->group->get('cn')
    . '&active=members'
);
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    id="hubForm"
    class="full">
    <fieldset>
        <legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_DETAILS'); ?></legend>
        <label>
            <?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_NAME'); ?>:
            <span class="required">
                <?php echo Lang::txt('JREQUIRED'); ?>
            </span>
            <input type="text" name="role[name]" value="<?php echo $this->role->name; ?>" >
        </label>
        <fieldset>
            <legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_PERMISSIONS'); ?></legend>
            <?php foreach ($this->available_permissions as $perm => $label) : ?>
                <label>
                    <?php $ckd = ($this->role->permissions->get($perm)) ? 'checked="checked"' : '' ?>
                    <input type="hidden" name="role[permissions][<?php echo $perm; ?>]" value="0" />
                    <input class="option"
                        type="checkbox" <?php echo $ckd; ?>
                        name="role[permissions][<?php echo $perm; ?>]"
                        value="1"> <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
    </fieldset>
    <div class="clear"></div>

    <input type="hidden" name="role[id]" value="<?php echo $this->role->id; ?>" >
    <input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
    <input type="hidden" name="active" value="members" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="action" value="saverole" />

    <p class="submit">
        <input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
    </p>
</form>
