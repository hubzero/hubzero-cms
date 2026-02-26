<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=blog';

$this->css()
    ->js();
?>

<ul id="page_options">
    <li>
        <a class="icon-archive archive btn" href="<?php echo Route::url($base); ?>">
            <?php echo Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE'); ?>
        </a>
    </li>
</ul>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
    <form action="<?php echo Route::url($base . '&task=delete&entry=' . $this->entry->get('id')); ?>"
        method="post"
        id="hubForm">
        <div class="explaination">
        <?php if ($this->authorized) {
            $newEntryUrl = Route::url($base . '&task=new');
            $newEntryTxt = Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY');
            ?>
            <p><a class="icon-add add btn" href="<?php echo $newEntryUrl; ?>"><?php echo $newEntryTxt; ?></a></p>
        <?php } ?>
        </div>
        <fieldset class="delete-entry">
            <legend><?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE_HEADER'); ?></legend>

            <?php
            $title = $this->escape(stripslashes($this->entry->get('title')));
            ?>
            <p class="warning"><?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE_WARNING', $title); ?></p>

            <label for="confirmdel">
                <input type="checkbox" class="option" name="confirmdel" id="confirmdel" value="1" />
                <?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE_CONFIRM'); ?>
            </label>
        </fieldset>
        <div class="clear"></div>

        <input type="hidden" name="id" value="<?php echo $this->entry->get('created_by'); ?>" />
        <input type="hidden" name="process" value="1" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="active" value="blog" />
        <input type="hidden" name="task" value="view" />
        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="entry" value="<?php echo $this->entry->get('id'); ?>" />

        <?php echo Html::input('token'); ?>

        <p class="submit">
            <input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?>" />

            <a class="btn btn-secondary" href="<?php echo Route::url($this->entry->link()); ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>
    </form>
