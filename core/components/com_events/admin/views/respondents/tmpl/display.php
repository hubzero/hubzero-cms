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

Toolbar::title(Lang::txt('COM_EVENTS').': '.Lang::txt('COM_EVENTS_RESPONDANTS'), 'user.png');
Toolbar::custom('download', 'upload', 'COM_EVENTS_DOWNLOAD_CSV', 'COM_EVENTS_DOWNLOAD_CSV', false, false);
Toolbar::deleteList('', 'remove', 'COM_EVENTS_DELETE');
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<h2><?php echo stripslashes($this->event->title); ?></h2>

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('COM_EVENTS_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_EVENTS_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
 			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_EVENTS_RESPONDANT_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_EVENTS_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_EVENTS_RESPONDANT_REGISTERED', 'registered', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_EVENTS_SPECIAL_NEEDS', 'special', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_COMMENT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$i = 0;
			foreach ($this->rows as $row)
			{
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<input type="checkbox" name="rid[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" class="checkbox-toggle" />
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=respondent&id=' . $row->id . '&event_id=' . $this->event->id); ?>">
							<?php echo $this->escape(stripslashes($row->last_name . ', ' . $row->first_name)); ?>
						</a>
					</td>
					<td>
						<a href="mailto:<?php echo $row->email ?>">
							<?php echo $this->escape($row->email); ?>
						</a>
					</td>
					<td>
						<?php echo Date::of($row->registered)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
					</td>
					<td>
						<?php
						if (!empty($row->dietary_needs))
						{
							echo Lang::txt('COM_EVENTS_RESPONDANT_DIETARY_NEEDS', $this->escape($row->dietary_needs)) . '<br />';
						}
						if ($row->disability_needs)
						{
							echo Lang::txt('COM_EVENTS_RESPONDANT_DISABILITY_REQUESTED');
						}
						?>
					</td>
					<td>
						<?php echo $this->escape($row->comment); ?>
					</td>
				</tr>
				<?php
				$i++;
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="event" value="<?php $id = Request::getArray('id', array()); echo is_array($id) ? implode(',', $id) : $id; ?>" />
	<input type="hidden" name="id[]" value="<?php $id = Request::getArray('id', array()); echo is_array($id) ? implode(',', $id) : $id; ?>" />

	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
