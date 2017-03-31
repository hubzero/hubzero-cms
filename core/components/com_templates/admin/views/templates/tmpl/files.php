<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Templates\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_VIEW_TEMPLATE'), 'thememanager');

Toolbar::cancel('template.cancel', 'JTOOLBAR_CLOSE');
Toolbar::divider();
Toolbar::help('template');

// Include the component HTML helpers.
Html::behavior('tooltip');
Html::behavior('modal');

$this->css();
?>
<div id="item-form">
	<div class="grid">
		<div class="col span6">
			<form action="<?php echo Route::url('index.php?option=com_templates&controller=templates'); ?>" method="post" name="adminForm" id="adminForm">
				<fieldset class="adminform" id="template-manager-description">
					<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_DESCRIPTION');?></legend>

					<div class="input-wrap">
						<?php echo Components\Templates\Helpers\Utilities::thumb($this->template->element, $this->template->client_id); ?>

						<h2><?php echo ucfirst($this->template->element); ?></h2>
						<p><?php echo Lang::txt($this->template->xml->get('description')); ?></p>
					</div>
				</fieldset>
				<fieldset class="adminform" id="template-manager-files">
					<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_MASTER_FILES');?></legend>

					<ul class="item-list layout">
						<?php foreach ($this->files['main'] as $key => $file) : ?>
							<li>
								<?php $id = $file->id; ?>
								<?php if ($canDo->get('core.edit')) : ?>
									<a href="<?php echo Route::url('index.php?option=com_templates&controller=source&task=edit&id=' . $id); ?>">
								<?php endif; ?>
									<?php echo Lang::txt('Edit %s', $file->get('name')); //Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_' . strtoupper($key)); ?>
								<?php if ($canDo->get('core.edit')) : ?>
									</a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
				<input type="hidden" name="task" value="" />
			</form>
			<div class="clr"></div>

			<form action="<?php echo Route::url('index.php?option=com_templates&controller=templates&task=copy&id=' . $this->template->get('id')); ?>" method="post" name="copyForm">
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
			<?php echo Html::sliders('start', 'template-sliders-' . $this->template->get('id')); ?>

			<?php echo Html::sliders('panel', Lang::txt('COM_TEMPLATES_TEMPLATE_ASSETS'), 'tmeplate-assets'); ?>
			<!-- <fieldset class="adminform" id="template-manager-css">
				<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_ASSETS');?></legend> -->

				<?php if (!empty($this->files['clo'])) : ?>
					<ul class="item-list css">
						<?php foreach ($this->files['clo'] as $file) : ?>
						<li>
							<?php if ($canDo->get('core.edit')) : ?>
							<a href="<?php echo Route::url('index.php?option=com_templates&controller=source&task=edit&id=' . $file->get('id')); ?>">
							<?php endif; ?>
								<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_CSS', $file->get('name')); ?>
							<?php if ($canDo->get('core.edit')) : ?>
							</a>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<!-- </fieldset> -->

			<?php echo Html::sliders('panel', Lang::txt('COM_TEMPLATES_TEMPLATE_HTML'), 'tmeplate-html'); ?>
			<!-- <fieldset class="adminform" id="template-manager-html">
				<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_HTML');?></legend> -->

				<?php if (!empty($this->files['html'])) : ?>
					<ul class="item-list css">
						<?php foreach ($this->files['html'] as $file) : ?>
						<li>
							<?php if ($canDo->get('core.edit')) : ?>
								<a href="<?php echo Route::url('index.php?option=com_templates&controller=source&task=edit&id=' . $file->get('id')); ?>">
							<?php endif; ?>
							<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_EDIT_HTML', $file->get('name')); ?>
							<?php if ($canDo->get('core.edit')) : ?>
								</a>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<!-- </fieldset> -->

			<?php echo Html::sliders('end'); ?>
		</div>
	</div>
</div>