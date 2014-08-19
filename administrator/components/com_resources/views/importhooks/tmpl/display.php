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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_RESOURCES_IMPORTHOOK_TITLE_HOOKS'), 'import.png');

JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php?option=com_resources&amp;controller=import" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<thead>
				<tr>
					<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->hooks->count(); ?>);" /></th>
					<th scope="col"><?php echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_NAME'); ?></th>
					<th scope="col"><?php echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_TYPE'); ?></th>
					<th scope="col"><?php echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_FILE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($this->hooks->count() > 0) : ?>
					<?php foreach ($this->hooks as $i => $hook) : ?>
						<tr>
							<td>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $hook->get('id'); ?>" onclick="isChecked(this.checked);" />
							</td>
							<td>
								<?php echo $this->escape($hook->get('name')); ?> <br />
								<span class="hint">
									<?php echo nl2br($this->escape($hook->get('notes'))); ?>
								</span>
							</td>
							<td>
								<?php
									switch ($hook->get('type'))
									{
										case 'postconvert':    echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');    break;
										case 'postmap':        echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');        break;
										case 'postparse':
										default:               echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');      break;
									}
								?>
							</td>
							<td>
								<?php echo $hook->get('file'); ?> &mdash;
								<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_resources&controller=importhooks&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo JText::_('COM_RESOURCES_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4">Currently there are no import hooks.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>