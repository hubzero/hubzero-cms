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

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS'), 'import.png');

if ($canDo->get('core.admin'))
{
	Toolbar::custom('sample', 'sample', 'sample', 'COM_MEMBERS_IMPORT_SAMPLE', false);
	Toolbar::spacer();
	Toolbar::custom('run', 'script', 'script', 'COM_MEMBERS_RUN');
	Toolbar::custom('runtest', 'runtest', 'script', 'COM_MEMBERS_TEST_RUN');
	Toolbar::spacer();
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

$this->css('import');
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'imports') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'importhooks') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_HOOKS'); ?></a>
		</li>
	</ul>
</nav>

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
				<th scope="col"><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_NAME'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_NUMRECORDS'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_CREATED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_LASTRUN'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					echo $this->imports->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->imports->count() > 0) :
				$i = 0;
				?>
				<?php foreach ($this->imports as $import) : ?>
					<tr>
						<td>
							<?php if ($canDo->get('core.admin')) { ?>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $import->get('id'); ?>" onclick="isChecked(this.checked);" />
							<?php } ?>
						</td>
						<td>
							<?php if ($canDo->get('core.admin')) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $import->get('id')); ?>">
									<?php echo $this->escape($import->get('name')); ?>
								</a>
							<?php } else { ?>
								<?php echo $this->escape($import->get('name')); ?>
							<?php } ?>
							<br />
							<span class="hint">
								<?php echo nl2br($this->escape($import->get('notes'))); ?>
							</span>
						</td>
						<td class="priority-4">
							<?php echo $this->escape($import->get('count', 0)); ?>
						</td>
						<td class="priority-3">
							<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_ON'); ?></strong>
							<time datetime="<?php echo $import->get('created_at'); ?>"><?php echo Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a'); ?></time><br />
							<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_BY'); ?></strong>
							<?php
								if ($created_by = Hubzero\User\Profile::getInstance($import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
						<td>
							<?php
								$lastRun = $import->runs()
									->whereEquals('import_id', $import->get('id'))
									->whereEquals('dry_run', 0)
									->ordered()
									->row();
							?>
							<?php if ($lastRun->get('id')) : ?>
								<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_ON'); ?></strong>
								<time datetime="<?php echo $import->get('ran_at'); ?>"><?php echo Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a'); ?></time><br />
								<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_BY'); ?></strong>
								<?php
									if ($created_by = Hubzero\User\Profile::getInstance($lastRun->get('ran_by')))
									{
										echo $created_by->get('name');
									}
								?>
							<?php else: ?>
								n/a
							<?php endif; ?>
						</td>
						<td class="priority-4">
							<?php
								$runs = $import->runs()
									->whereEquals('import_id', $import->get('id'))
									->whereEquals('dry_run', 0)
									->total();

								echo $runs;
							?>
						</td>
					</tr>
				<?php
					$i++;
				endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6"><?php echo Lang::txt('COM_MEMBERS_IMPORT_NONE'); ?></td>
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