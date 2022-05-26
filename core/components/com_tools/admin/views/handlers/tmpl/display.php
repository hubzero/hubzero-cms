<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HANDLERS'), 'tools');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_HANDLERS_TOOLNAME', 'tool.title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_HANDLERS_PROMPT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_HANDLERS_RULES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->rows) : ?>
				<?php $i = 0; ?>
				<?php foreach ($this->rows as $row) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
							<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('id'); ?></label>
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
								<span><?php echo $this->escape(stripslashes($row->tool->title)); ?></span>
							</a>
						</td>
						<td>
							<?php echo $this->escape(stripslashes($row->prompt)); ?>
						</td>
						<td>
							<?php $rules = []; ?>
							<?php foreach ($row->rules as $rule) : ?>
								<?php $rules[] = $rule->extension . ':' . $rule->quantity; ?>
							<?php endforeach; ?>
							<?php echo implode(', ', $rules); ?>
						</td>
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
