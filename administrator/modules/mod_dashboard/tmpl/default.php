<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$app = JFactory::getApplication();
?>
<div id="dashboard">
<?php if ($app->getTemplate() == 'khepri' && $this->module->showtitle) : ?>
	<h3 class="title"><?php echo $this->module->title; ?></h3>
<?php endif; ?>
	<table summary="Overview" class="adminlist">
		<thead>
			<tr>
				<th scope="col">Category</th>
				<th scope="col" class="numerical-data">Items</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<th scope="row">
					<a href="index.php?option=com_support&amp;task=abusereports">Abuse reports</a>
				</th>
				<td>
					<a href="index.php?option=com_support&amp;task=abusereports"><?php echo $reports; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="index.php?option=com_resources&amp;task=pending&amp;status=3">Pending resources</a>
				</th>
				<td>
					<a href="index.php?option=com_resources&amp;task=pending&amp;status=3"><?php echo $pending; ?></a>
				</td>
			</tr>
			<tr class="row0">
				<th scope="row">
					<a href="../index.php?option=com_contribtool">Tool contributions</a>
				</th>
				<td>
					<a href="../index.php?option=com_contribtool"><?php echo $contribtool; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="index.php?option=com_store">Store orders</a>
				</th>
				<td>
					<a href="index.php?option=com_store"><?php echo $orders; ?></a>
				</td>
			</tr>
			<tr class="row0">
				<th scope="row">
					<a href="index.php?option=com_feedback">Success stories</a>
				</th>
				<td>
					<a href="index.php?option=com_feedback"><?php echo $quotes; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="../wishlist">Wishes (<?php echo $hubname; ?> list)</a>
				</th>
				<td>
					<a href="../wishlist"><?php echo $wishes; ?></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>