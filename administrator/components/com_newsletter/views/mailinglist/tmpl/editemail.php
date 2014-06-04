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
$text = ($this->task == 'editEmail' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter Mailing List Email') . ': ' . $text, 'list.png');

//add toolbar buttons
JToolBarHelper::save('saveemail');
JToolBarHelper::cancel('cancelemail');
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><?php echo $text; ?> Mailing List Email</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="200px">Mailing List:</td>
						<td><strong><?php echo $this->list->name; ?></strong></td>
					</tr>
					<tr>
						<td class="key">Email:</td>
						<td><input type="text" name="email[email]" value="<?php echo $this->email->email; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50">
		<table class="meta">
			<tbody>
				<tr>
					<th>Date Added:</th>
					<td><?php echo date("F d, Y @ g:ia", strtotime($this->email->date_added)); ?></td>
				</tr>
				<tr>
					<th>Confirmed?</th>
					<td><?php echo ($this->email->confirmed) ? 'Yes' : 'No'; ?></td>
				</tr>
				<?php if ($this->email->confirmed) : ?>
					<tr>
						<th>Date Confirmed:</th>
						<td><?php echo date("F d, Y @ g:ia", strtotime($this->email->date_confirmed)); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="email[mid]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="email[id]" value="<?php echo $this->email->id; ?>" />
	<input type="hidden" name="task" value="saveemail" />
</form>