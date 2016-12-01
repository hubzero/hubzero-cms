<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$inGroup = false;

if ($this->group)
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
			<?php } elseif ($this->onlymanager) { ?>
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