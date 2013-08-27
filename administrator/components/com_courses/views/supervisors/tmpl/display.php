<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$roles = $this->model->roles(array('alias' => '!student'));
/*$r = array();
foreach ($roles as $key => $role)
{
	$r[$role->id] = $role;
}*/
?>
<?php if ($this->getError()) { ?>
	<dl id="system-message">
		<dt><?php echo JText::_('Error'); ?></dt>
		<dd class="error"><?php echo implode('<br />', $this->getErrors()); ?></dd>
	</dl>
<?php } ?>
<div id="groups">
	<form action="index.php" method="post">
		<table>
			<tbody>
				<tr>
					<td>
						<label>
							<input type="text" name="usernames" value="" />
							<?php echo JText::_('Enter comma-separated usernames or IDs'); ?>
						</label>
					</td>
					<td>
						<select name="role">
<?php foreach ($roles as $role) { ?>
							<option value="<?php echo $role->id; ?>"><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php } ?>
						</select>
					</td>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="section" value="<?php echo $this->model->section()->get('id'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="task" value="add" />

						<input type="submit" value="<?php echo JText::_('Add'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php echo JHTML::_('form.token'); ?>
	</form>
	<form action="index.php" method="post" id="adminForm">
		<table class="paramlist admintable">
			<thead>
				<tr>
					<th colspan="4">
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="section" value="<?php echo $this->model->section()->get('id'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="task" id="task" value="remove" />
						
						<input type="submit" name="action" value="<?php echo JText::_('COM_COURSES_MEMBER_REMOVE'); ?>" />
					</th>
				</tr>
			</thead>
			<tbody>
<?php
		ximport('Hubzero_User_Helper');

		$managers = $this->model->members(array(
			'student' => 0,
			'course_id' => $this->model->get('course_id'),
			'offering_id' => $this->model->get('id'),
			'section_id' => $this->model->section()->get('id')
		), true);

		$i = 0;
			foreach ($managers as $manager)
			{
				$u =& JUser::getInstance($manager->get('user_id'));
				if (!is_object($u)) 
				{
					continue;
				}
?>
				<tr>
					<td>
						<input type="hidden" name="entries[<?php echo $i; ?>][id]" value="<?php echo $manager->get('id'); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][course_id]" value="<?php echo $this->model->get('course_id'); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][offering_id]" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][section_id]" value="<?php echo $this->model->section()->get('id'); ?>" />
						<input type="hidden" name="entries[<?php echo $i; ?>][user_id]" value="<?php echo $u->get('id'); ?>" />
						<input type="checkbox" name="entries[<?php echo $i; ?>][select]" value="<?php echo $manager->get('id'); ?>" />
					</td>
					<td class="paramlist_key">
						<a href="index.php?option=com_members&amp;controller=members&amp;task=edit&amp;id[]=<?php echo $u->get('id'); ?>" target="_parent">
							<?php echo $this->escape($u->get('name')) . ' (' . $this->escape($u->get('username')) . ')'; ?>
						</a>
					</td>
					<td class="paramlist_value">
						<a href="mailto:<?php echo $this->escape($u->get('email')); ?>"><?php echo $this->escape($u->get('email')); ?></a>
					</td>
					<td>
						<select name="entries[<?php echo $i; ?>][role_id]" onchange="update();">
<?php foreach ($roles as $role) { ?>
							<option value="<?php echo $role->id; ?>"<?php if ($manager->get('role_id') == $role->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php } ?>
						</select>
					</td>
				</tr>
<?php
				$i++;
			}

?>
			</tbody>
		</table>
		
		<?php echo JHTML::_('form.token'); ?>
		
		<script type="text/javascript">
			function update() 
			{
				var task = document.getElementById('task');
				task.value = 'update';

				var form = document.getElementById('adminForm');
				form.submit();
			}
		</script>
	</form>
</div>