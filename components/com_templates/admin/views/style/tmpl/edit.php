<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$user = JFactory::getUser();
$canDo = TemplatesHelper::getActions();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'style.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, $('#item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_templates&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS');?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</div>

			<div class="width-50 fltlft">
				<div class="input-wrap">
					<?php echo $this->form->getLabel('template'); ?>
					<?php echo $this->form->getInput('template'); ?>
				</div>
			</div>
			<div class="width-50 fltrt">
				<div class="input-wrap">
					<?php echo $this->form->getLabel('client_id'); ?>
					<?php echo $this->form->getInput('client_id'); ?>
					<label for="client-readonly"><?php echo JText::_('Client');?></label>
					<input type="text" id="client-readonly" value="<?php echo $this->item->client_id == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>" class="readonly" readonly="readonly" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('home'); ?>
				<?php echo $this->form->getInput('home'); ?>
			</div>

			<div class="input-wrap">
				<table class="meta">
					<tbody>
					<?php if ($this->item->id) : ?>
						<tr>
							<th><?php echo $this->form->getLabel('id'); ?></th>
							<td>
								<?php echo $this->item->id; ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->xml) : ?>
						<?php if ($text = trim($this->item->xml->description)) : ?>
							<tr>
								<th><?php echo JText::_('COM_TEMPLATES_TEMPLATE_DESCRIPTION'); ?></th>
								<td><?php echo JText::_($text); ?></td>
							</tr>
						<?php endif; ?>
					<?php else : ?>
						<tr>
							<td colspan="2">
								<p class="error"><?php echo JText::_('COM_TEMPLATES_ERR_XML'); ?></p>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'template-sliders-'.$this->item->id); ?>

		<?php
			//get the menu parameters that are automatically set but may be modified.
			echo $this->loadTemplate('options');
		?>
		<div class="clr"></div>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<?php if ($user->authorise('core.edit', 'com_menu') && $this->item->client_id==0):?>
		<?php if ($canDo->get('core.edit.state')) : ?>
			<div class="width-60 fltlft">
			<?php echo $this->loadTemplate('assignment'); ?>
			</div>
		<?php endif; ?>
	<?php endif;?>

	<div class="clr"></div>
</form>
