<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_DASHBOARD_COL_CATEGORY'); ?></th>
				<th scope="col" class="numerical-data"><?php echo Lang::txt('MOD_DASHBOARD_COL_ITEMS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_support&controller=abusereports'); ?>"><?php echo Lang::txt('MOD_DASHBOARD_ABUSE_REPORTS'); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_support&controller=abusereports'); ?>"><?php echo $this->reports; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_resources&task=pending&status=3'); ?>"><?php echo Lang::txt('MOD_DASHBOARD_PENDING_RESOURCES'); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_resources&task=pending&status=3'); ?>"><?php echo $this->pending; ?></a>
				</td>
			</tr>
			<tr class="row0">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_tools'); ?>"><?php echo Lang::txt('MOD_DASHBOARD_TOOLS'); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_tools'); ?>"><?php echo $this->contribtool; ?></a>
				</td>
			</tr>
		<?php if ($this->banking && Component::isEnabled('com_store')) { ?>
			<tr class="row1">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_store'); ?>"><?php echo Lang::txt('MOD_DASHBOARD_STORE_ORDERS'); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_store'); ?>"><?php echo $this->orders; ?></a>
				</td>
			</tr>
		<?php } ?>
			<tr class="row0">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_feedback'); ?>"><?php echo Lang::txt('MOD_DASHBOARD_STORIES'); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_feedback'); ?>"><?php echo $this->quotes; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->mainlist); ?>"><?php echo Lang::txt('MOD_DASHBOARD_WISHES', $this->sitename); ?></a>
				</th>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->mainlist); ?>"><?php echo $this->wishes; ?></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>