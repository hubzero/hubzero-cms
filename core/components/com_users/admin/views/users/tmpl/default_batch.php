<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Create the copy/move options.
$options = array(
	Html::select('option', 'add', Lang::txt('COM_USERS_BATCH_ADD')),
	Html::select('option', 'del', Lang::txt('COM_USERS_BATCH_DELETE')),
	Html::select('option', 'set', Lang::txt('COM_USERS_BATCH_SET'))
);

?>
<fieldset class="batch">
	<legend><?php echo Lang::txt('COM_USERS_BATCH_OPTIONS');?></legend>

	<label id="batch-choose-action-lbl" for="batch-choose-action"><?php echo Lang::txt('COM_USERS_BATCH_GROUP') ?></label>
	<fieldset id="batch-choose-action" class="combo">
		<select name="batch[group_id]" class="inputbox" id="batch-group-id">
			<option value=""><?php echo Lang::txt('JSELECT') ?></option>
			<?php echo Html::select('options', JHtml::_('user.groups', User::get('isRoot'))); ?>
		</select>
		<?php echo Html::select('radiolist', $options, 'batch[group_action]', '', 'value', 'text', 'add') ?>
	</fieldset>

	<button type="submit" onclick="Joomla.submitbutton('user.batch');">
		<?php echo Lang::txt('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="$('#batch-group-id').val('');">
		<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
