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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Forum\Helpers\Permissions::getActions('category');

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_CATEGORIES'), 'forum.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
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
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('categories');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="scopeinfo"><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE'); ?>:</label>
		<select name="scopeinfo" id="scopeinfo" style="max-width: 20em;" onchange="document.adminForm.submit();">
			<option value=""<?php if ($this->filters['scopeinfo'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT'); ?></option>
			<option value="site:0"<?php if ($this->filters['scopeinfo'] == 'site:0') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_NONE'); ?></option>
			<?php
			$results = $this->forum->scopes();

			$list = array();

			foreach ($results as $result)
			{
				if (!isset($list[$result->scope]))
				{
					$list[$result->scope] = array();
				}
				$list[$result->scope][$result->scope_id] = $result;
			}

			$html = '';
			foreach ($list as $label => $optgroup)
			{
				if ($label == 'site')
				{
					continue;
				}
				$html .= ' <optgroup label="' . $label . '">';
				foreach ($optgroup as $result)
				{
					$html .= ' <option value="' . $result->scope . ':' . $result->scope_id . '"';
					if ($this->filters['scopeinfo'] == $result->scope . ':' . $result->scope_id)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>' . $this->escape(stripslashes($result->caption));
					$html .= '</option>'."\n";
				}
				$html .= '</optgroup>'."\n";
			}
			echo $html;
			?>
		</select>

		<?php if ($this->filters['scopeinfo']) { ?>
			<label for="field-section_id"><?php echo Lang::txt('COM_FORUM_FILTER_SECTION'); ?>:</label>
			<select name="section_id" id="field-section_id" onchange="document.adminForm.submit( );">
				<option value="-1"><?php echo Lang::txt('COM_FORUM_FILTER_SECTION_SELECT'); ?></option>
				<?php
				foreach ($this->sections as $section)
				{
					?>
					<option value="<?php echo $section->id; ?>"<?php if ($this->filters['section_id'] == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
					<?php
				}
				?>
			</select>
		<?php } else { ?>
			<input type="hidden" name="section_id" value="<?php echo $this->filters['section_id']; ?>" />
		<?php } ?>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_FORUM_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FORUM_THREADS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FORUM_POSTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				// initiate paging
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
		if ($this->results)
		{
			$k = 0;
			for ($i=0, $n=count($this->results); $i < $n; $i++)
			{
				$row =& $this->results[$i];
				switch ($row->state)
				{
					case '2':
						$task = 'publish';
						$alt = Lang::txt('JTRASHED');
						$cls = 'trash';
					break;
					case '1':
						$task = 'unpublish';
						$alt = Lang::txt('JPUBLISHED');
						$cls = 'publish';
					break;
					case '0':
					default:
						$task = 'publish';
						$alt = Lang::txt('JUNPUBLISHED');
						$cls = 'unpublish';
					break;
				}

				switch ($row->access)
				{
					case 0:
						$color_access = 'public';
						$task_access  = '1';
						$row->groupname = Lang::txt('COM_FORUM_ACCESS_PUBLIC');
						break;
					case 1:
						$color_access = 'registered';
						$task_access  = '2';
						$row->groupname = Lang::txt('COM_FORUM_ACCESS_REGISTERED');
						break;
					case 2:
						$color_access = 'special';
						$task_access  = '3';
						$row->groupname = Lang::txt('COM_FORUM_ACCESS_SPECIAL');
						break;
					case 3:
						$color_access = 'protected';
						$task_access  = '4';
						$row->groupname = Lang::txt('COM_FORUM_ACCESS_PROTECTED');
						break;
					case 4:
						$color_access = 'private';
						$task_access  = '0';
						$row->groupname = Lang::txt('COM_FORUM_ACCESS_PRIVATE');
						break;
				}
				?>
				<tr class="<?php echo "row$k" . ($row->state ==2 ? ' archived' : ''); ?>">
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
					</td>
					<td class="priority-5">
						<?php echo $row->id; ?>
					</td>
					<td>
						<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a>
						<?php } else { ?>
							<span>
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</span>
						<?php } ?>
					</td>
					<td class="priority-2">
						<?php if ($canDo->get('core.edit.state')) { ?>
							<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&section_id=' . $this->filters['section_id'] . '&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_FORUM_SET_TO', $task); ?>">
								<span><?php echo $alt; ?></span>
							</a>
						<?php } else { ?>
							<span class="state <?php echo $cls; ?>">
								<span><?php echo $alt; ?></span>
							</span>
						<?php } ?>
					</td>
					<td class="priority-4">
						<span class="access <?php echo $color_access; ?>">
							<span><?php echo $row->groupname; ?></span>
						</span>
					</td>
					<td class="priority-3">
						<span class="scope">
							<span><?php echo $row->scope . ' (' . (isset($list[$row->scope][$row->scope_id]) ? $this->escape($list[$row->scope][$row->scope_id]->caption) : $this->escape($row->scope_id)) . ')'; ?></span>
						</span>
					</td>
					<td>
						<?php if ($row->threads > 0) { ?>
							<a class="glyph thread" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=threads&category_id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_FORUM_VIEW_THREADS_FOR'); ?>">
								<span><?php echo $row->threads; ?></span>
							</a>
						<?php } else { ?>
							<span class="glyph thread">
								<span><?php echo $row->threads; ?></span>
							</span>
						<?php } ?>
					</td>
					<td>
						<?php if ($row->posts > 0) { ?>
							<a class="glyph comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=threads&category_id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_FORUM_VIEW_POSTS_FOR'); ?>">
								<span><?php echo $row->posts; ?></span>
							</a>
						<?php } else { ?>
							<span class="glyph comment">
								<span><?php echo $row->posts; ?></span>
							</span>
						<?php } ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>