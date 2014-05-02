<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$extension	= $this->escape($this->state->get('filter.extension'));
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_CATEGORIES_BATCH_OPTIONS');?></legend>

	<p><?php echo JText::_('COM_CATEGORIES_BATCH_TIP'); ?></p>

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
		<div class="input-wrap combo" id="batch-choose-action">
			<label id="batch-choose-action-lbl" for="batch-category-id">
				<?php echo JText::_('COM_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
			</label><br />

			<div class="col width-50 fltlft">
			<select name="batch[category_id]" class="inputbox" id="batch-category-id">
				<option value=""><?php echo JText::_('JSELECT') ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.categories', $extension, array('filter.published' => $published)));?>
			</select>
			</div>
			<div class="col width-50 fltrt">
			<?php echo JHtml::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
			</div>
			<div class="clr"></div>
		</div>
	<?php endif; ?>

		<div class="input-wrap">
			<button type="submit" onclick="submitbutton('category.batch');">
				<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
			</button>
			<button type="button" onclick="$('#batch-category-id').val('');$('#batch-access').val('');$('#batch-language-id').val('');">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
	</div>
	<div class="clr"></div>
</fieldset>
