<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_KARMA_GRANTS'), 'karma.png');

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GRANTS'), Route::url('index.php?option=' . $this->option . '&controller=grants'), true);

?>

<p class="info"><?php echo Lang::txt('COM_KARMA_GRANTS_INTRO', (int) $this->days); ?></p>

<?php if (!count($this->totals)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_KARMA_GRANTS_NONE'); ?></p>
<?php } else { ?>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_TYPE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_SCHEME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_PASSES'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_IDLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_ISSUED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_SPENT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_EXPIRED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_RATIO'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->totals as $total) { ?>
			<tr>
				<td><?php echo $this->escape($total->item_type); ?></td>
				<td><?php echo $this->escape($total->grantor); ?></td>
				<td><?php echo (int) $total->passes; ?></td>
				<td><?php echo (int) $total->idle; ?></td>
				<td><?php echo (int) $total->issued; ?></td>
				<td><?php echo (int) $total->spent; ?></td>
				<td><?php echo (int) $total->expired; ?></td>
				<td><?php echo is_null($total->ratio) ? '&mdash;' : $total->ratio; ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>

<?php foreach ($this->totals as $total) { ?>
<?php if (!$total->issued) { ?>
	<p class="error"><?php echo Lang::txt('COM_KARMA_GRANTS_ISSUING_NOTHING', $this->escape($total->item_type), $this->escape($total->grantor)); ?></p>
<?php } elseif (!is_null($total->ratio) && $total->ratio < 0.3) { ?>
	<p class="warning"><?php echo Lang::txt('COM_KARMA_GRANTS_TOO_LOOSE', $this->escape($total->item_type)); ?></p>
<?php } elseif (!is_null($total->ratio) && $total->ratio > 0.9 && !$total->expired) { ?>
	<p class="warning"><?php echo Lang::txt('COM_KARMA_GRANTS_TOO_TIGHT', $this->escape($total->item_type)); ?></p>
<?php } ?>
<?php } ?>

	<h3><?php echo Lang::txt('COM_KARMA_GRANTS_EACH_PASS'); ?></h3>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_WHEN'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_TYPE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_ELIGIBLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_GRANTED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_ISSUED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_GRANTS_NOTE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->rows as $row) { ?>
			<tr>
				<td><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
				<td><?php echo $this->escape($row->get('item_type')); ?></td>
				<td><?php echo (int) $row->get('eligible'); ?></td>
				<td><?php echo (int) $row->get('granted'); ?></td>
				<td><?php echo (int) $row->get('credits_issued'); ?></td>
				<td><?php echo $this->escape($row->get('note')); ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
<?php } ?>
