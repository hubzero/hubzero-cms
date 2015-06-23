<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="subject application">
	<table class="metadata">
		<tbody>
			<tr>
				<th><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_CLIENT_ID'); ?></th>
				<td><code><?php echo $this->application->get('client_id'); ?></code></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_CLIENT_SECRET'); ?></th>
				<td><code><?php echo $this->application->get('client_secret'); ?></code></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI'); ?></th>
				<td>
					<?php foreach (explode(' ', $this->application->get('redirect_uri')) as $uri) : ?>
						<code><?php echo $uri; ?></code> 
					<?php endforeach; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<hr />
	<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_DESCRIPTION'); ?></h3>
	<p><?php echo nl2br($this->escape($this->application->description())); ?></p>
	<hr />
	<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBERS'); ?></h3>
	<?php
		$team = $this->application->team();
		echo $this->view('_team')
				  ->set('members', $team)
				  ->set('cls', 'compact')
				  ->display();
	?>
</div>