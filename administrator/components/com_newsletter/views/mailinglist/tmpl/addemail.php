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
JToolBarHelper::title(JText::_( 'Newsletter Mailing List [' . $this->list->name .']' ), 'list.png');

//add toolbar buttons
JToolBarHelper::custom('doaddemail', 'save', '', 'Submit', false);
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
		<legend>Add Emails</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<th width="200px">Mailing List:</th>
					<td><strong><?php echo $this->list->name; ?></strong></td>
				</tr>
				<tr>
					<th width="200px">Confirmation Email:</th>
					<td>
						<select name="email_confirmation">
							<option value="-1">- Send Confirmation Emails &mdash;</option>
							<option value="1">Yes, Send!</option>
							<option value="0">No, Don't Send!</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Import Emails from File:</th>
					<td>
						<input type="file" name="email_file" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px">and/Or</span>
					</td>
				</tr>
				<tr>
					<th>Import Emails from Group:</th>
					<td>
						<select name="email_group">
							<option value="">- Select Group &mdash;</option>
							<?php foreach ($this->groups as $group) : ?>
								<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px">and/Or</span>
					</td>
				</tr>
				<tr>
					<th>Enter Emails Here:</th>
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