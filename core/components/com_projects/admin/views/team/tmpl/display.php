<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$group = $this->project->groupOwner();

$members = array();
$managers = array();
if ($group)
{
	$members = $group->get('members');
	$managers = $group->get('managers');
}

$groupSynced = ($group && $this->project->get('sync_group'));

Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_TEAM'), 'projects');
Toolbar::appendButton('Popup', 'new', 'COM_PROJECTS_TEAM_NEW', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&project=' . $this->filters['project'], 570, 170);
Toolbar::spacer();
Toolbar::deleteList('COM_PROJECTS_TEAM_DELETE', 'delete');
Toolbar::spacer();
Toolbar::help('team');

Html::behavior('tooltip');

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($groupSynced) { ?>
		<fieldset id="filter-bar">
			<p class="warning">
				<?php echo Lang::txt(
					'Membership is synced with group "%s".<br />Addition or removal of members in that group must handled through the group\'s membership interface.',
					'<a href="' . Route::url('index.php?option=com_groups&controller=membership&gid=' . $group->get('cn')) . '">' . $group->get('description') . ' (' . $group->get('cn') . ')</a>'
				); ?>
			</p>
		</fieldset>
	<?php } ?>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="8"><a href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_PROJECTS'); ?></a>  > (<?php echo $this->escape(stripslashes($this->project->get('alias'))); ?>) <?php echo $this->escape(stripslashes($this->project->get('title'))); ?></th>
			</tr>
			<tr>
				<?php //if (!$groupSynced) { ?>
					<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<?php //} ?>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PROJECTS_TEAM_USERID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_PROJECTS_TEAM_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_PROJECTS_TEAM_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_PROJECTS_TEAM_ROLE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_PROJECTS_TEAM_JOINED'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_PROJECTS_TEAM_LAST_VISIT'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_PROJECTS_TEAM_ADDED_AS_PART_OF_GROUP'); ?></th>
				<?php /*if (!$groupSynced) { ?>
					<th scope="col"><?php echo Lang::txt('COM_PROJECTS_TEAM_ACTION'); ?></th>
				<?php }*/ ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			$username = $row->username ? $row->username : $row->invited_email;
			$creator  = $this->project->owner('id') == $row->userid ? 1 : 0;

			switch ($row->role)
			{
				case '1':
					$role = Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER');
					break;
				case '5':
					$role = Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER');
					break;
				case '2':
				default:
					$role = Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR');
					break;
			}

			$disabled = false;
			if ($row->native && (($groupSynced && in_array($row->userid, $members)) || ($this->managers_count == 1 && $row->role == 1)))
			{
				$disabled = true;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<?php //if (!$groupSynced) { ?>
					<td>
						<?php if ($disabled) { ?>
							<!-- <input type="hidden" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" /> -->
						<?php } else { ?>
							<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
						<?php } ?>
					</td>
				<?php //} ?>
				<td class="priority-4">
					<?php echo $this->escape($row->userid); ?>
				</td>
				<td>
					<?php if (isset($row->username)) : ?>
						<a href="<?php echo Route::url('index.php?option=com_members&controller=members&task=edit&id=' . $row->userid); ?>">
							<?php echo $this->escape(stripslashes($row->fullname)); ?>
						</a>
					<?php else : ?>
						<span>
							<?php echo $this->escape(stripslashes($row->fullname)); ?>
						</span>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<span>
						<?php echo $this->escape(stripslashes($username)); ?>
					</span>
				</td>
				<td class="priority-5">
					<span>
						<?php //echo ($creator && !$this->project->groupOwner() ? '<span class="prominent">' . Lang::txt('COM_PROJECTS_TEAM_OWNER') . '</span> / ' : '') . $this->escape($role); ?>
					<?php if ($creator) { ?>
						<span class="prominent"><?php echo Lang::txt('COM_PROJECTS_TEAM_OWNER') . ' / ' . $this->escape($role); ?></span>
					<?php } else { ?>
						<select name="role[<?php echo $row->userid; ?>]" onchange="Joomla.submitbutton('update');">
							<option value="1"<?php if ($row->role == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER'); ?></option>
							<option value="0"<?php if ($row->role == 2 || !$row->role) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR'); ?></option>
							<option value="5"<?php if ($row->role == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER'); ?></option>
						</select>
					<?php } ?>
					</span>
				</td>
				<td class="priority-4">
					<?php echo $row->status == 1 ? Date::of($row->added)->toLocal('M d, Y') : '<span class="invited">' . Lang::txt('PLG_PROJECTS_TEAM_INVITED') . '</span>'; ?>
				</td>
				<td class="priority-4">
					<?php
					$timecheck = gmdate('Y-m-d H:i:s', time() - (15 * 60));
					$lastvisit = $row->lastvisit && $row->lastvisit != '0000-00-00 00:00:00'
								? Date::of($row->lastvisit)->relative()
								: Lang::txt('COM_PROJECTS_TEAM_NEVER');
					echo $lastvisit;
					?>
				</td>
				<td>
					<?php echo $row->groupdesc ? \Hubzero\Utility\String::truncate($row->groupdesc, 30) : ''; ?>
					<span class="block mini short prominent"><?php echo ($row->groupname ? $row->groupname : Lang::txt('COM_PROJECTS_NONE')); ?></span>
				</td>
				<?php /*if (!$groupSynced) { ?>
					<td>
						<?php
						switch ($row->role)
						{
							case '1': // Owner
								?>
									<a class="state demote" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=demote&project=' . $this->filters['project'] . '&id=' . $row->userid . '&' . Session::getFormToken() . '=1'); ?>">
										<span><?php echo Lang::txt('COM_PROJECTS_TEAM_DEMOTE'); ?></span>
									</a>
								<?php
							break;
							case '5': // Reviewer
							case '2': // Collaborator
							default:
								?>
									<a class="state trash" onclick="javascript:if (confirm('Cancel membership?')){return true;}else{return false;}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&project=' . $this->filters['project'] . '&id=' . $row->userid . '&' . Session::getFormToken() . '=1'); ?>">
										<span><?php echo Lang::txt('COM_PROJECTS_TEAM_REMOVE'); ?></span>
									</a>
								<?php
							break;
						}
						?>
					</td>
				<?php }*/ ?>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="project" value="<?php echo $this->filters['project']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>