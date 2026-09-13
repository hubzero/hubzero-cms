<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_KARMA_LEDGER'); ?></h2>
</header>

<section class="main section">

	<p><a href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_KARMA_BACK'); ?></a></p>

<?php if (!count($this->rows)) { ?>
	<p><?php echo Lang::txt('COM_KARMA_RECENT_NONE'); ?></p>
<?php } else { ?>
	<table class="karma-ledger">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_WHEN'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_WHAT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_DELTA'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_STATE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->rows as $row) { ?>
			<tr<?php if (!$row->isActive()) { echo ' class="reversed"'; } ?>>
				<td><time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></td>
				<td><?php echo $this->escape($row->get('rule')); ?></td>
				<td><?php echo ($row->get('applied') > 0 ? '+' : '') . $this->escape($row->get('applied')); ?></td>
				<td><?php echo Lang::txt($row->isActive() ? 'COM_KARMA_STATE_ACTIVE' : 'COM_KARMA_STATE_REVERSED'); ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>
<?php } ?>

</section>
