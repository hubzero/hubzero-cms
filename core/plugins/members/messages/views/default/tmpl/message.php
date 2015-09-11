<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div class="hub-mail">
	<table class="hub-message">
		<thead>
			<tr>
				<th colspan="2"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DETAILS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
				<td><?php echo Date::of($this->xmessage->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<td><?php echo $this->from; ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
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
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MESSAGE'); ?></th>
				<td><?php echo $this->xmessage->message; ?></td>
			</tr>
		</tbody>
	</table>
</div>
