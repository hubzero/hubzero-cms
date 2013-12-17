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

JToolBarHelper::title($this->group->get('description') . ': <small><small>[ ' . JText::_('Group Page Categories') . ' ]</small></small>', 'groups.png');

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
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('Page Category'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('Title'); ?>:</label></td>
					<td><input type="text" name="category[title]" id="title" value="<?php echo $this->escape($this->category->get('title')); ?>" size="50" /></td>
				</tr>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('Color'); ?>:</label></td>
					<td>#<input style="width: 98%" maxlength="6" type="text" name="category[color]" id="title" value="<?php echo $this->escape($this->category->get('color')); ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="category[id]" value="<?php echo $this->category->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>