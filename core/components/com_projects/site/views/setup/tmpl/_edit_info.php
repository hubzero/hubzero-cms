<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<fieldset>
	<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO')); ?></legend>

	<div class="form-group">
		<label for="field-name">
			<?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?>
			<input type="text" name="name" id="field-name" disabled="disabled" readonly="readonly" class="form-control disabled readonly" value="<?php echo $this->escape($this->model->get('alias')); ?>" />
		</label>
	</div>

	<div class="form-group">
		<label for="field-title">
			<?php echo Lang::txt('COM_PROJECTS_TITLE'); ?>
			<input name="title" id="field-title" maxlength="250" type="text" class="form-control" value="<?php echo $this->escape($this->model->get('title')); ?>" class="long" />
		</label>
	</div>

	<div class="form-group">
		<label for="field-about">
			<?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?>
			<?php echo $this->editor('about', $this->escape($this->model->about('raw')), 35, 25, 'about', array('class' => 'form-control minimal no-footer')); ?>
		</label>
	</div>

	<?php
	// Display project image upload
	$this->view('_picture')
		->set('model', $this->model)
		->set('option', $this->option)
		->display();
	?>
</fieldset><!-- / .basic info -->

<?php if (isset($this->fields) && !empty($this->fields)): ?>
	<fieldset>
		<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO_EXTENDED')); ?></legend>

		<?php
		// Convert to XML so we can use the Form processor
		$xml = Components\Projects\Models\Orm\Description\Field::toXml($this->fields, 'edit');
		// Create a new form
		Hubzero\Form\Form::addFieldPath(Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description' . DS. 'fields');

		$form = new Hubzero\Form\Form('description', array('control' => 'description'));
		$form->load($xml);

		$data = new stdClass;
		$data->textbox = 'abd';
		$data->projecttags = 'testing, tagging';

		$form->bind($this->data);

		foreach ($form->getFieldsets() as $fieldset):
			foreach ($form->getFieldset($fieldset->name) as $field):
				echo $field->label;
				echo $field->input;
				echo $field->description;
			endforeach;
		endforeach;
		?>
	</fieldset>
<?php endif; ?>

<fieldset>
	<legend><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></legend>

	<input type="hidden" name="private" value="<?php echo $this->escape($this->model->get('private')); ?>" />

	<div class="form-group">
		<label for="field-access">
			<?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT'); ?>
			<select class="form-control" name="access" id="field-access">
				<?php if (($this->model->get('access') && !in_array($this->model->get('access'), [1, 2, 5])) || User::authorise('core.manage', 'com_projects')): ?>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->model->get('access')); ?>
				<?php else: ?>
					<option value="1"<?php if ($this->model->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC'); ?></option>
					<option value="2"<?php if ($this->model->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_REGISTERED'); ?></option>
					<option value="5"<?php if ($this->model->get('access') == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE'); ?></option>
				<?php endif; ?>
			</select>
		</label>
	</div>

	<fieldset id="access-public"<?php if ($this->model->get('access') == 5) { echo ' class="hidden"'; } ?>>
		<legend><?php echo Lang::txt('COM_PROJECTS_OPTIONS_FOR_PUBLIC'); ?></legend>

		<div class="form-group form-check">
			<label for="params-allow_membershiprequest" class="form-check-label">
				<input type="hidden" name="params[allow_membershiprequest]" value="0" />
				<input type="checkbox" class="option form-check-input" name="params[allow_membershiprequest]" id="params-allow_membershiprequest" value="1" <?php if ($this->model->params->get('allow_membershiprequest')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_MEMBERSHIPREQUEST'); ?>
			</label>
		</div>

		<div class="form-group form-check">
			<label for="params-team_public" class="form-check-label">
				<input type="hidden" name="params[team_public]" value="0" />
				<input type="checkbox" class="option form-check-input" name="params[team_public]" id="params-team_public" value="1" <?php if ($this->model->params->get( 'team_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_TEAM_PUBLIC'); ?>
			</label>
		</div>

		<?php if ($this->publishing): ?>
			<div class="form-group form-check">
				<label for="params-publications_public" class="form-check-label">
					<input type="hidden" name="params[publications_public]" value="0" />
					<input type="checkbox" class="option form-check-input" name="params[publications_public]" id="params-publications_public" value="1" <?php if ($this->model->params->get( 'publications_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC'); ?>
				</label>
			</div>
		<?php endif; ?>

		<?php
		$pparams = Plugin::params('projects', 'notes');
		if ($pparams->get('enable_publinks')): ?>
			<div class="form-group form-check">
				<label for="params-notes_public" class="form-check-label">
					<input type="hidden" name="params[notes_public]" value="0" />
					<input type="checkbox" class="option form-check-input" name="params[notes_public]" id="params-notes_public" value="1" <?php if ($this->model->params->get( 'notes_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_NOTES_PUBLIC'); ?>
				</label>
			</div>
		<?php endif; ?>

		<?php
		$pparams = Plugin::params('projects', 'files');
		if ($pparams->get('enable_publinks')): ?>
			<div class="form-group form-check">
				<label for="params-files_public" class="form-check-label">
					<input type="hidden" name="params[files_public]" value="0" />
					<input type="checkbox" class="option form-check-input" name="params[files_public]" id="params-files_public" value="1" <?php if ($this->model->params->get( 'files_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_FILES_PUBLIC'); ?>
				</label>
			</div>
		<?php endif; ?>
	</fieldset>
</fieldset>
<?php
if ($this->config->get('grantinfo', 0)):
	$this->view('_edit_grant_info')
		->set('model', $this->model)
		->display();
endif;
?>

<p class="submitarea">
	<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>" />

	<span>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=info'); ?>" class="btn btn-secondary btn-cancel">
			<?php echo Lang::txt('JCANCEL'); ?>
		</a>
	</span>
</p>
