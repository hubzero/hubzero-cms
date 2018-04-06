<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

// Create shortcut to parameters.
	$params = $this->state->get('params');

	$params = $params->toArray();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions):
	$params['show_publishing_options'] = '1';
	$params['show_article_options'] = '1';
	$params['show_urls_images_backend'] = '0';
	$params['show_urls_images_frontend'] = '0';
endif;

// Check if the article uses configuration settings besides global. If so, use them.
if (!empty($this->item->attribs['show_publishing_options'])):
		$params['show_publishing_options'] = $this->item->attribs['show_publishing_options'];
endif;
if (!empty($this->item->attribs['show_article_options'])):
		$params['show_article_options'] = $this->item->attribs['show_article_options'];
endif;
if (!empty($this->item->attribs['show_urls_images_backend'])):
		$params['show_urls_images_backend'] = $this->item->attribs['show_urls_images_backend'];
endif;

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid($('#item-form'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_content&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo empty($this->item->id) ? Lang::txt('COM_CONTENT_NEW_ARTICLE') : Lang::txt('COM_CONTENT_EDIT_ARTICLE', $this->item->id); ?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('catid'); ?>
					<?php echo $this->form->getInput('catid'); ?>
				</div>

				<div class="width-50 fltlft">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('state'); ?>
						<?php echo $this->form->getInput('state'); ?>
					</div>
				</div>
				<div class="width-50 fltrt">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('access'); ?>
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
				<div class="clr"></div>

					<?php /*if ($this->canDo->get('core.admin')): ?>
						<div class="input-wrap">
							<span class="faux-label"><?php echo Lang::txt('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
							<div class="button2-left">
								<div class="blank">
									<button type="button" onclick="document.location.href='#access-rules';">
										<?php echo Lang::txt('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
									</button>
								</div>
							</div>
						</div>
					<?php endif;*/ ?>

				<div class="width-50 fltlft">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('featured'); ?>
						<?php echo $this->form->getInput('featured'); ?>
					</div>
				</div>
				<div class="width-50 fltrt">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('language'); ?>
						<?php echo $this->form->getInput('language'); ?>
					</div>
				</div>
				<div class="clr"></div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('articletext'); ?>
					<?php echo $this->form->getInput('articletext'); ?>
					<div class="clr"></div>
				</div>
			</fieldset>
		</div>

		<div class="col span5">
			<?php echo Html::sliders('start', 'content-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php // Do not show the publishing options if the edit form is configured not to. ?>
			<?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
				<?php echo Html::sliders('panel', Lang::txt('COM_CONTENT_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
				<fieldset class="panelform">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('created_by'); ?>
						<?php echo $this->form->getInput('created_by'); ?>
					</div>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('created_by_alias'); ?>
						<?php echo $this->form->getInput('created_by_alias'); ?>
					</div>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('created'); ?>
						<?php echo $this->form->getInput('created'); ?>
					</div>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('publish_up'); ?>
						<?php echo $this->form->getInput('publish_up'); ?>
					</div>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('publish_down'); ?>
						<?php echo $this->form->getInput('publish_down'); ?>
					</div>

					<?php if ($this->item->modified_by) : ?>
						<div class="input-wrap">
							<?php echo $this->form->getLabel('modified_by'); ?>
							<?php echo $this->form->getInput('modified_by'); ?>
						</div>

						<div class="input-wrap">
							<?php echo $this->form->getLabel('modified'); ?>
							<?php echo $this->form->getInput('modified'); ?>
						</div>
					<?php endif; ?>

					<?php if ($this->item->version) : ?>
						<div class="input-wrap">
							<?php echo $this->form->getLabel('version'); ?>
							<?php echo $this->form->getInput('version'); ?>
						</div>
					<?php endif; ?>

					<?php if ($this->item->hits) : ?>
						<div class="input-wrap">
							<?php echo $this->form->getLabel('hits'); ?>
							<?php echo $this->form->getInput('hits'); ?>
						</div>
					<?php endif; ?>
				</fieldset>
			<?php endif; ?>

			<?php $fieldSets = $this->form->getFieldsets('attribs'); ?>
			<?php foreach ($fieldSets as $name => $fieldSet) : ?>
				<?php // If the parameter says to show the article options or if the parameters have never been set, we will
					  // show the article options. ?>

				<?php if ($params['show_article_options'] || (( $params['show_article_options'] == '' && !empty($editoroptions) ))): ?>
					<?php // Go through all the fieldsets except the configuration and basic-limited, which are
						  // handled separately below. ?>

					<?php if ($name != 'editorConfig' && $name != 'basic-limited') : ?>
						<?php echo Html::sliders('panel', Lang::txt($fieldSet->label), $name.'-options'); ?>
						<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
							<p class="tip"><?php echo $this->escape(Lang::txt($fieldSet->description));?></p>
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
					<?php // If we are not showing the options we need to use the hidden fields so the values are not lost.  ?>
				<?php  elseif ($name == 'basic-limited'): ?>
					<?php foreach ($this->form->getFieldset('basic-limited') as $field) : ?>
						<?php  echo $field->input; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php // Not the best place, but here for continuity with 1.5/1/6/1.7 ?>
			<fieldset class="panelform">
				<div class="input-wrap">
					<?php echo $this->form->getLabel('xreference'); ?>
					<?php echo $this->form->getInput('xreference'); ?>
				</div>
			</fieldset>

			<?php // We need to make a separate space for the configuration
			      // so that those fields always show to those with permissions ?>
			<?php if ( $this->canDo->get('core.admin')   ):  ?>
				<?php echo Html::sliders('panel', Lang::txt('COM_CONTENT_SLIDER_EDITOR_CONFIG'), 'configure-sliders'); ?>
				<fieldset class="panelform">
					<?php foreach ($this->form->getFieldset('editorConfig') as $field) : ?>
						<div class="input-wrap">
							<?php echo $field->label; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif ?>

			<?php // The url and images fields only show if the configuration is set to allow them.  ?>
			<?php // This is for legacy reasons. ?>
			<?php if ($params['show_urls_images_backend']): ?>
				<?php echo Html::sliders('panel', Lang::txt('COM_CONTENT_FIELDSET_URLS_AND_IMAGES'), 'urls_and_images-options'); ?>
				<fieldset class="panelform">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('images'); ?>
						<?php echo $this->form->getInput('images'); ?>
					</div>

					<?php foreach ($this->form->getGroup('images') as $field): ?>
						<div class="input-wrap">
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endforeach; ?>

					<?php foreach ($this->form->getGroup('urls') as $field): ?>
						<div class="input-wrap">
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<?php echo Html::sliders('panel', Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
				<fieldset class="panelform">
					<?php echo $this->loadTemplate('metadata'); ?>

			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100">
			<?php //echo Html::sliders('start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php //echo Html::sliders('panel', Lang::txt('COM_CONTENT_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

			<?php //echo Html::sliders('end'); ?>
		</div>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo Request::getCmd('return');?>" />

	<?php echo Html::input('token'); ?>
</form>
