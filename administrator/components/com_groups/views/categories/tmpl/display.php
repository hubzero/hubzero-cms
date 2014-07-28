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

JToolBarHelper::title($this->group->get('description') . ': ' . JText::_('COM_GROUPS_PAGES_CATEGORIES'), 'groups.png');

if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('COM_GROUPS_PAGES_CATEGORIES_CONFIRM_DELETE', 'delete');
}
JToolBarHelper::spacer();
JToolBarHelper::custom('manage', 'config','config','COM_GROUPS_MANAGE',false);
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<?php require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" name="adminForm" id="adminForm" method="post">
	<table class="adminlist">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->categories->count();?>);" /></th>
				<th><?php echo JText::_('COM_GROUPS_PAGES_CATEGORY_TITLE'); ?></th>
				<th><?php echo JText::_('COM_GROUPS_PAGES_CATEGORY_COLOR'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php if ($this->categories->count() > 0) : ?>
	<?php foreach ($this->categories as $k => $category) : ?>
			<tr>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $category->get('id'); ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $this->escape($category->get('title')); ?></td>
				<td><?php echo '#' . $this->escape($category->get('color')); ?></td>
			</tr>
	<?php endforeach; ?>
<?php else : ?>
			<tr>
				<td colspan="3"><?php echo JText::_('COM_GROUPS_PAGES_NO_CATEGORIES'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>