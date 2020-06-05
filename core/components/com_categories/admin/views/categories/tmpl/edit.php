<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', 1);

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

Toolbar::title($this->title, 'content');
if ($this->canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::save2copy();
	Toolbar::save2new();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=com_categories&extension='.Request::getCmd('extension', 'com_content').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_CATEGORIES_FIELDSET_DETAILS');?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('parent_id'); ?>
					<?php echo $this->form->getInput('parent_id'); ?>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('published'); ?>
							<?php echo $this->form->getInput('published'); ?>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</fieldset>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_EXTENSION'); ?></th>
						<td>
							<?php echo $this->item->get('extension'); ?>
							<?php echo $this->form->getInput('extension'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_ID'); ?></th>
						<td>
							<?php echo $this->item->get('id', 0); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->item->get('id', 0); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_CREATOR'); ?></th>
						<td>
							<?php echo User::getInstance($this->item->get('created_user_id'))->get('name'); ?>
							<input type="hidden" name="fields[created_user_id]" value="<?php echo $this->item->created_user_id; ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_CREATED'); ?></th>
						<td>
							<?php echo Date::of($this->item->get('created_time'))->toLocal(); ?>
						</td>
					</tr>
					<?php if ($this->item->get('modified_time', false)): ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_MODIFIER'); ?></th>
							<td>
								<?php echo User::getInstance($this->item->get('modified_user_id'))->get('name'); ?>
								<input type="hidden" name="fields[modified_user_id]" value="<?php echo $this->item->modified_user_id; ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_CATEGORIES_FIELD_MODIFIED');?></th>
							<td>
								<?php echo Date::of($this->item->get('modified_time'))->toLocal(); ?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<?php echo Html::sliders('start', 'categories-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
				<?php echo $this->loadTemplate('options'); ?>

				<?php echo Html::sliders('panel', Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
				<fieldset class="panelform">
					<?php echo $this->loadTemplate('metadata'); ?>
				</fieldset>

				<?php $fieldSets = $this->form->getFieldsets('attribs'); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_' . $name . '_FIELDSET_LABEL'; ?>
					<?php if ($name != 'editorConfig' && $name != 'basic-limited') : ?>
						<?php echo Html::sliders('panel', Lang::txt($label), $name . '-options'); ?>
						<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
							<p class="tip"><?php echo $this->escape(Lang::txt($fieldSet->description)); ?></p>
						<?php endif; ?>
						<fieldset class="panelform">
							<?php foreach ($this->form->getFieldset($name) as $field) : ?>
								<div class="input-wrap">
									<?php echo $field->label; ?>
									<?php echo $field->input; ?>
								</div>
							<?php endforeach; ?>
						</fieldset>
					<?php endif ?>
				<?php endforeach; ?>
			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100">
			<fieldset class="panelform">
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
