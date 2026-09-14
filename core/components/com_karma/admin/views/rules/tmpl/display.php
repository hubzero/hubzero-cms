<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Karma\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_KARMA_RULES'), 'karma.png');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'), true);
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'));

$scaleTitles = array();
foreach ($this->scales as $scale)
{
	$scaleTitles[$scale->get('id')] = $scale->get('title');
}

// Rules nobody emits are dead configuration; sources nobody has priced earn
// nothing. Both are worth seeing on this screen.
$configured = array();
foreach ($this->rows as $row)
{
	$configured[$row->get('alias')] = true;
}
$unpriced = array_diff_key($this->declared, $configured);

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=rules'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />

		<label for="filter_scale"><?php echo Lang::txt('COM_KARMA_FILTER_SCALE'); ?>:</label>
		<select name="scale" id="filter_scale" class="filter filter-submit">
			<option value="0"><?php echo Lang::txt('COM_KARMA_FILTER_SCALE_ALL'); ?></option>
<?php foreach ($this->scales as $scale) { ?>
			<option value="<?php echo $this->escape($scale->get('id')); ?>"<?php if ($this->filters['scale'] == $scale->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($scale->get('title')); ?></option>
<?php } ?>
		</select>

		<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

<?php if ($unpriced) { ?>
	<p class="warning">
		<?php echo Lang::txt('COM_KARMA_RULES_UNPRICED', implode(', ', array_keys($unpriced))); ?>
	</p>
<?php } ?>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_KARMA_COL_SCALE'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_DELTA', 'delta', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_KARMA_COL_CAPS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_KARMA_COL_SOURCE'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	$emitter = isset($this->declared[$row->get('alias')]) ? $this->declared[$row->get('alias')] : null;
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=rules&task=edit&id[]=' . $row->get('id')); ?>"><?php echo $this->escape($row->get('title')); ?></a>
<?php } else { ?>
					<?php echo $this->escape($row->get('title')); ?>
<?php } ?>
				</td>
				<td><code><?php echo $this->escape($row->get('alias')); ?></code></td>
				<td class="priority-3"><?php echo $this->escape(isset($scaleTitles[$row->get('scale_id')]) ? $scaleTitles[$row->get('scale_id')] : Lang::txt('COM_KARMA_SCALE_MISSING')); ?></td>
				<td><?php echo ($row->get('delta') > 0 ? '+' : '') . $this->escape($row->get('delta')); ?></td>
				<td class="priority-4">
<?php if ($row->hasDailyCap() || $row->hasSourceCap()) { ?>
	<?php if ($row->hasDailyCap()) { ?><span><?php echo Lang::txt('COM_KARMA_CAP_DAILY', $this->escape($row->get('daily_cap'))); ?></span><?php } ?>
	<?php if ($row->hasSourceCap()) { ?><span><?php echo Lang::txt('COM_KARMA_CAP_SOURCE', $this->escape($row->get('per_source_cap'))); ?></span><?php } ?>
<?php } else { ?>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_CAP_NONE'); ?></span>
<?php } ?>
				</td>
				<td class="priority-2">
<?php if ($emitter) { ?>
					<?php echo $this->escape($emitter); ?>
<?php } else { ?>
					<span class="warning"><?php echo Lang::txt('COM_KARMA_RULE_NO_SOURCE'); ?></span>
<?php } ?>
				</td>
				<td>
					<a class="state <?php echo ($row->get('state') ? 'publish' : 'unpublish'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=rules&task=' . ($row->get('state') ? 'unpublish' : 'publish') . '&id[]=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo Lang::txt($row->get('state') ? 'JPUBLISHED' : 'JUNPUBLISHED'); ?></span>
					</a>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="rules" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
