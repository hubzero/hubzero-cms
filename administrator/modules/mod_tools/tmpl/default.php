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
<table class="tools-stats-overview" summary="<?php echo JText::_('Overview of tools'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Registered'); ?></th>
			<th scope="col"><?php echo JText::_('Created'); ?></th>
			<th scope="col"><?php echo JText::_('Uploaded'); ?></th>
			<th scope="col"><?php echo JText::_('Updated'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="registered"><a href="index.php?option=com_tools&amp;status=1" title="<?php echo JText::_('View registered tools'); ?>"><?php echo $this->registered; ?></a></td>
			<td class="created"><a href="index.php?option=com_tools&amp;status=2" title="<?php echo JText::_('View approved tools'); ?>"><?php echo $this->created; ?></a></td>
			<td class="uploaded"><a href="index.php?option=com_tools&amp;status=3" title="<?php echo JText::_('View uploaded tools'); ?>"><?php echo $this->uploaded; ?></a></td>
			<td class="updated"><a href="index.php?option=com_tools&amp;status=5" title="<?php echo JText::_('View updated tools'); ?>"><?php echo $this->updated; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="tools-stats-overview" summary="<?php echo JText::_('Overview of tools'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Approved'); ?></th>
			<th scope="col"><?php echo JText::_('Published'); ?></th>
			<th scope="col"><?php echo JText::_('Retired'); ?></th>
			<th scope="col"><?php echo JText::_('Abandoned'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="approved"><a href="index.php?option=com_tools&amp;status=6" title="<?php echo JText::_('View approved tools'); ?>"><?php echo $this->approved; ?></a></td>
			<td class="published"><a href="index.php?option=com_tools&amp;status=7" title="<?php echo JText::_('View registered tools'); ?>"><?php echo $this->published; ?></a></td>
			<td class="retired"><a href="index.php?option=com_tools&amp;status=8" title="<?php echo JText::_('View uploaded tools'); ?>"><?php echo $this->retired; ?></a></td>
			<td class="abandoned"><a href="index.php?option=com_tools&amp;status=9" title="<?php echo JText::_('View updated tools'); ?>"><?php echo $this->abandoned; ?></a></td>
		</tr>
	</tbody>
</table>