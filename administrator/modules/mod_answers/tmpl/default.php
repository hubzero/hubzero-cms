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
<table class="answers-stats-overview" summary="<?php echo JText::_('Overview of question and answer stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('Open'); ?></th>
			<th scope="col"><?php echo JText::_('Closed'); ?></th>
			<th scope="col"><?php echo JText::_('Last 24 hours'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="open"><a href="index.php?option=com_answers&amp;filterby=open" title="<?php echo JText::_('View open questions'); ?>"><?php echo $this->open; ?></a></td>
			<td class="closed"><a href="index.php?option=com_answers&amp;filterby=closed" title="<?php echo JText::_('View closed questions'); ?>"><?php echo $this->closed; ?></a></td>
			<td class="newest"><a href="index.php?option=com_answers&amp;filterby=all&amp;sortby=date" title="<?php echo JText::_('View newest questions'); ?>"><?php echo $this->pastDay; ?></a></td>
		</tr>
	</tbody>
</table>
<?php if ($this->params->get('showMine', 0)) { ?>
<table class="answers-stats-overview my-questions" summary="<?php echo JText::_('Overview of my question and answer stats'); ?>">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('My Questions (open)'); ?></th>
			<th scope="col"><?php echo JText::_('My Questions (closed)'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="myopen"><a href="index.php?option=com_answers&amp;filterby=open&amp;created_by=<?php echo $this->username ?>" title="<?php echo JText::_('View my open questions'); ?>"><?php echo $this->myopen; ?></a></td>
			<td class="myclosed"><a href="index.php?option=com_answers&amp;filterby=closed&amp;created_by=<?php echo $this->username ?>" title="<?php echo JText::_('View my closed questions'); ?>"><?php echo $this->myclosed; ?></a></td>
		</tr>
	</tbody>
</table>
<?php } ?>