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
JToolBarHelper::title(JText::_('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $this->list->name, 'list.png');

//add toolbar buttons
JToolBarHelper::custom('doaddemail', 'save', '', 'COM_NEWSLETTER_TOOLBAR_SUBMIT', false);
JToolBarHelper::cancel('cancelemail');
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<th width="200px"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_MAILINGLIST'); ?>:</th>
					<td><strong><?php echo $this->list->name; ?></strong></td>
				</tr>
				<tr>
					<th width="200px"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION'); ?>:</th>
					<td>
						<select name="email_confirmation">
							<option value="-1"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NULL'); ?></option>
							<option value="1"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_YES'); ?></option>
							<option value="0"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NO'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_FILE'); ?>:</th>
					<td>
						<input type="file" name="email_file" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP'); ?>:</th>
					<td>
						<select name="email_group">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP_OPTION_NULL'); ?></option>
							<?php foreach ($this->groups as $group) : ?>
								<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_RAW'); ?>:</th>
					<td>
						<textarea name="email_box" rows="10" cols="100"><?php echo $this->emailBox; ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="mid" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="task" value="doimportemail" />
</form>