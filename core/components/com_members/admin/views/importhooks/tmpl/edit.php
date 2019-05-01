<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// set title
$title  = ($this->hook->get('id')) ? Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_EDIT') : Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_ADD');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt($title), 'import');
Toolbar::save();
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELDSET_DETAILS'); ?></legend>

				<div class="input-wrap">
					<label for="field-event">
						<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE'); ?>
					</label>
					<select name="hook[event]" id="field-event">
						<option value="postparse">
							<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTPARSE'); ?>
						</option>
						<option <?php if ($this->hook->get('event') == 'postmap') { echo 'selected="selected"'; } ?> value="postmap">
							<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTMAP'); ?>
						</option>
						<option <?php if ($this->hook->get('event') == 'postconvert') { echo 'selected="selected"'; } ?> value="postconvert">
							<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_TYPE_POSTCONVERT'); ?>
						</option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-name">
						<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_NAME'); ?>
					</label>
					<input type="text" name="hook[name]" id="field-name" value="<?php echo $this->escape($this->hook->get('name')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-notes">
						<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_NOTES'); ?>
					</label>
					<textarea name="hook[notes]" id="field-notes" rows="5"><?php echo $this->escape($this->hook->get('notes')); ?></textarea>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELDSET_FILE'); ?></legend>

				<div class="input-wrap">
					<label for="field-name">
						<?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT'); ?>
					</label>
					<?php
						if ($this->hook->get('file'))
						{
							echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT_CURRENT', $this->hook->get('file'));
							echo ' &mdash; <a rel="noopener" target="_blank" href="' . Route::url('index.php?option=com_resources&controller=importhooks&task=raw&id='.$this->hook->get('id')) . '">'.Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_SCRIPT_VIEWRAW').'</a><br />';
						}
					?>
					<input type="file" name="file" />
				</div>
			</fieldset>
		</div>
		<div class="col span4">
			<?php if ($this->hook->get('id')) : ?>
				<table class="meta">
					<tbody>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_ID'); ?></th>
							<td><?php echo $this->hook->get('id'); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_CREATEDBY'); ?></th>
							<td>
								<?php
									if ($created_by = User::getInstance($this->hook->get('created_by')))
									{
										echo $created_by->get('name');
									}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORTHOOK_EDIT_FIELD_CREATEDON'); ?></th>
							<td>
								<time datetime="<?php echo $this->hook->get('created_at'); ?>"><?php
									echo Date::of($this->hook->get('created_at'))->toLocal('m/d/Y @ g:i a');
								?></time>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="hook[id]" value="<?php echo $this->hook->get('id'); ?>" />
	<input type="hidden" name="hook[type]" value="<?php echo $this->hook->get('type'); ?>" />

	<?php echo Html::input('token'); ?>
</form>