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

$canDo = Components\Kb\Admin\Helpers\Permissions::getActions('article');

$ttle = Lang::txt('COM_KB_ARTICLES');

Toolbar::title(Lang::txt('COM_KB') . ': ' . $ttle, 'kb');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
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
Toolbar::help('articles');

$access = Html::access('assetgroups');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_KB_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-state').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span7">
				<label for="filter-category"><?php echo Lang::txt('COM_KB_CATEGORY'); ?>:</label>
				<?php echo Components\Kb\Admin\Helpers\Html::categories($this->categories, $this->filters['category'], 'category', 'filter-category', 'onchange="this.form.submit()"'); ?>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', $access, 'value', 'text', $this->filters['access']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KB_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_KB_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_KB_ACCESS', 'a.access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_KB_CATEGORY', 'section', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_KB_VOTES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			switch ((int) $row->get('state', 0))
			{
				case 1:
					$class = 'publish';
					$task = 'unpublish';
					$alt = Lang::txt('JPUBLISHED');
				break;
				case 2:
					$class = 'expire';
					$task = 'publish';
					$alt = Lang::txt('JTRASHED');
				break;
				case 0:
				default:
					$class = 'unpublish';
					$task = 'publish';
					$alt = Lang::txt('JUNPUBLISHED');
				break;
			}

			foreach ($access as $ac)
			{
				if ($row->get('access') == $ac->value)
				{
					$row->set('access_level', $ac->text);
					break;
				}
			}

			foreach ($this->categories as $category)
			{
				if ($row->get('category') == $category->get('id'))
				{
					$row->set('ctitle', $category->get('title'));
					break;
				}
			}

			//$tags = $row->tags('cloud');
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php if ($row->get('checked_out') && $row->get('checked_out') != User::get('id')) { ?>
							<span class="checkedout" title="<?php echo Lang::txt('JLIB_HTML_CHECKED_OUT'); ?> :: <?php echo $this->escape($row->get('editor')); ?>">
								<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
							</span>
					<?php } else { ?>
						<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_KB_EDIT_ARTICLE'); ?>">
								<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
							</a>
						<?php } else { ?>
							<span>
								<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
							</span>
						<?php } ?>
					<?php } ?>
					<?php /*if ($tags) { ?>
						<br /><span><?php echo Lang::txt('COM_KB_TAGS'); ?>: <?php echo $tags; ?></span>
					<?php }*/ ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&category=' . $this->filters['category']); ?>" title="<?php echo Lang::txt('COM_KB_SET_TASK', $task);?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="access">
						<?php echo $row->get('access_level'); ?>
					</span>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->get('ctitle')); ?>
				</td>
				<td class="priority-5">
					<span style="color: green;">+<?php echo $row->get('helpful', 0); ?></span>
					<span style="color: red;">-<?php echo $row->get('nothelpful', 0); ?></span>
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
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
