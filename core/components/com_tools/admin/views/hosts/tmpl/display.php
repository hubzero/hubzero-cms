<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS'), 'tools');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('hosts');

$this->css();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_NAME', 'hostname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_PROVISIONS', 'provisions', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_USES', 'uses', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_COL_BROKEN_CONTAINERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows)
{
	$db = \Components\Tools\Helpers\Utils::getMWDBO();

	$i = 0;
	foreach ($this->rows as $row)
	{
		$list = array();
		for ($k=0; $k<count($this->hosttypes); $k++)
		{
			$r = $this->hosttypes[$k];
			$list[$r->name] = (int)$r->value & (int)$row->provisions;
		}
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->hostname; ?>" class="checkbox-toggle" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&hostname=' . $row->hostname); ?>">
						<span><?php echo $this->escape($row->hostname); ?></span>
					</a>
				</td>
				<td>
				<?php
					foreach ($list as $key => $value)
					{
						if ($value != '0')
						{
							echo '<strong>';
						}
						?>
					<a class="<?php echo ($value != '0') ? 'active' : 'inactive'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=toggle&hostname=' . $row->hostname . '&item=' . $key); ?>">
						<span><?php echo $this->escape($key); ?></span>
					</a>
						<?php
						if ($value != '0')
						{
							echo '</strong>';
						}
						echo '<br />';
					}
				?>
				</td>
				<td class="priority-2">
					<a class="state <?php echo ($row->status == 'up') ? 'publish' : 'unpublish'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&hostname=' . $row->hostname); ?>">
						<span><?php echo $this->escape($row->status); ?></span>
					</a>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->uses); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape(stripslashes($row->zone)); ?>
				</td>
				<td class="priority-3">
					<?php
						$db->setQuery("SELECT count(*) FROM `display` WHERE `status`='broken' AND `hostname`=" . $db->quote($row->hostname));
						echo $db->loadResult();
					?>
				</td>
			</tr>
<?php
		$i++;
	}
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
