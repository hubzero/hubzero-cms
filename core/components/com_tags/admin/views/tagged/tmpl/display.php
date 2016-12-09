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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_TAGGED'), 'tags');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('tagged');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-tbl"><?php echo Lang::txt('COM_TAGS_FILTER'); ?>:</label>
		<select name="tbl" id="filter-tbl" onchange="document.adminForm.submit();">
			<option value=""<?php if (!$this->filters['tbl']) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FILTER_TYPE'); ?></option>
			<?php foreach ($this->types as $type) { ?>
				<option value="<?php echo $type->get('tbl'); ?>"<?php if ($this->filters['tbl'] == $type->get('tbl')) { echo ' selected="selected"'; } ?>><?php echo $type->get('tbl'); ?></option>
			<?php } ?>
		</select>

		<input type="hidden" name="tagid" value="<?php echo $this->filters['tagid']; ?>" />
	</fieldset>

	<table class="adminlist">
		<?php if ($this->filters['tagid']) { ?>
			<caption><?php
			$tag = \Components\Tags\Models\Tag::oneOrFail($this->filters['tagid']);
			echo Lang::txt('COM_TAGS_TAG') . ': ' . $this->escape($tag->get('raw_tag')) . ' (' . $this->escape($tag->get('tag')) . ')';
			?></caption>
		<?php } ?>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_TAGS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php if (!$this->filters['tagid']) { ?>
					<th scope="col"><?php echo Html::grid('sort', 'COM_TAGS_COL_TAGID', 'tagid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php } ?>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TAGS_COL_TBL', 'tbl', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TAGS_COL_OBJECTID', 'objectid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TAGS_COL_CREATED', 'taggedon', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TAGS_COL_CREATED_BY', 'taggerid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo (!$this->filters['tagid'] ? 7 : 6); ?>"><?php
				// Initiate paging
				echo $this->rows->pagination;
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			//$row = \Components\Tags\Models\Object::blank()->set($row);
			//$row->set('id', $row->get('taggedid'));
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('id')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('id')); ?>
						</span>
					<?php } ?>
				</td>
				<?php if (!$this->filters['tagid']) { ?>
					<td>
						<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
								<?php echo $this->escape($row->get('tagid')); ?>
							</a>
						<?php } else { ?>
							<span>
								<?php echo $this->escape($row->get('tagid')); ?>
							</span>
						<?php } ?>
					</td>
				<?php } ?>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('tbl')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('tbl')); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('objectid')); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->get('objectid')); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo $row->get('taggedon'); ?>"><?php echo ($row->get('taggedon') != '0000-00-00 00:00:00' ? $row->get('taggedon') : Lang::txt('COM_TAGS_UNKNOWN')); ?></time>
				</td>
				<td class="priority-4">
					<?php if ($row->get('taggerid')) { ?>
						<a href="<?php echo Route::url('index.php?option=com_members&controller=members&task=edit&id=' . $row->get('taggerid')); ?>">
							<?php echo $row->creator->get('name', Lang::txt('COM_TAGS_UNKNOWN')); ?>
						</a>
					<?php } else { ?>
						<?php echo Lang::txt('COM_TAGS_UNKNOWN'); ?>
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

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>