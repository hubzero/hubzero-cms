<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Publications\Helpers\Permissions::getActions('license');

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_LICENSES'), 'publications');
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
	Toolbar::save('makedefault', 'COM_PUBLICATIONS_MAKE_DEFAULT');
	Toolbar::publishList('changestatus', 'COM_PUBLICATIONS_PUBLISH_UNPUBLISH');
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList();
}

$this->css();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PUBLICATIONS_SEARCH'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th class="priority-4"><?php echo Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-3"><?php echo Html::grid('sort', 'COM_PUBLICATIONS_FIELD_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo Html::grid('sort', 'COM_PUBLICATIONS_FIELD_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-2"><?php echo Html::grid('sort', 'COM_PUBLICATIONS_FIELD_STATUS', 'active', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-2"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DEFAULT'); ?></th>
				<th><?php echo Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ORDER', 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$i = 0;
			$orderings = $this->rows->fieldsByKey('ordering');
			foreach ($this->rows as $row)
			{
				$class = $row->active == 1 ? 'on' : 'off';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
						<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->id; ?></label>
					</td>
					<td class="priority-4">
						<?php echo $row->id; ?>
					</td>
					<td class="priority-3">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape($row->name); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape($row->title); ?></span>
						</a>
					</td>
					<td class="priority-2 centeralign">
						<span class="state <?php echo $class; ?>">
							<span><?php echo Lang::txt('j' . $class); ?></span>
						</span>
					</td>
					<td class="priority-2 centeralign">
						<?php if ($row->main == 1) { ?>
							<span class="state default">
								<span><?php echo Lang::txt('JYES'); ?></span>
							</span>
						<?php } ?>
					</td>
					<td class="order">
						<?php if ($this->filters['sort'] == 'ordering') { ?>
							<span>
								<?php echo $this->rows->pagination->orderUpIcon($i, isset($orderings[$i-1])); ?>
							</span>
							<span>
								<?php echo $this->rows->pagination->orderDownIcon($i, $this->rows->pagination->total, isset($orderings[$i+1])); ?>
							</span>
						<?php } ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" disabled="disabled" class="text-area-order" />
					</td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
	<?php echo Html::input('token'); ?>
</form>