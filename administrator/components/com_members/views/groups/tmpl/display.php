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
?>
<div id="groups">
	<form action="index.php" method="post">
		<table>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
						<input type="hidden" name="task" value="add" />

						<select name="gid" style="max-width: 15em;">
							<option value=""><?php echo JText::_('Select...'); ?></option>
							<?php
							foreach ($this->rows as $row)
							{
								echo '<option value="' . $row->gidNumber . '">' . $row->description . ' (' . $row->cn . ')</option>' . "\n";
							}
							?>
						</select>
						<select name="tbl">
							<option value="invitees"><?php echo JText::_('INVITEES'); ?></option>
							<option value="applicants"><?php echo JText::_('APPLICANTS'); ?></option>
							<option value="members" selected="selected"><?php echo JText::_('MEMBERS'); ?></option>
							<option value="managers"><?php echo JText::_('MANAGERS'); ?></option>
						</select>

						<input type="submit" value="<?php echo JText::_('ADD_GROUP'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<br />

		<table class="paramlist admintable">
			<tbody>
<?php
		$applicants = \Hubzero\User\Helper::getGroups($this->id, 'applicants');
		$invitees = \Hubzero\User\Helper::getGroups($this->id, 'invitees');
		$members = \Hubzero\User\Helper::getGroups($this->id, 'members');
		$managers = \Hubzero\User\Helper::getGroups($this->id, 'managers');

		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees   = (is_array($invitees))   ? $invitees   : array();
		$members    = (is_array($members))    ? $members    : array();
		$managers   = (is_array($managers))   ? $managers   : array();

		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager)
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem)
		{
			if (!in_array($mem->cn,$managerids))
			{
				$groups[] = $mem;
			}
		}

		if (count($groups) > 0)
		{
			foreach ($groups as $group)
			{
?>
				<tr>
					<td class="paramlist_key"><a href="index.php?option=com_groups&amp;controller=manage&amp;task=edit&amp;id[]=<?php echo $group->cn; ?>" target="_parent"><?php echo $group->description . ' (' . $group->cn . ')'; ?></a></td>
					<td class="paramlist_value"><?php
					$seen[] = $group->cn;

					if ($group->registered)
					{
						$status = JText::_('applicant');
						if ($group->regconfirmed)
						{
							$status = JText::_('member');
							if ($group->manager)
							{
								$status = JText::_('manager');
							}
						}
					}
					else
					{
						$status = JText::_('invitee');
					}
					echo $status; ?></td>
				</tr>
<?php
			}
		}
?>
			</tbody>
		</table>

		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>