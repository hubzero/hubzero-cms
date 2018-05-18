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

$canDo = Components\Careerplans\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_CAREERPLANS_TITLE'), 'careerplans');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
	$export = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=export';
	foreach ($this->filters as $key => $value)
	{
		$export .= '&' . $key . '=' . $value;
	}
	Toolbar::getRoot()->appendButton('Link', 'download', 'COM_CAREERPLANS_MENU_EXPORT', Route::url($export));
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'delete');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('entries');

Html::behavior('tooltip');

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
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
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CAREERPLANS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_CAREERPLANS_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_CAREERPLANS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-1"><?php echo Html::grid('sort', 'COM_CAREERPLANS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_CAREERPLANS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_CAREERPLANS_COL_MODIFIED', 'modified', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_CAREERPLANS_COL_COMMENTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
				// Initiate paging
				echo $this->rows->pagination
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;

		foreach ($this->rows as $row)
		{
			$created  = Date::of($row->get('created'));
			$modified = Date::of($row->get('modified'));

			$alt  = Lang::txt('JUNPUBLISHED');
			$cls  = 'unpublish';
			$task = 'publish';

			if ($row->isDeleted())
			{
				$alt  = Lang::txt('JTRASHED');
				$task = 'draft';
				$cls  = 'trash';
			}

			$link = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=summary&id=' . $row->get('id'));
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id') ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td class="priority-1">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo $link; ?>">
							<?php echo $this->escape($row->user->get('name', Lang::txt('(unknown)'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($row->user->get('name', Lang::txt('(unknown)'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo $link; ?>">
					<?php } ?>
					<time datetime="<?php echo $row->get('created'); ?>">
						<?php echo $created; ?>
					</time>
					<?php if ($canDo->get('core.edit')) { ?>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo $link; ?>">
					<?php } ?>
					<time datetime="<?php echo $row->get('modified'); ?>">
						<?php echo $modified; ?>
					</time>
					<?php if ($canDo->get('core.edit')) { ?>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->comments()->total(); ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
