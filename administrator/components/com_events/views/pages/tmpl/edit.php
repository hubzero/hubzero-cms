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

$text = ($this->task == 'edit' ? JText::_('COM_EVENTS_EDIT') : JText::_('COM_EVENTS_NEW'));

JToolBarHelper::title(JText::_('COM_EVENTS_PAGE' ) . ': ' . $text, 'event.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_PAGE'); ?></span></legend>

			<div class="input-wrap">
				<a href="index.php?option=com_events&amp;task=edit&amp;id=<?php echo $this->event->id; ?>">
					<?php echo $this->escape(stripslashes($this->event->title)); ?>
				</a>
			</div>

			<div class="input-wrap">
				<label for="title"><?php echo JText::_('COM_EVENTS_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="title" id="title" value="<?php echo $this->escape(stripslashes($this->page->title)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_EVENTS_ALIAS_HINT'); ?>">
				<label for="alias"><?php echo JText::_('COM_EVENTS_ALIAS'); ?>:</label>
				<input type="text" name="alias" id="alias" value="<?php echo $this->escape(stripslashes($this->page->alias)); ?>" />
				<span class="hint"><?php echo JText::_('COM_EVENTS_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="pagetext"><?php echo JText::_('COM_EVENTS_PAGE_TEXT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<?php echo JEditor::getInstance()->display('pagetext', $this->escape(stripslashes($this->page->pagetext)), '', '', 40, 20); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_PAGE_ORDERING'); ?></th>
					<td><?php echo $this->page->ordering; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_PAGE_CREATED'); ?></th>
					<td><?php echo $this->page->created; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_PAGE_CREATED_BY'); ?></th>
					<td><?php echo $this->page->created_by; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_PAGE_LAST_MODIFIED'); ?></th>
					<td><?php echo $this->page->modified; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_PAGE_LAST_MODIFIED_BY'); ?></th>
					<td><?php echo $this->page->modified_by; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="event" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->page->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
