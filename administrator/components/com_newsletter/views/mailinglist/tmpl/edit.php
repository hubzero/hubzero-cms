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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter Mailing List') . ': ' . $text, 'list.png');

//add toolbar buttons
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm">
	<?php if (!$this->list->id) : ?>
		<p class="info">You must first create the mailing list to add emails to it.</p>
	<?php endif; ?>
	<fieldset class="adminform">
		<legend><?php echo $text; ?> Mailing List</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<th width="200px">Name:</th>
					<td><input type="text" name="list[name]" value="<?php echo $this->list->name; ?>" /></td>
				</tr>
				<tr>
					<th>Public/Private:</th>
					<td>
						<select name="list[private]">
							<option value="0" <?php echo ($this->list->private == 0) ? 'selected="selected"': ''; ?>>Public</option>
							<option value="1" <?php echo ($this->list->private == 1) ? 'selected="selected"': ''; ?>>Private</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Description:</th>
					<td><textarea name="list[description]" rows="5"><?php echo $this->list->description; ?></textarea></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="list[id]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="task" value="save" />
</form>