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
<table class="groups-stats-overview" summary="<?php echo JText::_('Overview of group privacy stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Visible'); ?></th>
			<th scope="col"><?php echo JText::_('Hidden'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="public"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=0&amp;policy=" title="<?php echo JText::_('View Visible groups'); ?>"><?php echo $this->visible; ?></a></td>
			<td class="protected"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=1&amp;policy=" title="<?php echo JText::_('View Hidden groups'); ?>"><?php echo $this->hidden; ?></a></td>
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
			<td class="closed"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=closed&amp;approved=" title="<?php echo JText::_('View closed groups'); ?>"><?php echo $this->closed; ?></a></td>
			<td class="invite"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=invite&amp;approved=" title="<?php echo JText::_('View invite only groups'); ?>"><?php echo $this->invite; ?></a></td>
			<td class="restricted"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=restricted&amp;approved=" title="<?php echo JText::_('View restricted groups'); ?>"><?php echo $this->restricted; ?></a></td>
			<td class="open"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=open&amp;approved=" title="<?php echo JText::_('View open groups'); ?>"><?php echo $this->open; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="groups-stats-overview" summary="<?php echo JText::_('Overview of group status stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Published'); ?></th>
			<th scope="col"><?php echo JText::_('Pending Approval'); ?></th>
			<th scope="col"><?php echo JText::_('Last 24 hours'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="approved"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;approved=1&amp;discoverability=&amp;policy=" title="<?php echo JText::_('View approved groups'); ?>"><?php echo $this->approved; ?></a></td>
			<td class="pending"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;approved=0&amp;discoverability=&amp;policy=" title="<?php echo JText::_('View pending groups'); ?>"><?php echo $this->pending; ?></a></td>
			<td class="newest"><a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;created=pastday&amp;discoverability=&amp;policy=&amp;approved=" title="<?php echo JText::_('View groups created in the last 24 hours'); ?>"><?php echo $this->pastDay; ?></a></td>
		</tr>
	</tbody>
</table>