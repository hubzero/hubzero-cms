<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Create the copy/move options.
$options = array(
	Html::select('option', 'add', Lang::txt('COM_MEMBERS_BATCH_ADD')),
	Html::select('option', 'del', Lang::txt('COM_MEMBERS_BATCH_DELETE')),
	Html::select('option', 'set', Lang::txt('COM_MEMBERS_BATCH_SET'))
);

?>
<fieldset class="batch">
	<legend><?php echo Lang::txt('COM_MEMBERS_BATCH_OPTIONS');?></legend>

	<div class="grid">
		<div class="col span6">
			<div class="combo" id="batch-choose-action">
				<div class="input-wrap">
					<label id="batch-choose-action-lbl" for="batch-choose-action"><?php echo Lang::txt('COM_MEMBERS_BATCH_GROUP') ?></label>
					<select name="batch[group_id]" class="inputbox" id="batch-group-id">
						<option value=""><?php echo Lang::txt('JSELECT') ?></option>
						<?php echo Html::select('options', Components\Members\Helpers\Admin::getAccessGroups()); //Html::user('groups', User::get('isRoot'))); ?>
					</select>
				</div>

				<div class="input-wrap">
					<?php echo Html::select('radiolist', $options, 'batch[group_action]', '', 'value', 'text', 'add') ?>
				</div>
			</div>
		</div>
		<div class="col span6">
			<div class="input-wrap">
				<button type="submit" id="btn-batch-submit">
					<?php echo Lang::txt('JGLOBAL_BATCH_PROCESS'); ?>
				</button>
				<button type="button" id="btn-batch-clear">
					<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	</div>
</fieldset>
