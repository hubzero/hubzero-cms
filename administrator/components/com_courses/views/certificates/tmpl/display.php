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

$canDo = CoursesHelper::getActions('unit');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . JText::_('Certificate') . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList('delete', 'delete');
}

JHTML::_('behavior.tooltip');
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

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist" summary="<?php echo JText::_('COM_COURSES_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th colspan="2">
					(<!-- <a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>"> -->
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					<!-- </a> -->) 
					<!-- <a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>"> -->
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					<!-- </a> -->
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label for="params_certificate">
						<?php echo JText::_('Certificate template:'); ?>
					</label>
					<select name="params[certificate]" id="params_certificate">
						<option value=""><?php echo JText::_('[ none ]'); ?></option>
						<option value="default"<?php if ($this->course->config()->get('certificate') == 'default') { echo ' selected="selected"'; } ?>><?php echo JText::_('Default'); ?></option>
					</select>
				</td>
				<td>
<?php if ($this->course->config()->get('certificate')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=preview&amp;course=<?php echo $this->course->get('id'); ?>&amp;offering=<?php echo $this->offering->get('id'); ?>">
						<?php echo JText::_('Download PDF'); ?>
					</a>
<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>

<?php if ($this->course->config()->get('certificate')) { ?>
	<br />
	<iframe width="100%" height="850" name="managers" id="managers" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=render&amp;no_html=1&amp;course=<?php echo $this->course->get('id'); ?>"></iframe>
<?php } ?>

	<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>