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

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES_IMPORT_TITLE_IMPORTS'), 'import.png');

Toolbar::custom('run', 'script', 'script', 'COM_RESOURCES_RUN');
Toolbar::custom('runtest', 'runtest', 'script', 'COM_RESOURCES_TEST_RUN');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();

Toolbar::spacer();
Toolbar::help('import');

$this->css('import');
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

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->imports->count(); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_NUMRECORDS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_CREATED'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_LASTRUN'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->imports->count() > 0) : ?>
				<?php foreach ($this->imports as $i => $import) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $import->get('id'); ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $this->escape($import->get('name')); ?> <br />
							<span class="hint">
								<?php echo nl2br($this->escape($import->get('notes'))); ?>
							</span>
						</td>
						<td>
							<?php
								if ($import->get('count'))
								{
									echo number_format($this->escape($import->get('count')));
								}
							?>
						</td>
						<td class="priority-4">
							<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
							<?php
								$created_on = Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a');
								echo $created_on . '<br />';
							?>
							<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
							<?php
								if ($created_by = User::getInstance($import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
						<td class="priority-3">
							<?php
								$lastRun = $import->runs('list', array(
									'import' => $import->get('id'),
									'dry_run' => 0,
									''
								))->first();
							?>
							<?php if ($lastRun) : ?>
								<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
								<?php
									$created_on = Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a');
									echo $created_on . '<br />';
								?>
								<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
								<?php
									if ($created_by = User::getInstance($lastRun->get('ran_by')))
									{
										echo $created_by->get('name');
									}
								?>
							<?php else: ?>
								n/a
							<?php endif; ?>
						</td>
						<td class="priority-2">
							<?php
								$runs = $import->runs('list', array(
									'import' => $import->get('id'),
									'dry_run' => 0
								));
								echo $runs->count();
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6"><?php echo Lang::txt('COM_RESOURCES_IMPORT_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>