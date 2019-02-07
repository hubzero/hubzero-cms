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

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_UNITS'), 'courses');
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
Toolbar::help('units');

$this->css();

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_COURSES_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
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
		<caption>
			(<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=offerings&course=' . $this->course->get('id')); ?>">
				<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
			</a>)
			<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=offerings&course=' . $this->course->get('id')); ?>">
				<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
			</a>:
			<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_TITLE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_COURSES_COL_ALIAS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_COURSES_COL_STATE'); ?></th>
				<th scope="col" class="priority-3" colspan="2"><?php echo Lang::txt('COM_COURSES_COL_ORDERING'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_ASSET_GROUPS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_ASSETS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
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
		<?php
		$i = 0;
		$k = 0;
		$n = $this->rows->total();
		foreach ($this->rows as $row)
		{
			$assetgroups = $row->assetgroups()->total();
			$assets = $row->assets()->total();

			switch ($row->get('state'))
			{
				case 1:
					$class = 'publish';
					$task = 'unpublish';
					$alt = Lang::txt('COM_COURSES_PUBLISHED');
				break;
				case 2:
					$class = 'expire';
					$task = 'publish';
					$alt = Lang::txt('COM_COURSES_TRASHED');
				break;
				case 0:
					$class = 'unpublish';
					$task = 'publish';
					$alt = Lang::txt('COM_COURSES_UNPUBLISHED');
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
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
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task . '&offering=' . $this->offering->get('id') . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COURSES_SET_TASK',$task);?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php
					$prv = $this->rows->fetch('prev');
					$prev = 0;
					if (is_object($prv))
					{
						$prev = $prv->get('ordering');
					}
					echo $pageNav->orderUpIcon($i, ($row->get('ordering') != $prev)); ?>
				</td>
				<td class="priority-3">
					<?php
					$nxt = $this->rows->fetch('next');
					$next = 0;
					if (is_object($nxt))
					{
						$next = $nxt->get('ordering');
					}
					echo $pageNav->orderDownIcon($i, $n, ($row->get('ordering') != $next)); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $assetgroups > 0) { ?>
						<a class="glyph list" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=assetgroups&unit=' . $row->get('id')); ?>">
							<?php echo $assetgroups; ?>
						</a>
					<?php } else { ?>
						<?php echo $assetgroups; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=assetgroups&unit=' . $row->get('id') . '&task=add'); ?>">
							<span><?php echo Lang::txt('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="glyph assets" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $assets; ?>
						</a>
					<?php } else { ?>
						<span class="glyph assets">
							<?php echo $assets; ?>
						</span>
					<?php } ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
