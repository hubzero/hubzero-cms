<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&gid=' . $this->group->cn
    . '&active=blog&task=delete&entry=' . $this->entry->id
);
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
    <form action="<?php echo $formAction; ?>"
        method="post"
        id="hubForm">
        <div class="explaination">
<?php if ($this->authorized) { ?>
            <?php
            $newEntryUrl = Route::url(
                'index.php?option=' . $this->option
                . '&gid=' . $this->group->cn
                . '&active=blog&task=new'
            );
            ?>
            <p>
                <a class="add btn" href="<?php echo $newEntryUrl; ?>">
                    <?php echo Lang::txt('New entry'); ?>
                </a>
            </p>
<?php } ?>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_HEADER'); ?></legend>

            <p class="warning">
                <?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_WARNING', $this->entry->title); ?>
            </p>

            <label>
                <input type="checkbox" class="option" name="confirmdel" value="1" />
                <?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_CONFIRM'); ?>
            </label>
        </fieldset>
        <div class="clear"></div>

        <input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
        <input type="hidden" name="process" value="1" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="active" value="blog" />
        <input type="hidden" name="task" value="delete" />
        <input type="hidden" name="entry" value="<?php echo $this->entry->id; ?>" />

        <?php echo Html::input('token'); ?>

        <p class="submit">
            <input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>" />
            <?php
            $publishYear = Date::of($this->entry->publish_up)->toLocal('Y');
            $publishMonth = Date::of($this->entry->publish_up)->toLocal('m');
            $cancelUrl = Route::url(
                'index.php?option=' . $this->option
                . '&gid=' . $this->group->cn
                . '&active=blog&scope=' . $publishYear
                . '/' . $publishMonth
                . '/' . $this->entry->alias
            );
            ?>
            <a href="<?php echo $cancelUrl; ?>">
                <?php echo Lang::txt('Cancel'); ?>
            </a>
        </p>
    </form>
