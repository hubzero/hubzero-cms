<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (substr($this->xmessage->type, -8) == '_message')
{
	$u = User::getInstance($this->xmessage->created_by);
	$from = '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $u->get('id')) . '">' . $u->get('name') . '</a>';
}
else
{
	$from = Lang::txt('System') . ' (' . $this->xmessage->component . ')';
}

?>
<div class="subject">
	<?php if (!$this->no_html): ?>
	<ul class="entries-menu">
		<li><a class="active" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
		<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
			<li><a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=new'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
		<?php } ?>
	</ul>
	<br class="clear" />
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>" method="post">
		<fieldset class="hub-mail">
			<table class="groups entries">
				<caption>
					<?php echo Lang::txt('PLG_GROUPS_MESSAGE'); ?>
					<?php if (!$this->no_html) : ?>
						<span>
							<small>( <a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>"><?php echo Lang::txt('&lsaquo; Back to Sent Messages'); ?></a> )</small>
						</span>
					<?php endif; ?>
				</caption>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_RECEIVED'); ?>:</th>
						<td><?php echo Date::of($this->xmessage->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_FROM'); ?>:</th>
						<td><?php echo $from; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SUBJECT'); ?>:</th>
						<td><?php echo stripslashes($this->xmessage->subject); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_MESSAGE'); ?>:</th>
						<td><?php echo $this->xmessage->message; ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</div><!-- // .subject -->

