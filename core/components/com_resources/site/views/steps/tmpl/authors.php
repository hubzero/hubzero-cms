<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$this
     ->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-add add btn" href="<?= Route::url('index.php?option=' . $this->option . '&task=draft') ?>">
                <?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
            </a>
        </p>
    </div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
    <?php
        $this
             ->view('steps')
             ->set('option', $this->option)
             ->set('step', $this->step)
             ->set('steps', $this->steps)
             ->set('id', $this->id)
             ->set('resource', $this->row)
             ->set('progress', $this->progress)
             ->display();
    ?>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>

    <form action="<?= Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id) ?>" method="post" id="hubForm">
        <div class="explaination">
            <h4><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_HEADER'); ?></h4>
            <p><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_EXPLANATION'); ?></p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_OWNERSHIP'); ?></legend>

        <?php if ($this->groups && count($this->groups) > 0) { ?>
            <div class="grid">
                <div class="col span6">
                    <label for="group_owner">
                        <?= Lang::txt('COM_CONTRIBUTE_GROUPS_GROUP') ?>: <span class="optional"><?= Lang::txt('COM_CONTRIBUTE_OPTIONAL') ?></span>
                        <select name="group_owner" id="group_owner">
                            <option value=""><?php echo Lang::txt('COM_CONTRIBUTE_SELECT_GROUP'); ?></option>
                            <?php
                            $owner  = $this->row->group_owner->get('cn');
                            $listed = false;
                            if ($this->groups && count($this->groups) > 0) {
                                foreach ($this->groups as $group) {
                                    $listed = $listed || ($owner == $group->cn);
                                    ?>
                                <option value="<?= $this->escape($group->cn) ?>"<?= $owner == $group->cn ? ' selected="selected"' : '' ?>><?= $this->escape(stripslashes($group->description)) ?></option>
                                    <?php
                                }
                            }
                            // Keep a group the editor isn't a member of, or it would be dropped on save
                            if ($owner && !$listed) {
                                ?>
                                <option value="<?= $this->escape($owner) ?>" selected="selected"><?= $this->escape($this->row->group_owner->get('description', $owner)) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="access">
                        <?= Lang::txt('COM_CONTRIBUTE_GROUPS_ACCESS_LEVEL') ?>: <span class="optional"><?= Lang::txt('COM_CONTRIBUTE_OPTIONAL') ?></span>
                        <select name="access" id="access">
                            <option value="0"<?= $this->row->access == 0 ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC') ?></option>
                            <option value="1"<?= $this->row->access == 1 ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED') ?></option>
                            <option value="3"<?= $this->row->access == 3 ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED') ?></option>
                            <option value="4"<?= $this->row->access == 4 ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE') ?></option>
                        </select>
                    </label>
                </div>
            </div>
            <p>
                <strong><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC') ?></strong> = <?= Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION') ?><br />
                <strong><?= Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED') ?></strong> = <?= Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION') ?><br />
                <strong><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED') ?></strong> = <?= Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED_EXPLANATION') ?><br />
                <strong><?= Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE') ?></strong> = <?= Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE_EXPLANATION') ?>
            </p>
        <?php } else { ?>
            <p class="information">
                <?php echo Lang::txt('COM_CONTRIBUTE_GROUPS_JOIN'); ?>
            </p>
        <?php } ?>
        </fieldset><div class="clear"></div>

        <div class="explaination">
            <h4><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN'); ?></h4>
            <p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>

            <h4><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR'); ?></h4>
            <p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR_EXPLANATION'); ?></p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_AUTHORS'); ?></legend>
            <div class="field-wrap">
                <iframe width="100%" height="400" frameborder="0" name="authors" id="authors" title="<?= Lang::txt('COM_CONTRIBUTE_AUTHORS_AUTHORS') ?>" scrolling="auto" src="/index.php?option=<?= $this->option ?>&amp;controller=authors&amp;id=<?= $this->id ?>&amp;tmpl=component"></iframe>
            </div>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            <input type="hidden" name="task" value="<?php echo $this->task; ?>" />
            <input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
            <?php echo Html::input('token'); ?>
            <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
        </fieldset><div class="clear"></div>

        <div class="submit">
            <input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
        </div>
    </form>
</section><!-- / .main section -->
