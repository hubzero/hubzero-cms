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
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'editpage' ? JText::_( 'COM_EVENTS_EDIT' ) : JText::_( 'COM_EVENTS_NEW' ) );

JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'COM_EVENTS_PAGE' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'event.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2>
		<a href="index.php?option=com_events&amp;task=edit&amp;id=<?php echo $this->event->id; ?>">
			<?php echo $this->escape(stripslashes($this->event->title)); ?>
		</a>
	</h2>
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_PAGE'); ?></span></legend>
			
			<input type="hidden" name="event" value="<?php echo $this->event->id; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('COM_EVENTS_TITLE'); ?>:</label></td>
						<td><input type="text" name="title" id="title" value="<?php echo $this->escape(stripslashes($this->page->title)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="alias"><?php echo JText::_('COM_EVENTS_ALIAS'); ?>:</label></td>
						<td>
							<input type="text" name="alias" id="alias" value="<?php echo $this->escape(stripslashes($this->page->alias)); ?>" size="50" />
							<br /><span>A short identifier for this page. Ex: "agenda". Alpha-numeric characters only. No spaces.</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="pagetext"><?php echo JText::_('COM_EVENTS_PAGE_TEXT'); ?>:</label><br />
							<?php echo $editor->display('pagetext', $this->escape(stripslashes($this->page->pagetext)), '100%', '350px', '40', '10'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th>Ordering</th>
					<td><?php echo $this->page->ordering; ?></td>
				</tr>
				<tr>
					<th>Created</th>
					<td><?php echo $this->page->created; ?></td>
				</tr>
				<tr>
					<th>Created by</th>
					<td><?php echo $this->page->created_by; ?></td>
				</tr>
				<tr>
					<th>Last Modified</th>
					<td><?php echo $this->page->modified; ?></td>
				</tr>
				<tr>
					<th>Modified by</th>
					<td><?php echo $this->page->modified_by; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
