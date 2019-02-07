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

Toolbar::title(Lang::txt('COM_TOOLS' ), 'tools');
Toolbar::preferences('com_tools', '550');
Toolbar::spacer();
Toolbar::help('tools');

$this->css();

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?></label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('COM_BLOG_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php if ($this->filters['state'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_REGISTERED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_CREATED'); ?></option>
					<option value="3"<?php if ($this->filters['state'] === 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_UPLOADED'); ?></option>
					<option value="4"<?php if ($this->filters['state'] === 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_INSTALLED'); ?></option>
					<option value="5"<?php if ($this->filters['state'] === 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_UPDATED'); ?></option>
					<option value="6"<?php if ($this->filters['state'] === 6) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_APPROVED'); ?></option>
					<option value="7"<?php if ($this->filters['state'] === 7) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="8"<?php if ($this->filters['state'] === 8) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_RETIRED'); ?></option>
					<option value="9"<?php if ($this->filters['state'] === 9) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_ABANDONED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_TOOLS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_NAME', 'toolname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_REGISTERED', 'registered', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATECHANGED', 'state_changed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_VERSIONS', 'versions', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
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
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row = &$this->rows[$i];

			switch ($row['state'])
			{
				case 0:
					$state = 'unpublished';
					break;
				case 1:
					$state = 'registered';
					break;
				case 2:
					$state = 'created';
					break;
				case 3:
					$state = 'uploaded';
					break;
				case 4:
					$state = 'installed';
					break;
				case 5:
					$state = 'updated';
					break;
				case 6:
					$state = 'approved';
					break;
				case 7:
					$state = 'published';
					break;
				case 8:
					$state = 'retired';
					break;
				case 9:
					$state = 'abandoned';
					break;
				default:
					$state = 'unknown';
					break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="radio" name="id" id="cb<?php echo $i; ?>" value="<?php echo $row['id'] ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row['id']); ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row['id']); ?>">
						<?php echo $this->escape(stripslashes($row['toolname'])); ?>
					</a>
				</td>
				<td class="priority-4">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row['id']); ?>">
						<?php echo $this->escape(stripslashes($row['title'])); ?>
					</a>
				</td>
				<td>
					<span class="state <?php echo $state; ?> hasTip" title="<?php echo $this->escape(Lang::txt(strtoupper($this->option) . '_' . strtoupper($state))); ?>">
						<span><?php echo $this->escape(Lang::txt(strtoupper($this->option) . '_' . strtoupper($state))); ?></span>
					</span>
				</td>
				<td class="priority-3">
					<time><?php echo $this->escape($row['registered']); ?></time>
				</td>
				<td class="priority-3">
					<time><?php echo $this->escape($row['state_changed']); ?></time>
				</td>
				<td>
					<a class="glyph menulist" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=versions&id=' . $row['id']); ?>">
						<span><?php echo $this->escape($row['versions']); ?></span>
					</a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="view" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>