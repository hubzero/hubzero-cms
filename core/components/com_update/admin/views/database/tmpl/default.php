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

Toolbar::title(Lang::txt('CMS Updater: database'));
Toolbar::custom('migrate', 'purge', '', 'Run pending migrations', false);

$this->css();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<table id="tktlist" class="adminlist">
		<thead>
			<tr>
				<th scope="col">Component</th>
				<th scope="col">Date</th>
				<th scope="col">Status</th>
				<th scope="col">Description</th>
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
			<?php foreach ($this->rows as $row) : ?>
				<?php $item      = ltrim($row['entry'], 'Migration'); ?>
				<?php $date      = Date::of(strtotime(substr($item, 0, 14).'UTC'))->format('Y-m-d g:i:sa'); ?>
				<?php $component = substr($item, 14, -4); ?>
				<?php if (is_file(PATH_CORE . DS . 'migrations' . DS . $row['entry'])) : ?>
					<?php require_once PATH_CORE . DS . 'migrations' . DS . $row['entry']; ?>
				<?php else : ?>
					<?php require_once PATH_APP . DS . 'migrations' . DS . $row['entry']; ?>
				<?php endif; ?>
				<?php $class     = new ReflectionClass(substr($row['entry'], 0, -4)); ?>
				<?php $desc      = trim(rtrim(ltrim($class->getDocComment(), "/**\n *"), '**/')); ?>
				<tr>
					<td><?php echo $component; ?></td>
					<td><?php echo $date; ?></td>
					<td class="status">
						<?php if ($row['status'] == 'pending') : ?>
							<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=migrate&file='.$row['entry']); ?>">
						<?php endif; ?>
							<span class="state <?php echo ($row['status'] == 'complete') ? 'published' : $row['status']; ?>">
								<span class="text"><?php echo $row['status']; ?></span>
							</span>
						<?php if ($row['status'] == 'pending') : ?>
							</a>
						<?php endif; ?>
					</td>
					<td><?php echo $desc; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>