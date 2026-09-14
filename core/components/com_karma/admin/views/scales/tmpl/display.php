<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Karma\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_KARMA_SCALES'), 'karma.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
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

Html::behavior('tooltip');

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'), true);
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'));

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=scales'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_KARMA_FILTER_SEARCH_SCALES'); ?>" />

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
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_KARMA_COL_BOUNDS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_KARMA_COL_DECAY'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_KARMA_COL_VISIBILITY'); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_KARMA_COL_BALANCES'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	$entries = $row->ledgerCount();
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=scales&task=edit&id[]=' . $row->get('id')); ?>"><?php echo $this->escape($row->get('title')); ?></a>
<?php } else { ?>
					<?php echo $this->escape($row->get('title')); ?>
<?php } ?>
				</td>
				<td><code><?php echo $this->escape($row->get('alias')); ?></code></td>
				<td class="priority-3">
					<?php echo $this->escape($row->get('floor')); ?> &hellip; <?php echo $this->escape($row->get('ceiling')); ?>
					<span class="hint">(<?php echo Lang::txt('COM_KARMA_COL_INITIAL', $this->escape($row->get('initial'))); ?>)</span>
				</td>
				<td class="priority-4">
<?php if ($row->decays()) { ?>
					<?php echo Lang::txt('COM_KARMA_DECAY_SUMMARY', $this->escape($row->get('decay_per_day')), $this->escape($row->get('decay_after_days'))); ?>
<?php } else { ?>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_DECAY_NONE'); ?></span>
<?php } ?>
				</td>
				<td class="priority-2">
					<?php echo Lang::txt('COM_KARMA_VISIBILITY_SELF_' . strtoupper($row->get('visibility_self'))); ?>
					<span class="hint">/ <?php echo Lang::txt('COM_KARMA_VISIBILITY_PUBLIC_' . strtoupper($row->get('visibility_public'))); ?></span>
				</td>
				<td class="priority-5"><?php echo number_format($entries); ?></td>
				<td>
					<a class="state <?php echo ($row->get('state') ? 'publish' : 'unpublish'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=scales&task=' . ($row->get('state') ? 'unpublish' : 'publish') . '&id[]=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
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
	<input type="hidden" name="controller" value="scales" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
