<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_HOOKS'), 'import');

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
			<a<?php if ($this->controller == 'imports') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'importhooks') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_HOOKS'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<thead>
				<tr>
					<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
					<th scope="col" class="priority-3"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_NAME'); ?></th>
					<th scope="col" class="priority-2"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_TYPE'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_FILE'); ?></th>
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
											echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');
											break;
										case 'postmap':
											echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');
											break;
										case 'postparse':
										default:
											echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');
											break;
									}
								?>
							</td>
							<td>
								<?php echo $hook->get('file'); ?> &mdash;
								<a rel="noopener" target="_blank" href="<?php echo Route::url('index.php?option=com_resources&controller=importhooks&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
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