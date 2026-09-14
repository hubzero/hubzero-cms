<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Karma\Ledger as Entry;

$canDo = \Components\Karma\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_KARMA_LEDGER'), 'karma.png');
if ($canDo->get('core.edit.state'))
{
	Toolbar::custom('reverse', 'cancel', '', 'COM_KARMA_LEDGER_REVERSE', true);
}

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'), true);
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GRANTS'), Route::url('index.php?option=' . $this->option . '&controller=grants'));

$scaleTitles = array();
foreach ($this->scales as $scale)
{
	$scaleTitles[$scale->get('id')] = $scale->get('title');
}

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=ledger'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_subject"><?php echo Lang::txt('COM_KARMA_FILTER_SUBJECT'); ?>:</label>
		<input type="number" name="subject" id="filter_subject" class="filter" size="8" value="<?php echo $this->escape($this->filters['subject'] ?: ''); ?>" />

		<label for="filter_actor"><?php echo Lang::txt('COM_KARMA_FILTER_ACTOR'); ?>:</label>
		<input type="number" name="actor" id="filter_actor" class="filter" size="8" value="<?php echo $this->escape($this->filters['actor'] ?: ''); ?>" />

		<label for="filter_rule"><?php echo Lang::txt('COM_KARMA_FILTER_RULE'); ?>:</label>
		<select name="rule" id="filter_rule" class="filter filter-submit">
			<option value=""><?php echo Lang::txt('COM_KARMA_FILTER_RULE_ALL'); ?></option>
<?php foreach ($this->rules as $rule) { ?>
			<option value="<?php echo $this->escape($rule); ?>"<?php if ($this->filters['rule'] == $rule) { echo ' selected="selected"'; } ?>><?php echo $this->escape($rule); ?></option>
<?php } ?>
		</select>

		<label for="filter_scale"><?php echo Lang::txt('COM_KARMA_FILTER_SCALE'); ?>:</label>
		<select name="scale" id="filter_scale" class="filter filter-submit">
			<option value="0"><?php echo Lang::txt('COM_KARMA_FILTER_SCALE_ALL'); ?></option>
<?php foreach ($this->scales as $scale) { ?>
			<option value="<?php echo $this->escape($scale->get('id')); ?>"<?php if ($this->filters['scale'] == $scale->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($scale->get('title')); ?></option>
<?php } ?>
		</select>

		<label for="filter_state"><?php echo Lang::txt('COM_KARMA_FILTER_STATE'); ?>:</label>
		<select name="state" id="filter_state" class="filter filter-submit">
			<option value="-1"><?php echo Lang::txt('COM_KARMA_FILTER_STATE_ALL'); ?></option>
			<option value="1"<?php if ($this->filters['state'] === '1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_KARMA_STATE_ACTIVE'); ?></option>
			<option value="0"<?php if ($this->filters['state'] === '0') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_KARMA_STATE_REVERSED'); ?></option>
		</select>

		<label for="filter_from"><?php echo Lang::txt('COM_KARMA_FILTER_FROM'); ?>:</label>
		<input type="date" name="from" id="filter_from" class="filter" value="<?php echo $this->escape($this->filters['from']); ?>" />

		<label for="filter_to"><?php echo Lang::txt('COM_KARMA_FILTER_TO'); ?>:</label>
		<input type="date" name="to" id="filter_to" class="filter" value="<?php echo $this->escape($this->filters['to']); ?>" />

		<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_WHEN', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_SUBJECT', 'subject_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_KARMA_COL_ACTOR', 'actor_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_RULE', 'rule', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_DELTA'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_KARMA_COL_APPLIED'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_KARMA_COL_SOURCE'); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_KARMA_COL_SCALE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_STATE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?><?php if (!$row->isActive()) { echo ' reversed'; } ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td><time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=ledger&subject=' . (int) $row->get('subject_id')); ?>"><?php echo $this->escape($row->get('subject_id')); ?></a>
				</td>
				<td class="priority-3">
<?php if ($row->get('actor_id')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=ledger&actor=' . (int) $row->get('actor_id')); ?>"><?php echo $this->escape($row->get('actor_id')); ?></a>
<?php } else { ?>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_ACTOR_SYSTEM'); ?></span>
<?php } ?>
				</td>
				<td><code><?php echo $this->escape($row->get('rule')); ?></code></td>
				<td><?php echo ($row->get('delta') > 0 ? '+' : '') . $this->escape($row->get('delta')); ?></td>
				<td class="priority-4">
<?php if ((float) $row->get('applied') != (float) $row->get('delta')) { ?>
					<strong title="<?php echo Lang::txt('COM_KARMA_APPLIED_CLAMPED'); ?>"><?php echo $this->escape($row->get('applied')); ?></strong>
<?php } else { ?>
					<?php echo $this->escape($row->get('applied')); ?>
<?php } ?>
				</td>
				<td class="priority-2">
<?php if ($row->get('source_type')) { ?>
					<code><?php echo $this->escape($row->get('source_type')); ?></code>#<?php echo $this->escape($row->get('source_id')); ?>
<?php } else { ?>
					<span class="hint">&mdash;</span>
<?php } ?>
				</td>
				<td class="priority-5"><?php echo $this->escape(isset($scaleTitles[$row->get('scale_id')]) ? $scaleTitles[$row->get('scale_id')] : $row->get('scale_id')); ?></td>
				<td><?php echo Lang::txt($row->isActive() ? 'COM_KARMA_STATE_ACTIVE' : 'COM_KARMA_STATE_REVERSED'); ?></td>
			</tr>
	<?php
	$i++;
}
?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="ledger" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
