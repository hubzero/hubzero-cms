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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('CMS Updater: repository'));
//Toolbar::custom('rollback', 'back', '', 'Rollback repository', false);
//Toolbar::spacer();
Toolbar::custom('update', 'purge', '', 'Update repository', false);

$this->css();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('Search'); ?>: </label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" placeholder="search" />

		<label for="status"><?php echo Lang::txt('Status'); ?>:</label>
		<select name="status" id="status">
			<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('[ all ]'); ?></option>
			<option value="upcoming"<?php echo ($this->filters['status'] == 'upcoming') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('Upcoming'); ?></option>
			<option value="installed"<?php echo ($this->filters['status'] == 'installed') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('Installed'); ?></option>
		</select>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('Go'); ?>" />
	</fieldset>

	<div class="clr"></div>

	<table id="repository-list" class="adminlist">
		<thead>
			<tr>
				<th>Author</th>
				<th>Date</th>
				<th>Status</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php
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
			<?php if (count($this->rows) > 0) : ?>
				<?php foreach ($this->rows as $hash => $data) : ?>
					<tr class="<?php echo (substr($hash, 0, 1) == '*') ? 'upcoming' : 'installed'; ?>">
						<td><?php echo $data->name; ?></td>
						<td><?php echo $data->date; ?></td>
						<td><?php echo (substr($hash, 0, 1) == '*') ? 'upcoming' : 'installed'; ?></td>
						<td><?php echo $data->subject; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php elseif (count($this->rows) == 0 && $this->filters['status'] == 'upcoming' && empty($this->filters['search'])) : ?>
				<tr>
					<td colspan="4" class="no-rows">The repository is up-to-date.</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="4" class="no-rows">No matching records.</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>
