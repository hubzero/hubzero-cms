<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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