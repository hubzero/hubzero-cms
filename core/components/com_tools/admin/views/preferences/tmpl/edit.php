<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS') . ': ' . $text, 'user');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();

$this->js();

$user = User::getInstance($this->row->user_id);

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TOOLS_USER_PREFS_LEGEND'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<?php if (!$this->row->id) : ?>
					<div class="input-wrap">
						<label for="field-user_id"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USER'); ?>:</label>
						<?php
						$mc = Event::trigger('hubzero.onGetSingleEntry', array(
							array(
								'members',  // The component to call
								'fields[user_id]',  // Name of the input field
								'field-user_id',  // ID of the input field
								'',  // CSS class(es) for the input field
								''  // The value of the input field
							)
						));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
							<input type="text" name="fields[user_id]" id="field-user_id" value="" />
						<?php } ?>
						<span><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USER_HINT'); ?></span>
					</div>
				<?php else : ?>
					<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->user_id; ?>" />
				<?php endif; ?>
				<div class="input-wrap" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=getClassValues'); ?>">
					<label for="class_id"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_CLASS'); ?>:</label>
					<?php echo $this->classes; ?>
				</div>
				<div class="input-wrap">
					<label for="field-jobs"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_JOBS'); ?>:</label>
					<input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[jobs]" id="field-jobs" value="<?php echo $this->escape(stripslashes($this->row->jobs)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-params"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_PREFERENCES'); ?>:</label>
					<textarea name="fields[params]" id="field-params" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->row->params)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_ID'); ?></th>
						<td><?php echo $this->row->user_id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USERNAME'); ?></th>
						<td><?php echo ($user) ? $user->username : ''; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_NAME'); ?></th>
						<td><?php echo ($user) ? $user->name : ''; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>