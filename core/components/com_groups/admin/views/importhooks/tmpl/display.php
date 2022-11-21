<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Groups\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORTHOOK_TITLE_HOOKS'), 'import');

if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'imports') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'importhooks') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_HOOKS'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<thead>
				<tr>
					<th scope="col">
						<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
						<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
					</th>
					<th scope="col" class="priority-3"><?php echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_NAME'); ?></th>
					<th scope="col" class="priority-2"><?php echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_TYPE'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_FILE'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php
						// Initiate paging
						echo $this->hooks->pagination;
						?>
					</td>
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
							<td class="priority-3">
								<?php echo $this->escape($hook->get('name')); ?> <br />
								<span class="hint">
									<?php echo nl2br($this->escape($hook->get('notes'))); ?>
								</span>
							</td>
							<td class="priority-2">
								<?php
								switch ($hook->get('event'))
								{
									case 'postconvert':
										echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');
										break;
									case 'postmap':
										echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');
										break;
									case 'postparse':
									default:
										echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');
										break;
								}
								?>
							</td>
							<td>
								<?php echo $hook->get('file'); ?> &mdash;
								<a rel="noopener noreferrer" target="_blank" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php echo Lang::txt('Currently there are no import hooks.'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
