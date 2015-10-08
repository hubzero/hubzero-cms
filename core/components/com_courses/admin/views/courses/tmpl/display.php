<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES'), 'courses.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_courses', '550');
	Toolbar::spacer();
}
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
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('courses');

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
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('COM_COURSES_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_COURSES_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-state').val('-1');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" onchange="this.form.submit();">
					<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
					<option value="3"<?php if ($this->filters['state'] == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></option>
					<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
		 	<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_COURSES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_COURSES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_COURSES_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_COURSES_COL_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_COURSES_COL_CERTIFICATE'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_COURSES_COL_MANAGERS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_OFFERINGS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
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
			$offerings = $row->offerings(array('count' => true));
			$pages     = $row->pages(array('count' => true, 'active' => array(0, 1)));

			$params = new \Hubzero\Config\Registry($row->get('params'));
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('alias'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('title')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('title')); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('alias')); ?>
						</a>
					<?php } else { ?>
						<?php echo $this->escape($row->get('alias')); ?>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<?php if ($row->get('state') == 1) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unpublish&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_UNPUBLISHED')); ?>">
								<span class="state publish">
									<span class="text"><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></span>
								</span>
							</a>
						<?php } else if ($row->get('state') == 2) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state trash">
									<span class="text"><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></span>
								</span>
							</a>
						<?php } else if ($row->get('state') == 3) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state pending">
									<span class="text"><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></span>
								</span>
							</a>
						<?php } else if ($row->get('state') == 0) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state unpublish">
									<span class="text"><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></span>
								</span>
							</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php if ($row->certificate()->exists() && $row->certificate()->hasFile()) { ?>
						<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=certificates&course=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_COURSES_CERTIFICATE_SET'); ?>">
							<span class="state yes">
								<span class="text"><?php echo Lang::txt('COM_COURSES_CERTIFICATE_SET'); ?></span>
							</span>
						</a>
					<?php } else { ?>
						<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=certificates&course=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_COURSES_CERTIFICATE_NOT_SET'); ?>">
							<span class="state no">
								<span class="text"><?php echo Lang::txt('COM_COURSES_CERTIFICATE_NOT_SET'); ?></span>
							</span>
						</a>
					<?php } ?>
				</td>
				<td class="priority-3">
					<span class="glyph member">
						<?php echo $row->managers(array('count' => true)); ?>
					</span>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $offerings > 0) { ?>
						<a class="glyph list" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=offerings&course=' . $row->get('id')); ?>">
							<?php echo $offerings; ?>
						</a>
					<?php } else { ?>
						<?php echo $offerings; ?>
						<?php if ($canDo->get('core.manage')) { ?>
							&nbsp;
							<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=offerings&course=' . $row->get('id') . '&task=add'); ?>">
								<span>[ + ]</span>
							</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $pages > 0) { ?>
						<a class="glyph list" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pages&course=' . $row->get('id') . '&offering=0'); ?>">
							<?php echo $pages; ?>
						</a>
					<?php } else { ?>
						<?php echo $pages; ?>
						<?php if ($canDo->get('core.manage')) { ?>
							&nbsp;
							<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pages&course=' . $row->get('id') . '&offering=0&task=add'); ?>">
								<span>[ + ]</span>
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

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>