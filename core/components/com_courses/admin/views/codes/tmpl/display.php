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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_COUPON_CODES'), 'courses.png');
if ($canDo->get('core.create'))
{
	Toolbar::appendButton('Popup', 'refresh', 'COM_COURSES_GENERATE', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&section=' . $this->section->get('id') . '&task=options&tmpl=component', 500, 200);

	Toolbar::spacer();
	Toolbar::custom('export', 'export', 'export', 'COM_COURSES_EXPORT_CODES', false);
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
			<button type="button" onclick="$('#filter_search').val('');$('#filter-redeemed').val('-1');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-redeemed"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
			<select name="redeemed" id="filter-redeemed" onchange="this.form.submit();">
				<option value="-1"<?php if ($this->filters['redeemed'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ALL_STATES'); ?></option>
				<option value="1"<?php if ($this->filters['redeemed'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_FILTER_REDEEMED'); ?></option>
				<option value="0"<?php if ($this->filters['redeemed'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_FILTER_UNREDEEMED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<caption>
			(<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=offerings&course=' . $this->course->get('id')); ?>">
				<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
			</a>)
			<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=offerings&course=' . $this->course->get('id')); ?>">
				<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
			</a>:
			<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=sections&offering=' . $this->offering->get('id')); ?>">
				<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
			</a>:
			<?php echo $this->escape(stripslashes($this->section->get('title'))); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_CODE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_COURSES_COL_CREATED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_EXPIRES'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_REDEEMED'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_COURSES_COL_REDEEMED_BY'); ?></th>
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
$k = 0;
foreach ($this->rows as $i => $row)
{
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
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</span>
				<?php } ?>
				</td>
				<td class="priority-4">
					<time datetime="<?php echo $row->get('created'); ?>"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<?php echo ($row->get('expires') && $row->get('expires') != '0000-00-00 00:00:00') ? Date::of($row->get('expires'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_COURSES_NEVER'); ?>
				</td>
			<?php if ($row->get('redeemed')) { ?>
				<td>
					<span class="state <?php echo (($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') || $row->get('redeemed_by')) ? 'yes' : 'no'; ?>">
						<span><?php echo ($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') ? '<time datetime="' . $row->get('redeemed') . '">' . Date::of($row->get('redeemed'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '</time>' : Lang::txt('JNO'); ?></span>
					</span>
				</td>
				<td class="priority-3">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=students&task=edit&section=' . $row->get('section_id') . '&id=' . $row->get('redeemed_by')); ?>">
						<?php echo $this->escape(stripslashes($row->redeemer()->get('name'))); ?>
					</a>
				</td>
			<?php } else { ?>
				<td colspan="2">
					<?php echo Lang::txt('COM_COURSES_UNREDEEMED'); ?>
				</td>
			<?php } ?>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="section" value="<?php echo $this->section->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
