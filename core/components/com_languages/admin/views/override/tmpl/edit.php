<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');
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

<form action="<?php echo Route::url('index.php?option=com_languages&id='.$this->item->key); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo empty($this->item->key) ? Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND') : Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND'); ?></span></legend>

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

		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_LEGEND'); ?></span></legend>

				<p><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_TIP'); ?></p>

				<div id="refresh-status" class="overrider-spinner">
					<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_REFRESHING'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('searchtype'); ?>
					<?php echo $this->form->getInput('searchtype'); ?>
				</div>
				<div class="input-wrap">
					<?php echo $this->form->getInput('searchstring'); ?>
				</div>
				<p>
					<button type="submit" onclick="Joomla.overrider.searchStrings();return false;">
						<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON'); ?>
					</button>
				</p>
			</fieldset>

			<fieldset id="results-container" class="adminform">
				<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND'); ?></span></legend>

				<span id="more-results">
					<a href="javascript:Joomla.overrider.searchStrings(Joomla.overrider.states.more);">
						<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_MORE_RESULTS'); ?>
					</a>
				</span>
			</fieldset>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $this->item->key; ?>" />
			<?php echo Html::input('token'); ?>
		</div>
	</div>
</form>
