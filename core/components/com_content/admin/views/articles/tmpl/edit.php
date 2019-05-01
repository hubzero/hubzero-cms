<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Content\Admin\Helpers\Permissions::getActions('article', $this->item->get('id'));

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');
$lang = ($this->task == 'edit' || $this->task == 'apply' ? 'COM_CONTENT_PAGE_EDIT_ARTICLE' : 'COM_CONTENT_PAGE_ADD_ARTICLE');
Toolbar::title(Lang::txt($lang), 'content');
if ($canDo->get('core.edit')
	|| ($canDo->get('core.edit.own') && User::getInstance()->get('id') == $this->item->get('created_by'))
	|| ($canDo->get('core.create')))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::save2copy();
	Toolbar::save2new();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('article');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$params = $this->item->attribs->toArray();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions):
	$params['show_publishing_options'] = '1';
	$params['show_article_options'] = '1';
	$params['show_urls_images_backend'] = '0';
	$params['show_urls_images_frontend'] = '0';
endif;
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo empty($this->item->id) ? Lang::txt('COM_CONTENT_NEW_ARTICLE') : Lang::txt('COM_CONTENT_EDIT_ARTICLE', $this->item->id); ?></span></legend>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>

				<div class="form-group input-wrap">
					<label for="categories-field"><?php echo Lang::txt('COM_CONTENT_CHOOSE_CATEGORY_LABEL'); ?></label>
					<select id="categories-field" name="fields[catid]">
						<?php foreach ($this->item->categories as $category): ?>
							<?php
								if (!User::authorise('core.create', $category->asset_id))
								{
									continue;
								}
								$selected = '';
								if (!$this->item->catid)
								{
									if (strtolower($category->alias) == 'uncategorised')
									{
										$selected = 'selected="selected"';
									}
								}
								elseif ($category->id == $this->item->catid)
								{
									$selected = 'selected="selected"';
								}
							?>
							<option value="<?php echo $category->id; ?>" <?php echo $selected;?>><?php echo $category->nestedTitle(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="form-group input-wrap">
							<?php echo $this->form->getLabel('state'); ?>
							<?php if ($canDo->get('core.edit.state')): ?>
								<?php echo $this->form->getInput('state'); ?>
							<?php else: ?>
							<select name="fields[state]" id="categories-field" disabled="disabled">
								<option value="<?php echo $this->item->get('state'); ?>">
									<?php echo $this->item->state; ?>
								</option>
							</select>
							<?php endif; ?>
						</div>
					</div>
					<div class="col span6">
						<div class="form-group input-wrap">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="form-group input-wrap">
							<?php echo $this->form->getLabel('featured'); ?>
							<?php echo $this->form->getInput('featured'); ?>
						</div>
					</div>
					<div class="col span6">
						<div class="form-group input-wrap">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>
				</div>

				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('introtext'); ?>
					<?php echo $this->form->getInput('introtext'); ?>
				</div>
			</fieldset>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CONTENT_FIELD_ID_LABEL');?></th>
						<td><?php echo $this->item->get('id', 0);?>
							<input type="hidden" name="id" value="<?php echo $this->escape($this->item->get('id')); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CONTENT_FIELD_CREATED_BY_LABEL'); ?></th>
						<td>
							<?php echo $this->item->created_by ? User::getInstance($this->item->created_by)->get('name') : Lang::txt('JUNKNOWN'); ?>
							<input type="hidden" name="fields[created_by]" value="<?php echo $this->escape($this->item->created_by); ?>" />
						</td> 
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_CONTENT_FIELD_CREATED_LABEL');?></th>
						<td>
							<time datetime="<?php echo $this->item->created; ?>"><?php echo Date::of($this->item->created)->toLocal(); ?></time>
						</td>
					</tr>
					<?php if ($this->item->get('modified_by', false)): ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_CONTENT_FIELD_MODIFIER_LABEL'); ?></th>
							<td>
								<?php echo $this->item->modified_by ? User::getInstance($this->item->modified_by)->get('name') : Lang::txt('JUNKNOWN'); ?>
								<input type="hidden" name="fields[modified_by]" value="<?php echo $this->escape($this->item->modified_by); ?>" />
							</td> 
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_CONTENT_FIELD_MODIFIED_LABEL');?></th>
							<td>
								<time datetime="<?php echo $this->item->modified; ?>"><?php echo Date::of($this->item->modified)->toLocal(); ?></time>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<?php echo Html::sliders('start', 'content-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
			<?php // Do not show the publishing options if the edit form is configured not to. ?>
			<?php if ($params['show_publishing_options'] || ($params['show_publishing_options'] = '' && !empty($editoroptions))): ?>
				<?php echo Html::sliders('panel', Lang::txt('COM_CONTENT_FIELDSET_PUBLISHING'), 'publishing-details');?>
				<fieldset class="panelform">
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('publish_up'); ?>
						<?php echo $this->form->getInput('publish_up'); ?>
					</div>

					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('publish_down'); ?>
						<?php echo $this->form->getInput('publish_down'); ?>
					</div>

					<?php if ($this->item->version) : ?>
						<div class="form-group input-wrap">
							<?php echo $this->form->getLabel('version'); ?>
							<?php echo $this->form->getInput('version'); ?>
						</div>
					<?php endif; ?>

					<?php if ($this->item->hits) : ?>
						<div class="form-group input-wrap">
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
								<div class="form-group input-wrap">
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
				<div class="form-group input-wrap">
					<?php echo $this->form->getLabel('xreference'); ?>
					<?php echo $this->form->getInput('xreference'); ?>
				</div>
			</fieldset>

			<?php
			// We need to make a separate space for the configuration
			// so that those fields always show to those with permissions
			?>
			<?php if (User::authorise('core.admin')): ?>
				<?php echo Html::sliders('panel', Lang::txt('COM_CONTENT_SLIDER_EDITOR_CONFIG'), 'configure-sliders'); ?>
				<fieldset class="panelform">
					<?php foreach ($this->form->getFieldset('editorConfig') as $field) : ?>
						<div class="form-group input-wrap">
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
					<div class="form-group input-wrap">
						<?php echo $this->form->getLabel('images'); ?>
						<?php echo $this->form->getInput('images'); ?>
					</div>

					<?php foreach ($this->form->getGroup('images') as $field): ?>
						<div class="form-group input-wrap">
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endforeach; ?>

					<?php foreach ($this->form->getGroup('urls') as $field): ?>
						<div class="form-group input-wrap">
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
				</fieldset>

			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<?php if (User::authorise('core.manage')): ?>
		<div class="width-100">
			<fieldset class="panelform">
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo Request::getCmd('return'); ?>" />
	<?php echo Html::input('token'); ?>
</form>
