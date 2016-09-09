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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

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

$editAuthorized = User::authorise('com_users', 'manage');
?>

<div class="<?php echo $this->module->module; ?>" id="<?php echo $this->module->module . $this->module->id; ?>">
	<table class="adminlist whosonline-summary">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_SITE'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_ADMIN'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="front-end"><?php echo $siteUserCount; ?></td>
				<td class="back-end"><?php echo $adminUserCount; ?></td>
			</tr>
		</tbody>
	</table>

	<table class="adminlist whosonline-list">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_USER'); ?></td>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_LOCATION'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('MOD_WHOSONLINE_COL_ACTIVITY'); ?></th>
				<?php if ($editAuthorized) { ?>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_LOGOUT'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->rows) > 0) : ?>
				<?php foreach ($this->rows as $k => $row) : ?>
					<?php if (($k+1) <= $this->params->get('display_limit', 25)) : ?>
						<tr>
							<td>
								<?php
									// Get user object
									$user = User::getInstance($row->username);

									// Display link if we are authorized
									if ($editAuthorized)
									{
										echo '<a href="' . Route::url('index.php?option=com_members&task=edit&id='. $row->userid) . '" title="' . Lang::txt('MOD_WHOSONLINE_EDIT_USER') . '">' . $this->escape($user->get('name')) . ' [' . $this->escape($user->get('username')) . ']' . '</a>';
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
									echo '<span class="client client-' . $clientInfo->name . '" data-client="' . substr($clientInfo->name, 0, 1) . '">' . ucfirst($clientInfo->name) . '</span>';
								?>
							</td>
							<td class="priority-3">
								<?php echo Lang::txt('MOD_WHOSONLINE_HOURS_AGO', (time() - $row->time)/3600.0); ?>
							</td>
							<?php if ($editAuthorized) { ?>
								<td>
									<a class="force-logout" href="<?php echo Route::url('index.php?option=com_login&task=logout&uid=' . $row->userid .'&'. Session::getFormToken() .'=1'); ?>">
										<span><?php echo Lang::txt('JLOGOUT'); ?></span>
									</a>
								</td>
							<?php } ?>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				<tr>
					<td colspan="<?php echo ($editAuthorized ? 4 : 3); ?>" class="view-all">
						<a href="<?php echo Route::url('index.php?option=com_members&controller=whosonline'); ?>"><?php echo Lang::txt('MOD_WHOSONLINE_VIEW_ALL'); ?></a>
					</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="<?php echo ($editAuthorized ? 4 : 3); ?>">
						<?php echo Lang::txt('MOD_WHOSONLINE_NO_RESULTS'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
