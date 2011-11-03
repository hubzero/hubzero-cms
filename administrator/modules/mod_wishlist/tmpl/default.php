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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<table class="wishlist-stats-overview" summary="<?php echo JText::_('Overview of active wishlist items'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Pending'); ?></th>
			<th scope="col"><?php echo JText::_('Accepted'); ?></th>
			<th scope="col"><?php echo JText::_('Granted'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="pending"><a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=0" title="<?php echo JText::_('View pending wishes'); ?>"><?php echo $this->pending; ?></a></td>
			<td class="accepted"><a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=1" title="<?php echo JText::_('View accepted wishes'); ?>"><?php echo $this->accepted; ?></a></td>
			<td class="granted"><a href="index.php?option=com_wishlist&amp;status=1" title="<?php echo JText::_('View granted wishes'); ?>"><?php echo $this->granted; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="wishlist-stats-overview" summary="<?php echo JText::_('Overview of inactive wishlist items'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Rejected'); ?></th>
			<th scope="col"><?php echo JText::_('Withdrawn'); ?></th>
			<th scope="col"><?php echo JText::_('Removed'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="rejected"><a href="index.php?option=com_wishlist&amp;status=3" title="<?php echo JText::_('View rejected wishes'); ?>"><?php echo $this->rejected; ?></a></td>
			<td class="withdrawn"><a href="index.php?option=com_wishlist&amp;status=4" title="<?php echo JText::_('View withdrawn wishes'); ?>"><?php echo $this->withdrawn; ?></a></td>
			<td class="removed"><a href="index.php?option=com_wishlist&amp;status=2" title="<?php echo JText::_('View removed wishes'); ?>"><?php echo $this->removed; ?></a></td>
		</tr>
	</tbody>
</table>
<?php if ($this->params->get('showMine', 0)) { ?>
<table class="wishlist-stats-overview" summary="<?php echo JText::_('Overview of active my wishlist items'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('My Wishes (pending)'); ?></th>
			<th scope="col"><?php echo JText::_('My Wishes (accepted)'); ?></th>
			<th scope="col"><?php echo JText::_('My Wishes (granted)'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="mypending"><a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=0&amp;proposed_by=<?php echo $this->username; ?>" title="<?php echo JText::_('View my pending wishes'); ?>"><?php echo $this->mypending; ?></a></td>
			<td class="myaccepted"><a href="index.php?option=com_wishlist&amp;status=0&amp;accepted=1&amp;proposed_by=<?php echo $this->username; ?>" title="<?php echo JText::_('View my accepted wishes'); ?>"><?php echo $this->myaccepted; ?></a></td>
			<td class="mygranted"><a href="index.php?option=com_wishlist&amp;status=1&amp;proposed_by=<?php echo $this->username; ?>" title="<?php echo JText::_('View my granted wishes'); ?>"><?php echo $this->mygranted; ?></a></td>
		</tr>
	</tbody>
</table>
<?php } ?>