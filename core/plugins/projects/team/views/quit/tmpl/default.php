<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$inGroup = false;

if ($this->group && $this->model->get('sync_group') == 1) {
    $group = \Hubzero\User\Group::getInstance($this->group);
    if ($group && in_array(User::get('id'), $group->get('members'))) {
        $inGroup = true;
    }
}
?>
<div id="abox-content">
    <h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT'); ?></h3>
    <form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->model->link()); ?>">
        <fieldset>
            <input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
            <input type="hidden" name="action" value="quit" />
            <input type="hidden" name="task" value="view" />
            <input type="hidden" name="active" value="team" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo Html::input('token'); ?>
            <?php if ($this->model->access('owner')) { ?>
                <?php
                $linkUrl = Route::url($this->model->link('edit') . '&section=team');
                ?>
                <p class="warning"><?= Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_OWNER') ?> <a href="<?= $linkUrl ?>"><?= Lang::txt('PLG_PROJECTS_TEAM') ?></a>.</p>
            <?php } elseif ($this->model->access('componentmanager')) { ?>
                <p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_COMPONENTMANAGER'); ?></p>
            <?php } elseif ($this->onlymanager) { ?>
                    <?php
                    $linkUrl = Route::url($this->model->link('edit') . '&section=team');
                    ?>
                    <p class="warning"><?= Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_ONLY_MANAGER') ?> <a href="<?= $linkUrl ?>"><?= Lang::txt('PLG_PROJECTS_TEAM') ?></a>.</p>
            <?php } elseif ($inGroup) { ?>
                <?php
                $linkUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('gidNumber'));
                ?>
                <p class="warning"><?= Lang::txt('PLG_PROJECTS_TEAM_LEAVE_GROUP_MEMBER') ?> <a href="<?= $linkUrl ?>"><?= $this->escape($group->get('description')) ?></a> <?= Lang::txt('PLG_PROJECTS_TEAM_LEAVE_GROUP_MEMBER_QUIT') ?></p>
            <?php } else { ?>
                <p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_NOTE'); ?></p>
                <h4><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT'); ?></h4>
                <p>
                    <input type="hidden" name="confirm" value="1" />
                    <span><input type="submit" class="btn btn-success active"
                        value="<?= Lang::txt('PLG_PROJECTS_TEAM_QUIT') ?>" /></span>
                    <span><a href="<?= Route::url($this->model->link()) ?>"
                        class="btn btn-cancel"><?= Lang::txt('PLG_PROJECTS_TEAM_CANCEL') ?></a></span>
                </p>
            <?php } ?>
        </fieldset>
    </form>
</div>
