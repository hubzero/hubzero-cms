<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$inGroup = false;

if ($this->group && $this->model->get('sync_group') == 1)
{
	$group = \Hubzero\User\Group::getInstance($this->group);
	if ($group && in_array(User::get('id'), $group->get('members')))
	{
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
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_OWNER'); ?> <a href="<?php echo Route::url($this->model->link('edit') . '&section=team'); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM'); ?></a>.</p>
			<?php }
			elseif ($this->model->access('componentmanager')) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_COMPONENTMANAGER'); ?></p>
			<?php }
			elseif ($this->onlymanager) { ?>
					<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_ONLY_MANAGER'); ?> <a href="<?php echo Route::url($this->model->link('edit') . '&section=team'); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM'); ?></a>.</p>
			<?php } elseif ($inGroup) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_GROUP_MEMBER'); ?> <a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->get('gidNumber')); ?>"><?php echo $group->get('description'); ?></a> <?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_GROUP_MEMBER_QUIT'); ?></p>
			<?php } else { ?>
				<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT_NOTE'); ?></p>
				<h4><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT'); ?></h4>
				<p>
					<input type="hidden" name="confirm" value="1" />
					<span><input type="submit" class="btn btn-success active" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_QUIT'); ?>" /></span>
					<span><a href="<?php echo Route::url($this->model->link()); ?>" class="btn btn-cancel"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?></a></span>
				</p>
			<?php } ?>
		</fieldset>
	</form>
</div>
