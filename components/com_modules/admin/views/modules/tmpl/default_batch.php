<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$clientId = $this->state->get('filter.client_id');
$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><span><?php echo JText::_('COM_MODULES_BATCH_OPTIONS');?></span></legend>
	<p><?php echo JText::_('COM_MODULES_BATCH_TIP'); ?></p>

	<div class="col width-50 fltlft">
		<div class="input-wrap">
			<?php echo JHtml::_('batch.access');?>
		</div>

		<div class="input-wrap">
			<?php echo JHtml::_('batch.language'); ?>
		</div>
	</div>
	<div class="col width-50 fltrt">
	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('modules.positions', $clientId);?>
	<?php endif; ?>

		<div class="input-wrap">
			<button type="submit" onclick="Joomla.submitbutton('module.batch');">
				<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
			</button>
			<button type="button" onclick="$('#batch-position-id').val('');$('#batch-access').val('');$('#batch-language-id').val('');">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
	</div>
	<div class="clr"></div>
</fieldset>
