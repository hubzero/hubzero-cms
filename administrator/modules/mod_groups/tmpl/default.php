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
?>
<table class="groups-stats-overview" summary="<?php echo JText::_('Overview of group privacy stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Public'); ?></th>
			<th scope="col"><?php echo JText::_('Protected'); ?></th>
			<th scope="col"><?php echo JText::_('Private'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="public"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=public&amp;policy=" title="<?php echo JText::_('View public groups'); ?>"><?php echo $this->public; ?></a></td>
			<td class="protected"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=protected&amp;policy=" title="<?php echo JText::_('View protected groups'); ?>"><?php echo $this->protected; ?></a></td>
			<td class="private"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=private&amp;policy=" title="<?php echo JText::_('View private groups'); ?>"><?php echo $this->private; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="groups-stats-overview" summary="<?php echo JText::_('Overview of group policy stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Closed'); ?></th>
			<th scope="col"><?php echo JText::_('Invite only'); ?></th>
			<th scope="col"><?php echo JText::_('Restricted'); ?></th>
			<th scope="col"><?php echo JText::_('Open'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="closed"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=&amp;policy=closed" title="<?php echo JText::_('View closed groups'); ?>"><?php echo $this->closed; ?></a></td>
			<td class="invite"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=&amp;policy=invite" title="<?php echo JText::_('View invite only groups'); ?>"><?php echo $this->invite; ?></a></td>
			<td class="restricted"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=&amp;policy=restricted" title="<?php echo JText::_('View restricted groups'); ?>"><?php echo $this->restricted; ?></a></td>
			<td class="open"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;privacy=&amp;policy=open" title="<?php echo JText::_('View open groups'); ?>"><?php echo $this->open; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="groups-stats-overview" summary="<?php echo JText::_('Overview of group status stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Approved'); ?></th>
			<th scope="col"><?php echo JText::_('Pending'); ?></th>
			<th scope="col"><?php echo JText::_('Last 24 hours'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="approved"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;published=1&amp;privacy=&amp;policy=" title="<?php echo JText::_('View approved groups'); ?>"><?php echo $this->approved; ?></a></td>
			<td class="pending"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;published=0&amp;privacy=&amp;policy=" title="<?php echo JText::_('View pending groups'); ?>"><?php echo $this->pending; ?></a></td>
			<td class="newest"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;created=pastday&amp;privacy=&amp;policy=" title="<?php echo JText::_('View groups created in the last 24 hours'); ?>"><?php echo $this->pastDay; ?></a></td>
		</tr>
	</tbody>
</table>