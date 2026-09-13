<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="karma-profile">

	<h3><?php echo Lang::txt('PLG_MEMBERS_KARMA'); ?></h3>

<?php if (!$this->scales) { ?>
	<p><?php echo Lang::txt('PLG_MEMBERS_KARMA_NOTHING'); ?></p>
<?php } else { ?>
	<dl class="karma-scales">
<?php foreach ($this->scales as $entry) { ?>
		<dt><?php echo $this->escape($entry['scale']->get('title')); ?></dt>
		<dd><?php echo $this->escape($entry['value']); ?></dd>
<?php } ?>
	</dl>
<?php } ?>

<?php if ($this->isSelf) { ?>
	<?php if ($this->standing) { ?>
	<h4><?php echo Lang::txt('PLG_MEMBERS_KARMA_STANDING'); ?></h4>
	<dl class="karma-standing">
	<?php foreach ($this->standing as $alias => $value) { ?>
		<dt><?php echo $this->escape($alias); ?></dt>
		<dd><?php echo $this->escape(is_null($value) ? Lang::txt('PLG_MEMBERS_KARMA_UNSET') : $value); ?></dd>
	<?php } ?>
	</dl>
	<?php } ?>

	<?php if (!empty($this->wallets) && count($this->wallets)) { ?>
	<h4><?php echo Lang::txt('PLG_MEMBERS_KARMA_MODERATION'); ?></h4>
	<table class="karma-wallets">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_KARMA_WHERE'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_KARMA_CREDITS'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_KARMA_DONE'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_KARMA_RAISED'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_KARMA_LOWERED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->wallets as $wallet) { ?>
			<tr>
				<td><?php echo $this->escape($wallet->get('item_type')); ?></td>
				<td><?php echo (int) $wallet->get('credits'); ?></td>
				<td><?php echo (int) $wallet->get('total_moderations'); ?></td>
				<td><?php echo (int) $wallet->get('up_moderations'); ?></td>
				<td><?php echo (int) $wallet->get('down_moderations'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p class="hint"><?php echo Lang::txt('PLG_MEMBERS_KARMA_PRIVATE'); ?></p>
	<?php } ?>

	<p><a href="<?php echo Route::url('index.php?option=com_karma'); ?>"><?php echo Lang::txt('PLG_MEMBERS_KARMA_MANAGE'); ?></a></p>
<?php } ?>

</div>
