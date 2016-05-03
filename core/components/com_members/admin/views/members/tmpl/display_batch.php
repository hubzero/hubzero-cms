<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
				<button type="submit" onclick="Joomla.submitbutton('user.batch');">
					<?php echo Lang::txt('JGLOBAL_BATCH_PROCESS'); ?>
				</button>
				<button type="button" onclick="$('#batch-group-id').val('');">
					<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	</div>
</fieldset>
