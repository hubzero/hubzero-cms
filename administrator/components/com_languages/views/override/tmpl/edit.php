<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#jform_searchstring').on('focus', function() {
			if (!Joomla.overrider.states.refreshed) {
				<?php if ($this->state->get('cache_expired')): ?>
				Joomla.overrider.refreshCache();
				Joomla.overrider.states.refreshed = true;
				<?php endif; ?>
			}
			$(this).removeClass('invalid');
		});
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'override.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_languages&id='.$this->item->key); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo empty($this->item->key) ? JText::_('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND') : JText::_('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND'); ?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('key'); ?>
				<?php echo $this->form->getInput('key'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('override'); ?>
				<?php echo $this->form->getInput('override'); ?>
			</div>

			<?php if ($this->state->get('filter.client') == 'administrator'): ?>
				<div class="input-wrap">
					<?php echo $this->form->getLabel('both'); ?>
					<?php echo $this->form->getInput('both'); ?>
				</div>
			<?php endif; ?>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('client'); ?>
				<?php echo $this->form->getInput('client'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('file'); ?>
				<?php echo $this->form->getInput('file'); ?>
			</div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_LEGEND'); ?></span></legend>

			<span class="readonly"><?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_TIP'); ?></span>
			<div class="clr"></div>

			<ul class="adminformlist">
				<li id="refresh-status" class="overrider-spinner">
					<?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_REFRESHING'); ?>
				</li>
				<li><?php echo $this->form->getInput('searchstring'); ?>
					<button type="submit" onclick="Joomla.overrider.searchStrings();return false;">
						<?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON'); ?>
					</button>
				</li>
				<li>
					<?php echo $this->form->getLabel('searchtype'); ?>
					<?php echo $this->form->getInput('searchtype'); ?>
				</li>
			</ul>
		</fieldset>

		<fieldset id="results-container" class="adminform">
			<legend><span><?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND'); ?></span></legend>

			<span id="more-results">
				<a href="javascript:Joomla.overrider.searchStrings(Joomla.overrider.states.more);">
					<?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDE_MORE_RESULTS'); ?>
				</a>
			</span>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->key; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>
