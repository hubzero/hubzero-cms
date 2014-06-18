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
defined('_JEXEC') or die('Restricted access');

?>
<div class="hub-mail">
	<table class="hub-message">
		<thead>
			<tr>
				<th colspan="2"><?php echo JText::_('PLG_MEMBERS_MESSAGES_DETAILS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
				<td><?php echo JHTML::_('date', $this->xmessage->created, JText::_('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<td><?php echo $this->from; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<td>
					<?php
						$subject = stripslashes($this->xmessage->subject);
						if ($this->xmessage->component == 'support')
						{
							$fg = explode(' ', $subject);
							$fh = array_pop($fg);
							echo implode(' ', $fg);
						}
						else
						{
							echo $this->escape($subject);
						}
					?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_MESSAGE'); ?></th>
				<td><?php echo $this->xmessage->message; ?></td>
			</tr>
		</tbody>
	</table>
</div>
