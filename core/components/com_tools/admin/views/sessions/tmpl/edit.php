<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS_SESSION_CLASSES') . ': ' . $text, 'tools');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel('cancelclass');
?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_LEGEND'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_ALIAS'); ?>:</label>
					<input <?php echo ($this->row->alias == 'default') ? 'readonly' : ''; ?> type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-jobs"><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_JOBS'); ?>:</label>
					<input type="text" name="fields[jobs]" id="field-jobs" value="<?php echo $this->escape(stripslashes($this->row->jobs)); ?>" />
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_USERGROUPS_LEGEND'); ?></span></legend>
				<p><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_USERGROUPS_DESC'); ?></p>
				<?php
				// Include the component HTML helpers.
				Html::addIncludePath(Component::path('com_members') . '/admin/helpers/html');
				?>
				<div class="input-wrap">
					<?php echo Html::access('usergroups', 'fields[groups]', $this->row->getGroupIds(), true); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_USER_COUNT'); ?></th>
						<td><?php echo ($this->row->id) ? $this->row->userCount() : 0; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="saveClass" />

	<?php echo Html::input('token'); ?>
</form>