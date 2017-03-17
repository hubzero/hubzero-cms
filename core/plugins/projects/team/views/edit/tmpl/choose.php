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

$group = $this->model->groupOwner();

$members  = array();
$managers = array();
if ($group)
{
	$members  = $group->get('members');
	$managers = $group->get('managers');
}
$team = $this->model->table('Owner')->getIds($this->model->get('id'), 'all', 1);

$notteam = array_diff($members, $team);
?>
<div id="abox-content-wrap">
	<div id="abox-content">
		<form id="select-form" class="select-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=save&alias=' . $this->model->get('alias') . '&active=team'); ?>">
			<h3>
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_TOTAL_MEMBERS')); ?>: <span class="prominent"><?php echo count($notteam); ?></span>
				<span class="abox-controls">
					<input type="submit" class="btn btn-success active" id="b-save" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_SAVE_SELECTION'); ?>" />
					<?php if ($this->ajax) { ?>
						<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?></a>
					<?php } ?>
				</span>
			</h3>
			<table id="teamlist" class="listing">
				<thead>
					<tr>
						<th class="checkbox"></th>
						<th class="th_image"></th>
						<th class="th_user i_user activesort">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_NAME'); ?>
						</th>
						<th<?php if ($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>>
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_ROLE'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$groupmembers = User::all()
						->whereIn('id', $notteam)
						->order('name', 'asc')
						->rows();

					foreach ($groupmembers as $profile)
					{
						if (in_array($profile->get('id'), $team))
						{
							continue;
						}

						?>
						<tr class="mline" id="tr_<?php echo $profile->get('id'); ?>">
							<td>
								<input type="checkbox" value="<?php echo $profile->get('id'); ?>" name="newmember[]" class="checkmember" />
							</td>
							<td class="imagebox">
								<span class="user-img-wrap">
									<img src="<?php echo $profile->picture(); ?>" alt="<?php echo $this->escape($profile->get('name')); ?>" />
								</span>
							</td>
							<td>
								<?php echo $this->escape($profile->get('name')); ?><span class="block mini short prominent"><?php echo $this->escape($profile->get('username')); ?></span>
							</td>
							<td class="mini">
								<?php
								$role = 'MEMBER';
								if (in_array($profile->get('id'), $managers))
								{
									$role = 'MANAGER';
								}
								echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_' . $role);
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="active" value="<?php echo $this->section; ?>" />
			<input type="hidden" name="setup" id="insetup" value="<?php echo $this->model->inSetup() ? 1 : 0; ?>" />
			<input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->model->get('owned_by_group', 0); ?>" />
			<input type="hidden" name="group_id" value="<?php echo $this->model->get('owned_by_group', 0); ?>" />
			<input type="hidden" name="sync_group" value="0" />

			<?php echo Html::input('token'); ?>
		</form>
	</div>
</div>