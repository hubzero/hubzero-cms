<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	<p><?php echo nl2br($this->escape($this->application->get('description'))); ?></p>
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