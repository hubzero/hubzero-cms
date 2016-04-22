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

$canDo = Components\Forum\Helpers\Permissions::getActions('section');

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SECTIONS'), 'forum');
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
Toolbar::help('sections');
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
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_FORUM_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_FORUM_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-state').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8 align-right">
				<label for="scopeinfo"><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE'); ?>:</label>
				<select name="scopeinfo" id="scopeinfo" style="max-width: 20em;" onchange="document.adminForm.submit();">
					<option value=""<?php if ($this->filters['scopeinfo'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT'); ?></option>
					<option value="site:0"<?php if ($this->filters['scopeinfo'] == 'site:0') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_NONE'); ?></option>
					<?php
					$list = array();
					foreach ($this->scopes as $result)
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

				<label for="filter-state"><?php echo Lang::txt('COM_BLOG_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" onchange="this.form.submit();">
					<option value="-1"<?php if ($this->filters['state'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" onchange="this.form.submit()">
					<option value="-1"><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_FORUM_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// initiate paging
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
				switch ($row->get('state'))
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

				switch ($row->get('access'))
				{
					case 1:
						$color_access = 'public';
						$task_access  = '1';
						$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PUBLIC'));
						break;
					case 2:
						$color_access = 'registered';
						$task_access  = '2';
						$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_REGISTERED'));
						break;
					case 3:
						$color_access = 'special';
						$task_access  = '3';
						$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_SPECIAL'));
						break;
					case 4:
						$color_access = 'protected';
						$task_access  = '4';
						$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PROTECTED'));
						break;
					case 5:
						$color_access = 'private';
						$task_access  = '0';
						$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PRIVATE'));
						break;
				}

				$cat = $row->categories->count();
				?>
				<tr class="<?php echo "row$k" . ($row->get('state') ==2 ? ' archived' : ''); ?>">
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
					</td>
					<td class="priority-5">
						<?php echo $row->get('id'); ?>
					</td>
					<td>
						<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						<?php } else { ?>
							<span>
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</span>
						<?php } ?>
					</td>
					<td class="priority-2">
						<?php if ($canDo->get('core.edit.state')) { ?>
							<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_FORUM_SET_TO', $task); ?>">
								<span><?php echo $alt; ?></span>
							</a>
						<?php } else { ?>
							<span class="state <?php echo $cls; ?>">
								<span><?php echo $alt; ?></span>
							</span>
						<?php } ?>
					</td>
					<td class="priority-4">
						<span class="access <?php echo $color_access; ?>"><?php echo $this->escape($row->get('access_level')); ?></span>
					</td>
					<td class="priority-3">
						<span class="scope">
							<span><?php echo $this->escape($row->get('scope')) . ' (' . (isset($list[$row->get('scope')][$row->get('scope_id')]) ? $this->escape($list[$row->get('scope')][$row->get('scope_id')]->caption) : $this->escape($row->get('scope_id'))) . ')'; ?></span>
						</span>
					</td>
					<td>
						<?php if ($cat > 0) { ?>
							<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=categories&section_id=' . $row->get('id')); ?>">
								<span><?php echo $cat; ?></span>
							</a>
						<?php } else { ?>
							<span class="glyph category">
								<span><?php echo $cat; ?></span>
							</span>
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

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>