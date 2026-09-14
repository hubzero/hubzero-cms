<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_KARMA_REVIEW_ADMIN'), 'karma.png');

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'), true);

?>

<p class="info"><?php echo Lang::txt('COM_KARMA_REVIEW_ADMIN_INTRO'); ?></p>

<?php if (!count($this->rows)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_KARMA_REVIEW_NO_TYPES'); ?></p>
<?php } else { ?>

<?php foreach ($this->rows as $row) { ?>
	<div class="review-type">
		<h3><?php echo $this->escape($row->item_type); ?></h3>

		<table class="adminlist">
			<tbody>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_KARMA_REVIEW_STATE'); ?></th>
					<td><?php echo Lang::txt($row->enabled ? 'COM_KARMA_REVIEW_ON' : 'COM_KARMA_REVIEW_OFF_STATE'); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_KARMA_REVIEW_CONSENSUS'); ?></th>
					<td><?php echo (int) $row->consensus; ?></td>
				</tr>
				<tr class="<?php echo $row->has_volume ? 'has-volume' : 'no-volume'; ?>">
					<th scope="row"><?php echo Lang::txt('COM_KARMA_REVIEW_VOLUME'); ?></th>
					<td>
						<?php echo Lang::txt('COM_KARMA_REVIEW_VOLUME_VALUE', (int) $row->produced, (int) $row->needed); ?>
						<strong><?php
							echo Lang::txt($row->has_volume ? 'COM_KARMA_REVIEW_VOLUME_OK' : 'COM_KARMA_REVIEW_VOLUME_SHORT');
						?></strong>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_KARMA_REVIEW_UNRESOLVED'); ?></th>
					<td><?php echo (int) $row->pending; ?></td>
				</tr>
			</tbody>
		</table>

<?php if ($row->enabled && !$row->has_volume) { ?>
		<p class="error"><?php echo Lang::txt('COM_KARMA_REVIEW_ENABLED_TOO_EARLY'); ?></p>
<?php } elseif (!$row->enabled && !$row->has_volume) { ?>
		<p class="warning"><?php echo Lang::txt('COM_KARMA_REVIEW_LEAVE_IT_OFF'); ?></p>
<?php } elseif (!$row->enabled && $row->has_volume) { ?>
		<p class="info"><?php echo Lang::txt('COM_KARMA_REVIEW_COULD_ENABLE'); ?></p>
<?php } ?>

		<h4><?php echo Lang::txt('COM_KARMA_REVIEW_CONSEQUENCES'); ?></h4>

		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_REVIEW_FRACTION'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_REVIEW_MOD_CREDITS'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_REVIEW_MOD_KARMA'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_REVIEW_AGREED'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_REVIEW_DISAGREED'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach ($row->consequences->bands() as $fraction => $values) { ?>
				<tr>
					<td><?php echo Lang::txt('COM_KARMA_REVIEW_AT_LEAST', number_format((float) $fraction * 100, 0)); ?></td>
					<td><?php echo ($values[0] > 0 ? '+' : '') . (int) $values[0]; ?></td>
					<td><?php echo ($values[1] > 0 ? '+' : '') . (int) $values[1]; ?></td>
					<td><?php echo ($values[2] > 0 ? '+' : '') . (int) $values[2]; ?></td>
					<td><?php echo ($values[3] > 0 ? '+' : '') . (int) $values[3]; ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>

		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=review'); ?>" method="post" class="review-check">
			<label for="consequences-<?php echo $this->escape($row->item_type); ?>"><?php echo Lang::txt('COM_KARMA_REVIEW_CHECK_LABEL'); ?></label>
			<textarea name="consequences" id="consequences-<?php echo $this->escape($row->item_type); ?>" rows="6" cols="48"><?php
				echo $this->escape($row->consequences->toString());
			?></textarea>
			<input type="submit" class="btn" value="<?php echo Lang::txt('COM_KARMA_REVIEW_CHECK'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="review" />
			<input type="hidden" name="task" value="check" />
			<?php echo Html::input('token'); ?>
		</form>
	</div>
<?php } ?>

<?php } ?>
