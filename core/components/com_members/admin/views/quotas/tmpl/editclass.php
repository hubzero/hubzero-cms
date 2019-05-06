<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->task == 'editClass' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTA_CLASSES') . ': ' . $text, 'user');
if ($canDo->get('core.edit')):
	Toolbar::apply('applyClass');
	Toolbar::save('saveClass');
	Toolbar::spacer();
endif;
Toolbar::cancel('cancelClass');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_LEGEND'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_MEMBERS_QUOTA_ALIAS'); ?>:</label>
					<input <?php echo ($this->row->get('alias') == 'default') ? 'readonly' : ''; ?> type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label>
					<input type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('soft_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label>
					<input type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('hard_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label>
					<input type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->get('soft_files'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label>
					<input type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->get('hard_files'))); ?>" />
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_LEGEND'); ?></span></legend>
				<p><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_DESC'); ?></p>
				<?php
				// Include the component HTML helpers.
				Html::addIncludePath(Component::path('com_members') . '/admin/helpers/html');

				$groups = array();
				foreach ($this->row->groups as $g):
					$groups[] = $g->get('group_id');
				endforeach;
				?>
				<div class="input-wrap">
					<?php echo Html::access('usergroups', 'fields[groups]', $groups, true); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_ID'); ?></th>
						<td><?php echo $this->row->get('id'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USER_COUNT'); ?></th>
						<td><?php echo ($this->row->get('id')) ? $this->user_count : 0; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="saveClass" />

	<?php echo Html::input('token'); ?>
</form>