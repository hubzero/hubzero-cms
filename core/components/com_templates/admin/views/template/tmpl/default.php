<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('modal');
$canDo = TemplatesHelper::getActions();

\Hubzero\Document\Assets::addComponentStylesheet('com_templates');
?>
<div id="item-form">
	<div class="grid">
		<div class="col span6">
			<form action="<?php echo Route::url('index.php?option=com_templates&view=templates'); ?>" method="post" name="adminForm" id="adminForm">
				<fieldset class="adminform" id="template-manager-description">
					<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_DESCRIPTION');?></legend>

					<div class="input-wrap">
						<?php echo Html::templates('thumb', $this->template->element, $this->template->client_id); ?>

						<h2><?php echo ucfirst($this->template->element); ?></h2>
						<?php //$client = JApplicationHelper::getClientInfo($this->template->client_id); ?>
						<p><?php $this->template->xmldata = TemplatesHelper::parseXMLTemplateFile(($this->template->protected ? PATH_CORE : PATH_APP), $this->template->element);?></p>
						<p><?php echo Lang::txt($this->template->xmldata->description); ?></p>
					</div>
				</fieldset>
				<fieldset class="adminform" id="template-manager-files">
					<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_MASTER_FILES');?></legend>

					<ul class="item-list layout">
						<li>
							<?php $id = $this->files['main']['index']->id; ?>
							<?php if ($canDo->get('core.edit')) : ?>
							<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $id); ?>">
							<?php endif; ?>
								<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_MAIN');?>
							<?php if ($canDo->get('core.edit')) : ?>
								</a>
							<?php endif; ?>
						</li>
						<?php if ($this->files['main']['error']->exists) : ?>
						<li>
							<?php $id = $this->files['main']['error']->id; ?>
							<?php if ($canDo->get('core.edit')) : ?>
								<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $id); ?>">
							<?php endif; ?>
								<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_ERROR');?>
							<?php if ($canDo->get('core.edit')) : ?>
								</a>
							<?php endif; ?>
						</li>
						<?php endif; ?>
						<?php if ($this->files['main']['offline']->exists) :  ;?>
							<li>
								<?php $id = $this->files['main']['offline']->id; ?>
								<?php if ($canDo->get('core.edit')) : ?>
									<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $id); ?>">
								<?php endif; ?>
									<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_OFFLINEVIEW');?>
								<?php if ($canDo->get('core.edit')) : ?>
									</a>
								<?php endif; ?>
							</li>
						<?php endif; ?>
						<?php if ($this->files['main']['print']->exists) : ?>
						<li>
							<?php $id = $this->files['main']['print']->id; ?>
							<?php if ($canDo->get('core.edit')) : ?>
								<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $id); ?>">
							<?php endif; ?>
								<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_PRINTVIEW');?>
							<?php if ($canDo->get('core.edit')) : ?>
								</a>
							<?php endif; ?>
						</li>
						<?php endif; ?>
					</ul>
				</fieldset>
				<input type="hidden" name="task" value="" />
			</form>
			<div class="clr"></div>

			<form action="<?php echo Route::url('index.php?option=com_templates&task=template.copy&id=' . Request::getInt('id')); ?>" method="post" name="copyForm">
				<fieldset class="adminform" id="template-manager-css">
					<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_COPY');?></legend>
					<div class="input-wrap">
						<label id="new_name" class="hasTip" title="<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NEW_NAME_DESC'); ?>"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NEW_NAME_LABEL')?></label>
						<input class="inputbox" type="text" id="new_name" name="new_name"  />
						<button type="submit"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_COPY'); ?></button>
					</div>
				</fieldset>
				<input type="hidden" name="task" value="" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>

		<div class="col span6">
			<fieldset class="adminform" id="template-manager-css">
				<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_CSS');?></legend>

				<?php if (!empty($this->files['css'])) : ?>
				<ul class="item-list css">
					<?php foreach ($this->files['css'] as $file) : ?>
					<li>
						<?php if ($canDo->get('core.edit')) : ?>
						<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $file->id); ?>">
						<?php endif; ?>
							<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_CSS', $file->name);?>
						<?php if ($canDo->get('core.edit')) : ?>
						</a>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>

				<!--<div>
					<a href="#" class="modal">
						<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_ADD_CSS');?></a>
				</div>-->
			</fieldset>

			<fieldset class="adminform" id="template-manager-html">
				<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_HTML');?></legend>

				<?php if (!empty($this->files['html'])) : ?>
				<ul class="item-list css">
					<?php foreach ($this->files['html'] as $file) : ?>
					<li>
						<?php if ($canDo->get('core.edit')) : ?>
							<a href="<?php echo Route::url('index.php?option=com_templates&task=source.edit&id=' . $file->id); ?>">
						<?php endif; ?>
						<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_HTML', $file->name); ?>
						<?php if ($canDo->get('core.edit')) : ?>
							</a>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>
</div>