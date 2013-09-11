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
<table class="members-stats-overview" summary="<?php echo JText::_('Overview of member stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Confirmed'); ?></th>
			<th scope="col"><?php echo JText::_('Unconfirmed'); ?></th>
			<th scope="col"><?php echo JText::_('Last 24 hours'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="confirmed"><a href="index.php?option=com_members&amp;emailConfirmed=1&amp;registerDate=" title="<?php echo JText::_('View confirmed members'); ?>"><?php echo $this->confirmed; ?></a></td>
			<td class="unconfirmed"><a href="index.php?option=com_members&amp;emailConfirmed=-1&amp;registerDate=" title="<?php echo JText::_('View unconfirmed members'); ?>"><?php echo $this->unconfirmed; ?></a></td>
			<td class="newest"><a href="index.php?option=com_members&amp;emailConfirmed=0&amp;registerDate=<?php echo date("Y-m-d H:i:s", strtotime('-1 day')); ?>" title="<?php echo JText::_('View newest members'); ?>"><?php echo $this->pastDay; ?></a></td>
		</tr>
	</tbody>
</table>