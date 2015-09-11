<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('modal');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
function setTask(task)
{
	$('#task').value = task;
}
jQuery(document).ready(function($){
	$('a.edit-asset').on('click', function(e) {
		e.preventDefault();

		window.parent.$.fancybox.open($(this).attr('href'), {type: 'iframe', size: {x: 570, y: 550}});
	});
});

</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="<?php echo (!$this->filters['zone'] ? 9 : 8); ?>" style="text-align:right;">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=add&zone=' . $this->filters['zone'] . '&tmpl=' . $this->filters['tmpl']); ?>" class="button edit-asset" rel="{type: 'iframe', size: {x: 570, y: 550}}"><?php echo Lang::txt('COM_TOOLS_ADD_LOCATION'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_ID'); ?></th>
			<?php if (!$this->filters['zone']) { ?>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_ZONE', 'zone', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php } ?>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_IP_FROM', 'ipFROM', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_IP_TO', 'ipTO', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_CONTINENT', 'continent', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_COUNTRY', 'countrySHORT', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_REGION', 'ipREGION', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_TOOLS_COL_CITY', 'ipCITY', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
					<input style="visibility:hidden;" type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
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
						<span><img src="components/<?php echo $this->option; ?>/assets/img/trash.png" width="15" height="15" alt="<?php echo Lang::txt('[ x ]'); ?>" /></span>
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

	<input type="hidden" name="zone" value="<?php echo $this->filters['zone']; ?>" />
	<input type="hidden" name="tmpl" value="<?php echo $this->filters['tmpl']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
