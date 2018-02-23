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

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_MIGRATIONS'));

Toolbar::custom('runup', 'up', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_UP');
Toolbar::custom('rundown', 'down', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_DOWN');
Toolbar::spacer();
Toolbar::custom('migrate', 'purge', '', 'Run pending migrations', false);

Html::behavior('tooltip');

$this->css();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<?php if (!empty($this->breadcrumb)): ?>
	<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&folder='); ?>" class="breadcrumb">Filter: <?php echo $this->breadcrumb; ?></a>
	<?php endif; ?>
	<table id="tktlist" class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col">Extension</th>
				<th scope="col priority-3">Date</th>
				<th scope="col">Filename</th>
				<th scope="col">Status</th>
				<th scope="col priority-4">Description</th>
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
			<?php foreach ($this->rows as $i => $row) : ?>
				<?php
				$parts = explode('/', $row['entry']);

				$row['file']  = array_pop($parts);
				$row['scope'] = implode('/', $parts);
				$row['core']  = ($parts[0] == 'core');

				$item      = ltrim($row['file'], 'Migration');
				$date      = Date::of(strtotime(substr($item, 0, 14).'UTC'))->format('Y-m-d g:i:sa');
				$component = substr($item, 14, -4);

				if (is_file(PATH_ROOT . DS . $row['entry']))
				{
					if (!class_exists(substr($row['file'], 0, -4)))
					{
						require_once PATH_ROOT . DS . $row['entry'];
					}
					$class = new ReflectionClass(substr($row['file'], 0, -4));
					$desc  = trim(rtrim(ltrim($class->getDocComment(), "/**\n *"), '**/'));
				}
				else
				{
					$desc = '<span class="warning">' . Lang::txt('COM_INSTALLER_MSG_MIGRATIONS_FILE_NOT_FOUND') . '</span>';
				}
				?>
				<tr>
					<td>
						<input type="checkbox" name="migration[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row['entry']); ?>" onclick="isChecked(this.checked);" />
					</td>
					<td>
						<?php echo $component; ?><br />
						<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&folder='.urlencode(str_replace('/migrations', '', $row['scope']))); ?>" class="dir-locale <?php echo ($row['core'] ? 'dir-core' : 'dir-app'); ?>"><?php echo str_replace('/migrations', '', $row['scope']); ?></a>
					</td>
					<td class="priority-3"><?php echo $date; ?></td>
					<td>
						<?php $migrationName = explode('/', $row['entry']);
									$migrationName = end($migrationName);
									echo $migrationName;
						?>
					</td>
					<td class="status">
						<?php if ($row['status'] == 'pending') : ?>
							<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=migrate&file='.$row['file']).'&'.Session::getFormToken().'=1'; ?>">
						<?php endif; ?>
							<span class="state <?php echo ($row['status'] == 'complete') ? 'published' : $row['status']; ?>">
								<span class="text"><?php echo $row['status']; ?></span>
							</span>
						<?php if ($row['status'] == 'pending') : ?>
							</a>
						<?php endif; ?>
					</td>
					<td class="priority-4"><?php echo $desc; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="folder" value="<?php echo urlencode($this->filters['folder']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
