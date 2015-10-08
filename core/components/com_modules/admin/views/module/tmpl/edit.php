<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('combobox');

$hasContent = empty($this->item->module) || $this->item->module == 'custom' || $this->item->module == 'mod_custom';

$script = "Joomla.submitbutton = function(task)
	{
			if (task == 'module.cancel' || document.formvalidator.isValid($('#item-form'))) {";
if ($hasContent) {
	$script .= $this->form->getField('content')->save();
}
$script .= "	Joomla.submitform(task, document.getElementById('item-form'));
				if (self != top) {
					window.top.setTimeout('window.parent.$.fancybox().close()', 1000);
				}
			} else {
				alert('".$this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'))."');
			}
	}";

Document::addScriptDeclaration($script);
?>
<form action="<?php echo Route::url('index.php?option=com_modules&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('title'); ?><br />
					<?php echo $this->form->getInput('title'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('showtitle'); ?><br />
					<?php echo $this->form->getInput('showtitle'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('position'); ?><br />
					<?php echo $this->form->getInput('position'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('ordering'); ?><br />
					<?php echo $this->form->getInput('ordering'); ?>
				</div>

				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('published'); ?><br />
								<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>
						<div class="col span6">
				<?php endif; ?>
							<div class="input-wrap">
								<?php echo $this->form->getLabel('access'); ?><br />
								<?php echo $this->form->getInput('access'); ?>
							</div>
				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($this->item->xml && (string) $this->item->xml->name != 'Login Form'): ?>
					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('publish_up'); ?><br />
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
						</div>
						<div class="col span6">
							<div class="input-wrap">
								<?php echo $this->form->getLabel('publish_down'); ?><br />
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('language'); ?><br />
					<?php echo $this->form->getInput('language'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('note'); ?><br />
					<?php echo $this->form->getInput('note'); ?>
				</div>

				<?php if ($this->item->id) : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('id'); ?><br />
						<?php echo $this->form->getInput('id'); ?>
					</div>
				<?php endif; ?>
			</fieldset>

			<?php if ($hasContent) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MODULES_CUSTOM_OUTPUT'); ?></span></legend>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('content'); ?><br />
						<?php echo $this->form->getInput('content'); ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<?php if ($this->item->client_id == 0) :?>
				<?php echo $this->loadTemplate('assignment'); ?>
			<?php endif; ?>
		</div>

		<div class="col span5">
			<table class="meta">
				<tbody>
				<?php if ($this->item->id) : ?>
				<?php endif; ?>
					<tr>
						<th>
							<?php echo Lang::txt('COM_MODULES_HEADING_MODULE'); ?>
							<?php echo $this->form->getLabel('module'); ?>
						</th>
						<td>
							<?php echo $this->form->getInput('module'); ?>
							<?php if ($this->item->xml) echo ($text = (string) $this->item->xml->name) ? Lang::txt($text) : $this->item->module;else echo Lang::txt('COM_MODULES_ERR_XML');?>
						</td>
					</tr>
					<tr>
						<th>
							<?php echo Lang::txt('Client'); ?>
							<?php echo $this->form->getLabel('client_id'); ?>
						</th>
						<td>
							<?php echo $this->form->getInput('client_id'); ?>
							<?php echo $this->item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
						</td>
					</tr>
					<tr>
						<th>
							<?php echo Lang::txt('COM_MODULES_MODULE_DESCRIPTION'); ?>
						</th>
						<td>
							<?php if ($this->item->xml) : ?>
								<?php if ($text = trim($this->item->xml->description)) : ?>
									<?php echo Lang::txt($text); ?>
								<?php endif; ?>
							<?php else : ?>
								<p class="error"><?php echo Lang::txt('COM_MODULES_ERR_XML'); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php echo Html::sliders('start', 'module-sliders'); ?>
				<?php echo $this->loadTemplate('options'); ?>
			<?php echo Html::sliders('end'); ?>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
