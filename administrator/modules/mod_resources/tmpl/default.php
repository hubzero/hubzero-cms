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
<table class="resources-stats-overview" summary="<?php echo JText::_('Overview of draft and pending resources'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Draft (internal)'); ?></th>
			<th scope="col"><?php echo JText::_('Draft (user)'); ?></th>
			<th scope="col"><?php echo JText::_('Pending'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="draft-internal"><a href="index.php?option=com_resources&amp;c=resources&amp;status=5" title="<?php echo JText::_('View draft (internal) resources'); ?>"><?php echo $this->draftInternal; ?></a></td>
			<td class="draft-user"><a href="index.php?option=com_resources&amp;c=resources&amp;status=2" title="<?php echo JText::_('View draft (user) resources'); ?>"><?php echo $this->draftUser; ?></a></td>
			<td class="pending"><a href="index.php?option=com_resources&amp;c=resources&amp;status=3" title="<?php echo JText::_('View pending resources'); ?>"><?php echo $this->pending; ?></a></td>
		</tr>
	</tbody>
</table>

<table class="resources-stats-overview" summary="<?php echo JText::_('Overview of published resources'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Published'); ?></th>
			<th scope="col"><?php echo JText::_('Unpublished'); ?></th>
			<th scope="col"><?php echo JText::_('Removed'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="published"><a href="index.php?option=com_resources&amp;c=resources&amp;status=1" title="<?php echo JText::_('View published resources'); ?>"><?php echo $this->published; ?></a></td>
			<td class="unpublished"><a href="index.php?option=com_resources&amp;c=resources&amp;status=0" title="<?php echo JText::_('View unpublished resources'); ?>"><?php echo $this->unpublished; ?></a></td>
			<td class="removed"><a href="index.php?option=com_resources&amp;c=resources&amp;status=4" title="<?php echo JText::_('View removed resources'); ?>"><?php echo $this->removed; ?></a></td>
		</tr>
	</tbody>
</table>