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

$canDo = Components\Members\Helpers\Admin::getActions('component');

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS_QUOTA_CLASSES'), 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::addNew('addClass');
	Toolbar::editList('editClass');
	Toolbar::deleteList('COM_MEMBERS_QUOTA_CONFIRM_DELETE', 'deleteClass');
	Toolbar::spacer();
}
Toolbar::help('quotaclasses');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th class="priority-5"><?php echo Lang::txt('COM_MEMBERS_QUOTA_ID'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_ALIAS'); ?></th>
				<th class="priority-3"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?></th>
				<th class="priority-3"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES'); ?></th>
				<th class="priority-2"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
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
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-5">
					<?php if ($canDo->get('core.edit')): ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editClass&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('id')); ?>
						</a>
					<?php else: ?>
						<?php echo $this->escape($row->get('id')); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')): ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editClass&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('alias')); ?>
						</a>
					<?php else: ?>
						<?php echo $this->escape($row->get('alias')); ?>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->get('soft_blocks')); ?>
				</td>
				<td>
					<?php echo $this->escape($row->get('hard_blocks')); ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->get('soft_files')); ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape($row->get('hard_files')); ?>
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
	<input type="hidden" name="task" value="displayClasses" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>