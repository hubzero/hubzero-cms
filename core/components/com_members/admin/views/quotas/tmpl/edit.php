<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS') . ': ' . $text, 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_LEGEND'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<?php if (!$this->row->get('user_id')) : ?>
					<div class="input-wrap">
						<script type="text/javascript" src="<?php echo $base; ?>/plugins/hubzero/autocompleter/assets/js/autocompleter.js"></script>
						<script type="text/javascript">var plgAutocompleterCss = "<?php echo $base; ?>/plugins/hubzero/autocompleter/assets/css/autocompleter.css";</script>

						<label for="field-user_id"><?php echo Lang::txt('COM_MEMBERS_QUOTA_USER'); ?>:</label>
						<input type="text" name="fields[user_id]" id="field-user_id" data-options="members,multi," id="acmembers" class="autocomplete" value="" autocomplete="off" data-css="" data-script="<?php echo $base; ?>/administrator/index.php" />
						<span><?php echo Lang::txt('COM_MEMBERS_QUOTA_USER_HINT'); ?></span>
					</div>
				<?php else : ?>
					<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->get('user_id'); ?>" />
				<?php endif; ?>
				<div class="input-wrap">
					<label for="class_id"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS'); ?>:</label>
					<?php echo $this->classes; ?>
				</div>
				<div class="input-wrap">
					<label for="field-soft_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('soft_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('hard_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->get('soft_files'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->get('hard_files'))); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_ID'); ?></th>
						<td><?php echo $this->row->get('user_id'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_USERNAME'); ?></th>
						<td><?php echo $this->row->get('username'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_NAME'); ?></th>
						<td><?php echo $this->row->get('name'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SPACE'); ?></th>
						<td><?php echo Lang::txt('COM_MEMBERS_QUOTA_SPACE_DISPLAY', (isset($this->du['info']['space']) ? $this->du['info']['space'] / 1024 : 0), $this->du['percent']); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_FILES'); ?></th>
						<td><?php echo (isset($this->du['info']['files'])) ? $this->du['info']['files'] : 0; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>