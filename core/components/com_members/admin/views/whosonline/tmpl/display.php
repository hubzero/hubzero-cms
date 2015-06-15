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

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS_WHOSONLINE'), 'user.png');

//get whos online summary
$siteUserCount  = 0;
$adminUserCount = 0;
foreach ($this->rows as $row)
{
	if ($row->client_id == 0)
	{
		$siteUserCount++;
	}
	else
	{
		$adminUserCount++;
	}
}

//are we authorized to edit users
$editAuthorized = User::authorise('core.manage', 'com_users');
?>

<table class="adminlist whosonline-summary">
	<thead>
		<tr>
			<th scope="col"><?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_SITE'); ?></th>
			<th scope="col"><?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_ADMIN'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="front-end"><?php echo $siteUserCount; ?></td>
			<td class="back-end"><?php echo $adminUserCount; ?></td>
		</tr>
	</tbody>
</table>
<br />

<table class="adminlist whosonline-list">
	<thead>
		<tr>
			<th><?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_COL_USER'); ?></th>
			<th><?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_COL_LOCATION'); ?></th>
			<th><?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_COL_ACTIVITY'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->rows) > 0) : ?>
			<?php foreach ($this->rows as $k => $row) : ?>
				<tr>
					<td>
						<?php
							//get user object
							$user = User::getInstance($row->username);

							//display link if we are authorized
							if ($editAuthorized)
							{
								$editLink = Route::url('index.php?option=com_members&controller=members&task=edit&id='. $row->userid);
								echo '<a href="' . $editLink . '" title="' . Lang::txt('JACTION_EDIT') . '">' . $this->escape($user->get('name')) . ' [' . $this->escape($user->get('username')) . ']' . '</a>';
							}
							else
							{
								echo $this->escape($user->get('name')) . ' [' . $this->escape($user->get('username')) . ']';
							}
						?>
					</td>
					<td>
						<?php
							$clientInfo = \Hubzero\Base\ClientManager::client($row->client_id);
							echo ucfirst($clientInfo->name);
						?>
					</td>
					<td>
						<?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_AGO', (time() - $row->time)/3600.0); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="3">
					<?php echo Lang::txt('COM_MEMBERS_WHOSONLINE_NO_RESULTS'); ?>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>