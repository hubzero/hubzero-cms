<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Karma\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_KARMA_GATES'), 'karma.png');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_SCALES'), Route::url('index.php?option=' . $this->option . '&controller=scales'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_RULES'), Route::url('index.php?option=' . $this->option . '&controller=rules'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_GATES'), Route::url('index.php?option=' . $this->option . '&controller=gates'), true);
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_LEDGER'), Route::url('index.php?option=' . $this->option . '&controller=ledger'));
Submenu::addEntry(Lang::txt('COM_KARMA_SUBMENU_REVIEW'), Route::url('index.php?option=' . $this->option . '&controller=review'));

$scaleTitles = array();
foreach ($this->scales as $scale)
{
	$scaleTitles[$scale->get('id')] = $scale->get('title');
}

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=gates'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KARMA_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_TITLE'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_KARMA_COL_SCALE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_BANDS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_KARMA_COL_DEFAULT'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=gates&task=edit&id[]=' . $row->get('id')); ?>"><code><?php echo $this->escape($row->get('alias')); ?></code></a>
<?php } else { ?>
					<code><?php echo $this->escape($row->get('alias')); ?></code>
<?php } ?>
				</td>
				<td><?php echo $this->escape($row->get('title')); ?></td>
				<td class="priority-3"><?php echo $this->escape(isset($scaleTitles[$row->get('scale_id')]) ? $scaleTitles[$row->get('scale_id')] : Lang::txt('COM_KARMA_SCALE_MISSING')); ?></td>
				<td><code><?php echo $this->escape($row->get('bands')); ?></code></td>
				<td class="priority-4"><?php echo $this->escape($row->get('default_value')); ?></td>
			</tr>
	<?php
	$i++;
}
?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="gates" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
