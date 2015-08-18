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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_OFFERINGS'), 'courses.png');
if ($canDo->get('core.create'))
{
	Toolbar::custom('copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
	Toolbar::spacer();
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_COURSES_DELETE_CONFIRM', 'delete');
}
Toolbar::spacer();
Toolbar::help('offerings');

Html::behavior('tooltip');
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
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo Lang::txt('COM_COURSES_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');$('#filter-state').val('-1');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
			<select name="state" id="filter-state" onchange="this.form.submit();">
				<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ALL_STATES'); ?></option>
				<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
				<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
				<option value="3"<?php if ($this->filters['state'] == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></option>
				<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<caption>
			(<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>)
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_COURSES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_COURSES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_COURSES_COL_STARTS', 'publish_up', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_COURSES_COL_ENDS', 'publish_down', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_COURSES_COL_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_SECTIONS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_COURSES_COL_ENROLLMENT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_UNITS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_COURSES_COL_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
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
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	$units    = $row->units(array('count' => true));
	$students = 0;

	$s = $row->sections();
	if ($s->total() >  0)
	{
		$sids = array();
		foreach ($s as $section)
		{
			$sids[] = $section->get('id');
		}

		$students = $row->members(array(
						'count' => true,
						'student' => 1,
						'section_id' => $sids
					));
	}

	$pages    = $row->pages(array('count' => true, 'active' => array(0, 1)));
	$sections = $row->sections(array('count' => true));
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</span>
				<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00') ? Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_COURSES_NO_DATE'); ?>
				</td>
				<td class="priority-4">
					<?php echo ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00') ? Date::of($row->get('publish_down'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_COURSES_NEVER'); ?>
				</td>
				<td class="priority-3">
				<?php if ($canDo->get('core.edit.state')) { ?>
					<?php if ($row->get('state') == 1) { ?>
					<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=unpublish&course=' . $this->course->get('id') . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_UNPUBLISHED')); ?>">
						<span class="state publish">
							<span class="text"><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 2) { ?>
					<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&course=' . $this->course->get('id') . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
						<span class="state trash">
							<span class="text"><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 3) { ?>
					<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&course=' . $this->course->get('id') . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
						<span class="state pending">
							<span class="text"><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 0) { ?>
					<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&course=' . $this->course->get('id') . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
						<span class="state unpublish">
							<span class="text"><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></span>
						</span>
					</a>
					<?php } ?>
				<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $sections > 0) { ?>
						<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=sections&offering=' . $row->get('id')); ?>">
							<?php echo $sections; ?>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<?php echo $sections; ?>
						</span>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=sections&offering=' . $row->get('id') . '&task=add'); ?>">
							<span><?php echo Lang::txt('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.manage')) { ?>
						<a class="glyph member" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=students&offering=' . $row->get('id') . '&section=0'); ?>">
							<?php echo $students; ?>
						</a>
					<?php } else { ?>
						<span class="glyph member">
							<?php echo $students; ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $units > 0) { ?>
						<a class="glyph list" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=units&offering=' . $row->get('id')); ?>">
							<?php echo $units; ?>
						</a>
					<?php } else { ?>
						<?php echo $units; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=units&offering=' . $row->get('id') . '&task=add'); ?>">
							<span><?php echo Lang::txt('COM_COURSES_ADD'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.manage') && $pages > 0) { ?>
						<a class="glyph list" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=pages&offering=' . $row->get('id')); ?>">
							<?php echo $pages; ?>
						</a>
					<?php } else { ?>
						<?php echo $pages; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=pages&course=' . $this->course->get('id') . '&offering=' . $row->get('id') . '&task=add'); ?>">
							<span><?php echo Lang::txt('COM_COURSES_ADD'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
	$i++;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>