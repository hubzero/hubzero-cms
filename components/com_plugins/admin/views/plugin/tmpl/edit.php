<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'plugin.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_plugins&layout=edit&extension_id='.(int) $this->item->extension_id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS') ?></span></legend>

			<!--
			<div class="input-wrap">
				<?php echo $this->form->getLabel('name'); ?><br />
				<?php echo $this->form->getInput('name'); ?>
				<span class="readonly plg-name"><?php echo JText::_($this->item->name);?></span>
			</div>
		-->

			<div class="width-50 fltlft">
				<div class="input-wrap">
					<?php echo $this->form->getLabel('enabled'); ?><br />
					<?php echo $this->form->getInput('enabled'); ?>
				</div>
			</div>
			<div class="width-50 fltrt">
				<div class="input-wrap">
					<?php echo $this->form->getLabel('access'); ?><br />
					<?php echo $this->form->getInput('access'); ?>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('ordering'); ?><br />
				<?php echo $this->form->getInput('ordering'); ?>
			</div>
			<!--
			<div class="input-wrap">
				<?php echo $this->form->getLabel('folder'); ?><br />
				<?php echo $this->form->getInput('folder'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('element'); ?><br />
				<?php echo $this->form->getInput('element'); ?>
			</div>

			<?php if ($this->item->extension_id) : ?>
				<div class="input-wrap">
					<?php echo $this->form->getLabel('extension_id'); ?><br />
					<?php echo $this->form->getInput('extension_id'); ?>
				</div>
			<?php endif; ?>
			-->
		</fieldset>

			<table class="meta">
				<tbody>
					<tr>
						<th>
							<?php echo JText::_('COM_PLUGINS_FIELD_NAME_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->name); ?>
							<?php echo $this->form->getInput('name'); ?>
						</td>
					</tr>
				<?php if ($this->item->extension_id) : ?>
					<tr>
						<th>
							<?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->extension_id); ?>
							<input type="hidden" name="jform[extension_id]" id="jform_extension_id" value="<?php echo $this->item->extension_id; ?>" />
						</td>
					</tr>
				<?php endif; ?>
					<tr>
						<th>
							<?php echo JText::_('COM_PLUGINS_FIELD_FOLDER_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->folder); ?>
							<input type="hidden" name="jform[folder]" id="jform_folder" value="<?php echo $this->escape($this->item->folder); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php echo JText::_('COM_PLUGINS_FIELD_ELEMENT_LABEL'); ?>
						</th>
						<td>
							<?php echo $this->escape($this->item->element); ?>
							<input type="hidden" name="jform[element]" id="jform_element" value="<?php echo $this->escape($this->item->element); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
						</th>
						<td>
							<?php if ($this->item->xml) : ?>
								<?php if ($text = trim($this->item->xml->description)) : ?>
									<?php echo JText::_($text); ?>
								<?php endif; ?>
							<?php else : ?>
								<p class="error"><?php echo JText::_('COM_PLUGINS_XML_ERR'); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>

	</div>

	<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start', 'plugin-sliders-'.$this->item->extension_id); ?>

		<?php echo $this->loadTemplate('options'); ?>

		<div class="clr"></div>

	<?php echo JHtml::_('sliders.end'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>

	<input type="hidden" name="component" value="<?php echo JRequest::getCmd('component', ''); ?>" />

	<div class="clr"></div>
</form>
