<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('contributor');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUTHORS'), 'addedit.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_ID', 'authorid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_COL_MEMBER'); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_RESOURCES', 'resources', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	if ($row->authorid > 0)
	{
		$stickyTask = '1';
		$stickyAlt = Lang::txt('JYES');
		$scls = 'member';
	}
	else
	{
		$stickyTask = '0';
		$stickyAlt = Lang::txt('JNO');
		$scls = 'notmember';
	}
	if ($row->authorid > 0 && !$row->name)
	{
		$u = User::getInstance($row->authorid);
		$row->name = $u->get('name');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->authorid; ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->authorid; ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->authorid); ?>">
							<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_RESOURCES_UNKNOWN'); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_RESOURCES_UNKNOWN'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($row->authorid > 0) { ?>
						<a class="state <?php echo $scls; ?>" href="<?php echo Route::url('index.php?option=com_members&task=edit&id=' . $row->authorid); ?>">
							<span><?php echo $stickyAlt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $scls; ?>">
							<span><?php echo $stickyAlt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php echo Lang::txt('COM_RESOURCES_AUTHORS_NUM_RESOURCES', $this->escape(stripslashes($row->resources))); ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
