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

// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Wiki\Helpers\Permissions::getActions('page');

Toolbar::title(Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE') . ': ' . Lang::txt('COM_WIKI_REVISIONS'), 'wiki.png');
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
Toolbar::help('revisions');

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
	<table class="adminlist">
		<tbody>
			<tr>
				<th><?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?></th>
				<td><?php echo $this->escape(stripslashes($this->page->get('title'))); ?></td>
				<th class="priority-2"><?php echo Lang::txt('COM_WIKI_COL_SCOPE'); ?></th>
				<td class="priority-2"><?php echo $this->escape(stripslashes($this->page->get('scope'))); ?></td>
			</tr>
			<tr>
				<th>(<?php echo Lang::txt('COM_WIKI_COL_ID'); ?>) <?php echo Lang::txt('COM_WIKI_COL_PAGENAME'); ?></th>
				<td>(<?php echo $this->page->get('id'); ?>) <?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?></td>
				<th class="priority-2"><?php echo Lang::txt('COM_WIKI_COL_GROUP'); ?></th>
				<td class="priority-2"><?php echo $this->escape(stripslashes($this->page->get('group_cn'))); ?></td>
			</tr>
		</tbody>
	</table>

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_WIKI_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_WIKI_COL_REVISION', 'revision', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_WIKI_COL_EDIT_SUMMARY', 'summary', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_WIKI_COL_APPROVED', 'approved', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_WIKI_COL_MINOR_EDIT', 'minor_edit', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_WIKI_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_WIKI_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
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
$i = 0;
foreach ($this->rows as $row)
{
	switch ($row->get('approved'))
	{
		case '2':
			$color_access = 'trashed';
			$class = 'trashed';
			$task = '0';
			$alt = Lang::txt('COM_WIKI_STATE_TRASHED');
		break;

		case '1':
			$color_access = 'public';
			$class = 'approved';
			$task = '0';
			$alt = Lang::txt('COM_WIKI_STATE_APPROVED');
			break;
		case '0':
			$color_access = 'private';
			$class = 'unapprove';
			$task = '1';
			$alt = Lang::txt('COM_WIKI_STATE_NOT_APPROVED');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&pageid=' . $this->filters['pageid'] . '&' . JUtility::getToken() . '=1'); ?>">
							<?php echo Lang::txt('COM_WIKI_REVISION_NUM', $this->escape(stripslashes($row->get('version')))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo Lang::txt('COM_WIKI_REVISION_NUM', $this->escape(stripslashes($row->get('version')))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape(stripslashes($row->get('summary'))); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="access <?php echo $class . ' ' . $color_access; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=approve&id=' . $row->get('id') . '&pageid=' . $this->filters['pageid'] . '&approve=' . $task . '&' . JUtility::getToken() . '=1'); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="access <?php echo $class . ' ' . $color_access; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="state <?php echo ($row->get('minor_edit') ? 'yes' : 'no'); ?>">
						<span><?php echo $this->escape($row->get('minor_edit')); ?></span>
					</span>
				</td>
				<td class="priority-2">
					<time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php echo $this->escape($row->get('created')); ?></time>
				</td>
				<td class="priority-3">
					<span class="glyph user">
						<?php echo $this->escape(stripslashes($row->get('created_by_name'))); ?>
					</span>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />

	<?php echo Html::input('token'); ?>
</form>