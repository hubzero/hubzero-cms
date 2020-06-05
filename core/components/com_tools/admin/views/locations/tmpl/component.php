<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('modal');

$this->js('locations.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="<?php echo !$this->filters['zone'] ? 9 : 8; ?>" class="align-right">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=add&zone=' . $this->filters['zone'] . '&tmpl=' . $this->filters['tmpl']); ?>" class="button edit-asset" rel="{type: 'iframe', size: {x: 570, y: 550}}"><?php echo Lang::txt('COM_TOOLS_ADD_LOCATION'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_ID'); ?></th>
			<?php if (!$this->filters['zone']) { ?>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php } ?>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_IP_FROM', 'ipFROM', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_IP_TO', 'ipTO', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_CONTINENT', 'continent', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_COUNTRY', 'countrySHORT', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_REGION', 'ipREGION', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_CITY', 'ipCITY', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->escape($row->get('id')); ?>
					<input class="hide" type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
			<?php if (!$this->filters['zone']) { ?>
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('zone'))); ?></span>
					</a>
				</td>
			<?php } ?>
				<td>
					<?php if ($row->get('ipFROM') != 0000000000) : ?>
						<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
							<?php echo $this->escape(stripslashes(long2ip($row->get('ipFROM')))); ?>
						</a>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($row->get('ipTO') != 0000000000) : ?>
						<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
							<?php echo $this->escape(stripslashes(long2ip($row->get('ipTO')))); ?>
						</a>
					<?php endif; ?>
				</td>
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
						<?php echo $this->escape(stripslashes($row->get('continent'))); ?>
					</a>
				</td>
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('countrySHORT'))); ?></span>
					</a>
				</td>
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('ipREGION'))); ?></span>
					</a>
				</td>
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl']); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('ipCITY'))); ?></span>
					</a>
				</td>
				<td>
					<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=remove&id=' . $row->get('id') . '&tmpl=' . $this->filters['tmpl'] . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo Lang::txt('[ x ]'); ?></span>
					</a>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="zone" value="<?php echo $this->escape($this->filters['zone']); ?>" />
	<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->filters['tmpl']); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
