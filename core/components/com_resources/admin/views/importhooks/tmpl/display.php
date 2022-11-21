<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_HOOKS'), 'import');

Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
					<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
					<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
					<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_FILE', 'file', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4"><?php
					// Initiate paging
					echo $this->hooks->pagination;
					?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php if ($this->hooks->count() > 0) : ?>
					<?php foreach ($this->hooks as $i => $hook) : ?>
						<tr>
							<td>
								<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $hook->get('id'); ?>" class="checkbox-toggle" />
								<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $hook->get('id'); ?></label>
							</td>
							<td>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $hook->get('id')); ?>">
									<?php echo $this->escape($hook->get('name')); ?>
								</a><br />
								<span class="hint">
									<?php echo nl2br($this->escape($hook->get('notes'))); ?>
								</span>
							</td>
							<td>
								<?php
									switch ($hook->get('type'))
									{
										case 'postconvert':
											echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');
											break;
										case 'postmap':
											echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');
											break;
										case 'postparse':
										default:
											echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');
											break;
									}
								?>
							</td>
							<td class="priority-2">
								<?php echo $hook->get('file'); ?> &mdash;
								<a rel="noopener" target="_blank" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_NONE_FOUND'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>