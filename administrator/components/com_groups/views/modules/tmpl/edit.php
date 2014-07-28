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

$canDo = GroupsHelper::getActions('group');

JToolBarHelper::title($this->group->get('description') . ': ' . JText::_('COM_GROUPS_PAGES_MODULES'), 'groups.png');

if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<?php require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" name="adminForm" id="adminForm" method="post">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_MODULES_DETAILS'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('COM_GROUPS_MODULES_TITLE'); ?>:</label></td>
						<td><input type="text" name="module[title]" id="title" value="<?php echo $this->escape($this->module->get('title')); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('COM_GROUPS_MODULES_POSITION'); ?>:</label></td>
						<td><input type="text" name="module[position]" id="title" value="<?php echo $this->escape($this->module->get('position')); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('COM_GROUPS_MODULES_STATUS'); ?>:</label></td>
						<td>
							<select name="module[state]">
								<?php
								$states = array(
									1 => JText::_('COM_GROUPS_MODULES_STATUS_PUBLISHED'),
									0 => JText::_('COM_GROUPS_MODULES_STATUS_UNPUBLISHED'),
									2 => JText::_('COM_GROUPS_MODULES_STATUS_DELETED')
								);

								foreach ($states as $k => $v)
								{
									$sel = ($this->module->get('state') == $k) ? 'selected="selected"' : '';
									echo '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('COM_GROUPS_MODULES_ORDERING'); ?>:</label></td>
						<td>
							<select name="module[ordering]">
								<?php foreach ($this->order as $k => $order) : ?>
									<?php $sel = ($order->get('title') == $this->module->get('title')) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel ;?> value="<?php echo ($k + 1); ?>"><?php echo ($k + 1) . '. ' . $order->get('title'); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php
			// get module menus
			$menus = $this->module->menu('list');
			$activeMenu = (!$this->module->get('id')) ? array(0) : array();
			foreach ($menus as $menu)
			{
				$activeMenu[] = $menu->get('pageid');
			}
		?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_MODULES_MENU_ASSIGNMENT'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<label><?php echo JText::_('COM_GROUPS_MODULES_MODULE_ASSIGNMENT'); ?>:</label>
							<select name="menu[assignment]" id="field-assignment">
								<option value="0"><?php echo JText::_('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_ALL'); ?></option>
								<option <?php if (!in_array(0, $activeMenu)) { echo 'selected="selected"'; } ?> value=""><?php echo JText::_('COM_GROUPS_MODULES_MODULE_ASSIGNMENT_SELECTED'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset class="adminform">
								<legend><?php echo JText::_('COM_GROUPS_MODULES_MENU_SELECTION'); ?></legend>

								<?php foreach ($this->pages as $page) : ?>
									<label>
										<?php $ckd = (in_array($page->get('id'), $activeMenu) || in_array(0, $activeMenu)) ? 'checked="checked"' : ''; ?>
										<input type="checkbox" class="option" <?php echo $ckd; ?> name="menu[assigned][]" value="<?php echo $page->get('id'); ?>" /> <?php echo $page->get('title'); ?> <br />
									</label>
								<?php endforeach; ?>
								<br />
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<?php if ($this->module->get('id')) : ?>
			<table class="meta" summary="Metadata">
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_OWNER'); ?></th>
						<td><?php echo $this->group->get('description'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_ID'); ?></th>
						<td><?php echo $this->module->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_CREATED'); ?></th>
						<td><?php echo JHTML::_('date', $this->module->get('created'), 'F j, Y @ g:ia'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_CREATED_BY'); ?></th>
						<td>
							<?php
								$profile = \Hubzero\User\Profile::getInstance($this->module->get('created_by'));
								echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('COM_GROUPS_PAGES_SYSTEM');
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_LAST_MODIFIED'); ?></th>
						<td>
							<?php
								$modified = '--';
								if ($this->module->get('modified_by') != null)
								{
									$modified = JHTML::_('date', $this->module->get('modified'), 'F j, Y @ g:ia');
								}
								echo $modified;
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_MODULES_LAST_MODIFIED_BY'); ?></th>
						<td>
							<?php
								$modified_by = '--';
								if ($this->module->get('modified_by') != null)
								{
									$profile = \Hubzero\User\Profile::getInstance($this->module->get('modified_by'));
									$modified_by = (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('COM_GROUPS_PAGES_SYSTEM');
								}
								echo $modified_by;
							?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_MODULES_MODULE_CONTENT'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<label for="type"><?php echo JText::_('COM_GROUPS_MODULES_CONTENT'); ?>:</label>
							<textarea name="module[content]" rows="20"><?php echo $this->module->get('content'); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>