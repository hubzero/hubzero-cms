<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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