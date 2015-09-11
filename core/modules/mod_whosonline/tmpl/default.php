<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();
?>

<div class="<?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('showmode', 0) == 0 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN'); ?></th>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_GUESTS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo number_format($this->loggedInCount); ?></td>
					<td><?php echo number_format($this->guestCount); ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ($this->params->get('showmode', 0) == 1 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th colspan="2"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_NAME'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->loggedInList as $loggedin) : ?>
					<tr>
						<td><?php echo $loggedin->get('name'); ?></td>
						<td>
							<a href="<?php echo Route::url('index.php?option=com_members&id=' . $loggedin->get('uidNumber')); ?>">
								<?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_VIEW_PROFILE'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<table>
		<tbody>
			<tr>
				<td>
					<a class="btn btn-secondary opposite icon-next" href="<?php echo Route::url('index.php?option=com_members&task=activity'); ?>">
						<?php echo Lang::txt('MOD_WHOSONLINE_VIEW_ALL_ACTIVITIY'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>