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

$canDo = \Components\Citations\Helpers\Permissions::getActions('citation');

Toolbar::title(Lang::txt('CITATIONS'), 'citation');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_citations', 600, 800);
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
Toolbar::help('citations');

//set the escape callback
$this->setEscape("htmlentities");
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = $('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CITATIONS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<?php /*<label for="sort"><?php echo Lang::txt('SORT'); ?>: </label>
				<select name="sort" id="sort" onchange="document.adminForm.submit();">
					<option value="created DESC"<?php if ($this->filters['sort'] == 'created DESC') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('DATE'); ?></option>
					<option value="year"<?php if ($this->filters['sort'] == 'year') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('YEAR'); ?></option>
					<option value="type"<?php if ($this->filters['sort'] == 'type') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('TYPE'); ?></option>
					<option value="author ASC"<?php if ($this->filters['sort'] == 'author ASC') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('AUTHORS'); ?></option>
					<option value="title ASC"<?php if ($this->filters['sort'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('TITLE'); ?></option>
					<option value="scope_id ASC"<?php if ($this->filters['sort'] == 'scope_id ASC') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('SCOPE_ID'); ?></option>
				</select>*/ ?>

				<label for="scope"><?php echo Lang::txt('SCOPE'); ?>: </label>
				<select name="scope" id="scope" onchange="document.adminForm.submit();">
					<option value="all"<?php if ($this->filters['scope'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('- Scope -'); ?></option>
					<option value="hub"<?php if ($this->filters['scope'] == 'hub') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('HUB'); ?></option>
					<option value="group"<?php if ($this->filters['scope'] == 'group') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('GROUP'); ?></option>
					<option value="member"<?php if ($this->filters['scope'] == 'member') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('MEMBER'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('TITLE'); ?> / <?php echo Lang::txt('AUTHORS'); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'PUBLISHED', 'published', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'YEAR', 'year', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'AFFILIATED', 'affiliated', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'FUNDED_BY', 'fundedby', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'SCOPE_ID', 'scope_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			if ($row->published == 1) :
				$cls = 'publish';
				$alt = Lang::txt('UNPUBLISH');
			elseif ($row->published == 0) :
				$cls = 'unpublish';
				$alt = Lang::txt('PUBLISH');
			elseif ($row->published == 2) :
				$cls = 'delete';
				$alt = Lang::txt('DELETED');
			endif;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td class="priority-2">
					<?php
						$type = $row->relatedType->get('type_title');
						echo ($type) ? $type : Lang::txt('GENERIC');
					?>
				</td>
				<td>
					<?php
						$title = html_entity_decode($row->title);
						$author = html_entity_decode($row->author);
						if (!preg_match('!\S!u', $title))
						{
							$title = utf8_encode($title);
						}

						if (!preg_match('!\S!u', $author))
						{
							$author = utf8_encode($author);
						}
					?>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<?php echo $this->escape($title); ?>
						</a>
						<br />
						<small><?php echo $this->escape($author); ?></small>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($title); ?></a><br />
							<small><?php echo $this->escape($author); ?></small>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<?php if ($row->published == 1) : ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unpublish&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state <?php echo $cls; ?>"><span><?php echo $alt; ?></span></span></a>
						<?php elseif ($row->published == 0) : ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state <?php echo $cls; ?>"><span><?php echo $alt; ?></span></span></a>
						<?php elseif ($row->published == 2) : ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state <?php echo $cls; ?>"><span><?php echo $alt; ?></span></span></a>
						<?php endif; ?>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->year); ?>
				</td>
				<td class="priority-4">
					<?php if ($row->affiliated == 1) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=affiliate&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state publish"><span><?php echo Lang::txt('NO'); ?></span></span></a>
					<?php else : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=affiliate&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state unpublish"><span><?php echo Lang::txt('YES'); ?></span></span></a>
					<?php endif; ?>
				</td>
				<td class="priority-4">
					<?php if ($row->fundedby == 1) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=fund&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state publish"><span><?php echo Lang::txt('NO'); ?></span></span></a>
					<?php else : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=fund&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>"><span class="state unpublish"><span><?php echo Lang::txt('YES'); ?></span></span></a>
					<?php endif; ?>
				</td>
				<td class="priority-4">
					<?php echo ($row->scope == '' ? Lang::txt('Hub') : $this->escape($row->scope)); ?>
				</td>
				<td class="priority-4">
					<?php echo ($row->scope_id == 0 ? Lang::txt('N/A') : $this->escape($row->scope_id)); ?>
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
