<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
				<td><?php echo Date::of($this->xmessage->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<td><?php
				if (substr($this->xmessage->get('type'), -8) == '_message')
				{
					$from = Lang::txt('JANONYMOUS');
					if (!$this->xmessage->anonymous)
					{
						$u = $this->xmessage->creator;
						$from = '<a href="'.Route::url('index.php?option=' . $this->option . '&id=' . $u->get('id')) . '">' . $u->get('name') . '</a>' . "\n";
					}
				}
				else
				{
					$from = Lang::txt('PLG_MEMBERS_MESSAGES_SYSTEM', $this->xmessage->get('component'));
				}
				echo $from;
				?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<td>
					<?php
						$subject = stripslashes($this->xmessage->get('subject'));
						if ($this->xmessage->get('component') == 'support')
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
