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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('MOD_DASHBOARD_COL_CATEGORY'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('MOD_DASHBOARD_COL_ITEMS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<th scope="row">
					<a href="index.php?option=com_support&amp;controller=abusereports"><?php echo JText::_('MOD_DASHBOARD_ABUSE_REPORTS'); ?></a>
				</th>
				<td>
					<a href="index.php?option=com_support&amp;controller=abusereports"><?php echo $this->reports; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="index.php?option=com_resources&amp;task=pending&amp;status=3"><?php echo JText::_('MOD_DASHBOARD_PENDING_RESOURCES'); ?></a>
				</th>
				<td>
					<a href="index.php?option=com_resources&amp;task=pending&amp;status=3"><?php echo $this->pending; ?></a>
				</td>
			</tr>
			<tr class="row0">
				<th scope="row">
					<a href="../index.php?option=com_tools"><?php echo JText::_('MOD_DASHBOARD_TOOLS'); ?></a>
				</th>
				<td>
					<a href="../index.php?option=com_tools"><?php echo $this->contribtool; ?></a>
				</td>
			</tr>
		<?php if ($this->banking && JComponentHelper::isEnabled('com_store')) { ?>
			<tr class="row1">
				<th scope="row">
					<a href="index.php?option=com_store"><?php echo JText::_('MOD_DASHBOARD_STORE_ORDERS'); ?></a>
				</th>
				<td>
					<a href="index.php?option=com_store"><?php echo $this->orders; ?></a>
				</td>
			</tr>
		<?php } ?>
			<tr class="row0">
				<th scope="row">
					<a href="index.php?option=com_feedback"><?php echo JText::_('MOD_DASHBOARD_STORIES'); ?></a>
				</th>
				<td>
					<a href="index.php?option=com_feedback"><?php echo $this->quotes; ?></a>
				</td>
			</tr>
			<tr class="row1">
				<th scope="row">
					<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->mainlist; ?>"><?php echo JText::sprintf('MOD_DASHBOARD_WISHES', $this->sitename); ?></a>
				</th>
				<td>
					<a href="index.php?option=com_wishlist&amp;controller=wishes&amp;wishlist=<?php echo $this->mainlist; ?>"><?php echo $this->wishes; ?></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>