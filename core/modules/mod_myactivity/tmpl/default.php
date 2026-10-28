<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
    <?php if ($this->params->get('button_show_all', 1)) { ?>
    <ul class="module-nav">
        <li>
            <?php
            $activityUrl = Route::url(
                'index.php?option=com_members&id='
                . User::get('id') . '&active=activity'
            );
            ?>
            <a class="icon-browse" href="<?php echo $activityUrl; ?>">
                <?php echo Lang::txt('MOD_MYACTIVITY_ALL_ACTIVITY'); ?>
            </a>
        </li>
    </ul>
    <?php } ?>

    <?php if ($this->rows->count()) { ?>
        <?php
        $activityListUrl = Route::url(
            'index.php?option=com_members&id='
            . User::get('id') . '&active=activity'
        );
        ?>
        <ul class="compactlist" data-url="<?php echo $activityListUrl; ?>">
            <?php
            foreach ($this->rows as $row) {
                require $this->getLayoutPath('default_item');
            }
            ?>
        </ul>
    <?php } else { ?>
        <p class="no-results"><?php echo Lang::txt('MOD_MYACTIVITY_NO_RESULTS'); ?></p>
    <?php } ?>
</div>
