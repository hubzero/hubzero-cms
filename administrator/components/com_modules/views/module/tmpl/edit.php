<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.combobox');
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
				alert('".$this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'))."');
			}
	}";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_modules&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

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

			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
				<div class="col width-50 fltlft">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('published'); ?><br />
						<?php echo $this->form->getInput('published'); ?>
					</div>
				</div>
				<div class="col width-50 fltrt">
			<?php endif; ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('access'); ?><br />
						<?php echo $this->form->getInput('access'); ?>
					</div>
			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
				</div>
				<div class="clr"></div>
			<?php endif; ?>

			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
				<div class="col width-50 fltlft">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('publish_up'); ?><br />
						<?php echo $this->form->getInput('publish_up'); ?>
					</div>
				</div>
				<div class="col width-50 fltrt">
					<div class="input-wrap">
						<?php echo $this->form->getLabel('publish_down'); ?><br />
						<?php echo $this->form->getInput('publish_down'); ?>
					</div>
				</div>
				<div class="clr"></div>
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
	</div>

	<div class="width-40 fltrt">
		<table class="meta">
			<tbody>
			<?php if ($this->item->id) : ?>
			<?php endif; ?>
				<tr>
					<th>
						<?php echo JText::_('COM_MODULES_HEADING_MODULE'); ?>
						<?php echo $this->form->getLabel('module'); ?>
					</th>
					<td>
						<?php echo $this->form->getInput('module'); ?>
						<?php if ($this->item->xml) echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->module;else echo JText::_('COM_MODULES_ERR_XML');?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Client'); ?>
						<?php echo $this->form->getLabel('client_id'); ?>
					</th>
					<td>
						<?php echo $this->form->getInput('client_id'); ?>
						<?php echo $this->item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_MODULES_MODULE_DESCRIPTION'); ?>
					</th>
					<td>
						<?php if ($this->item->xml) : ?>
							<?php if ($text = trim($this->item->xml->description)) : ?>
								<?php echo JText::_($text); ?>
							<?php endif; ?>
						<?php else : ?>
							<p class="error"><?php echo JText::_('COM_MODULES_ERR_XML'); ?></p>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php echo JHtml::_('sliders.start', 'module-sliders'); ?>
		<?php echo $this->loadTemplate('options'); ?>
		<div class="clr"></div>
	<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<?php if ($hasContent) : ?>
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_MODULES_CUSTOM_OUTPUT'); ?></span></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('content'); ?><br />
					<?php echo $this->form->getInput('content'); ?>
				</div>
			</fieldset>
		</div>
	<?php endif; ?>

	<?php if ($this->item->client_id == 0) :?>
		<div class="width-60 fltlft">
			<?php echo $this->loadTemplate('assignment'); ?>
		</div>
	<?php endif; ?>

	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
