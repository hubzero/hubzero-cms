<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Publications\Helpers\Permissions::getActions('category');

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_CATEGORIES'), 'category');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::editList();
	Toolbar::publishList('changestatus', Lang::txt('COM_PUBLICATIONS_CHANGE_STATUS'));
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList();
}

$this->css();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cotnroller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th class="priority-4"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_NAME'), 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-3"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'), 'contributable', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-2"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-4">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
					<span class="block">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') . ': ' . $this->escape($row->alias); ?> |
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS') . ': ' . $this->escape($row->url_alias); ?> |
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE') . ': ' . $this->escape($row->dc_type); ?>
					</span>
				</td>
				<td class="priority-3 centeralign">
					<span class="state <?php echo ($row->contributable == 1) ? 'yes' : 'no'; ?>">
						<span><?php echo ($row->contributable == 1) ? Lang::txt('JYES') : Lang::txt('JNO'); ?></span>
					</span>
				</td>
				<td class="priority-2 centeralign">
					<span class="state <?php echo ($row->state == 1) ? 'on' : 'off'; ?>">
						<span><?php echo ($row->state == 1) ? Lang::txt('COM_PUBLICATIONS_ON') : Lang::txt('COM_PUBLICATIONS_OFF'); ?></span>
					</span>
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