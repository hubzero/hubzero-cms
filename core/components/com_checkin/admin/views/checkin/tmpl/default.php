<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CHECKIN_GLOBAL_CHECK_IN'), 'checkin.png');
if (User::authorise('core.admin', 'com_checkin'))
{
	Toolbar::custom('checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
	Toolbar::divider();
	Toolbar::preferences('com_checkin');
	Toolbar::divider();
}
Toolbar::help('checkin');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::url('index.php?option=com_checkin'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_CHECKIN_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>

	<table id="global-checkin" class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CHECKIN_DATABASE_TABLE', 'table', $listDirn, $listOrder); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CHECKIN_ITEMS_TO_CHECK_IN', 'count', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $table => $count): $i=0; ?>
				<tr class="row<?php echo $i%2; ?>">
					<td><?php echo Html::grid('id', $i, $table); ?></td>
					<td><?php echo Lang::txt('COM_CHECKIN_TABLE', $table); ?></td>
					<td><?php echo $count; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php 
					$pagination = $this->pagination(
						$this->total,
						$this->state->get('list.limit'),
						$this->state->get('list.start')
					);
					echo $pagination->render();
					?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo Html::input('token'); ?>
</form>
