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

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_PAGES'), 'courses');
if ($canDo->get('core.create'))
{
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
Toolbar::help('pages');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('COM_COURSES_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_COURSES_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-active').val('-1');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-active"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
				<select name="active" id="filter-active" onchange="this.form.submit();">
					<option value="-1"<?php if ($this->filters['active'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['active'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['active'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<caption>
			<?php if ($this->course->exists()) { ?>
				(<a href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
					<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
				</a>)
				<a href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
					<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
				</a>:
				<?php if ($this->offering->exists()) { ?>
				<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=offerings&course=' . $this->course->get('id')); ?>">
					<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
				</a>:
				<?php } ?>
			<?php } else { ?>
				<?php echo Lang::txt('COM_COURSES_PAGES_USER_GUIDE'); ?>:
			<?php } ?>
			<?php echo Lang::txt('COM_COURSES_PAGES'); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_TITLE'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_COURSES_COL_STATE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_ORDERING'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php
					// Initiate paging
					$pageNav = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					echo $pageNav->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if (count($this->rows) > 0) { ?>
			<?php

			$i = 0;
			$rows = array();
			foreach ($this->rows as $key => $page)
			{
				$rows[$i] = $page;
				$i++;
			}

			$i = 0;
			$n = count($rows);
			foreach ($rows as $page)
			{
				?>
				<tr>
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($page->get('id')); ?>" onclick="Joomla.isChecked(this.checked);" />
					</td>
					<td class="priority-3">
						<?php echo $this->escape($page->get('id')); ?>
					</td>
					<td>
						<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $this->escape($page->get('id'))); ?>">
								<?php echo $this->escape(stripslashes($page->get('title'))); ?>
							</a>
						<?php } else { ?>
							<span>
								<?php echo $this->escape(stripslashes($page->get('title'))); ?>
							</span>
						<?php } ?>
					</td>
					<td class="priority-2">
						<?php if ($canDo->get('core.edit.state')) { ?>
							<?php if ($page->get('active') == 1) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=unpublish&id=' . $page->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_UNPUBLISHED')); ?>">
								<span class="state publish">
									<span class="text"><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></span>
								</span>
							</a>
							<?php } else if ($page->get('active') == 2) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&id=' . $page->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state trash">
									<span class="text"><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></span>
								</span>
							</a>
							<?php } else if ($page->get('active') == 3) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&id=' . $page->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state pending">
									<span class="text"><?php echo Lang::txt('COM_COURSES_DRAFT'); ?></span>
								</span>
							</a>
							<?php } else if ($page->get('active') == 0) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=publish&id=' . $page->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK', Lang::txt('COM_COURSES_PUBLISHED')); ?>">
								<span class="state unpublish">
									<span class="text"><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></span>
								</span>
							</a>
							<?php } ?>
						<?php } ?>
					</td>
					<td class="order">
						<?php echo $page->get('ordering'); ?>
						<span><?php echo $pageNav->orderUpIcon($i, isset($rows[$i - 1]), 'orderup', 'COM_COURSES_MOVE_UP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon($i, $n, isset($rows[$i + 1]), 'orderdown', 'COM_COURSES_MOVE_DOWN', true); ?></span>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
		<?php } else { ?>
			<tr>
				<td colspan="5"><?php echo Lang::txt('COM_COURSES_NONE_FOUND'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="course" value="<?php echo $this->escape($this->filters['course']); ?>" />
	<input type="hidden" name="offering" value="<?php echo $this->escape($this->filters['offering']); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>