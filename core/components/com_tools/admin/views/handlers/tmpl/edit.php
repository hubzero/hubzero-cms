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

use Components\Tools\Models\Orm\Tool;

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HANDLERS') . ': ' . $text);
Toolbar::save();
Toolbar::cancel();

$this->css('handlers')
     ->js('handlers');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="handlers">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-tool"><?php echo Lang::txt('COM_TOOLS_HANDLERS_TOOLNAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
			<select name="tool" id="field-tool">
				<?php $tools = Tool::whereEquals('state', 7)->order('title', 'asc')->rows(); ?>
				<?php if (count($tools) > 0) : ?>
					<?php foreach ($tools as $tool) : ?>
						<option value="<?php echo $tool->id; ?>"<?php echo $tool->id == $this->row->tool_id ? ' selected="selected"' : ''; ?>><?php echo $tool->title; ?></option>
					<?php endforeach; ?>
				<?php else : ?>
					<option value=""><?php echo Lang::txt('COM_TOOLS_HANDLERS_NO_TOOLS'); ?></option>
				<?php endif; ?>
			</select>
		</div>

		<div class="input-wrap">
			<label for="field-prompt"><?php echo Lang::txt('COM_TOOLS_HANDLERS_PROMPT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
			<input type="text" name="prompt" id="field-prompt" value="<?php echo $this->escape(stripslashes($this->row->prompt)); ?>" size="50" />
		</div>
	</fieldset>
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_TOOLS_HANDLERS_RULES'); ?></span></legend>

		<div class="rule rule-sample">
			<div class="input-wrap">
				<label for="field-extension-new"><?php echo Lang::txt('COM_TOOLS_HANDLERS_EXTENSION'); ?>:</label><br />
				<input type="text" name="" id="field-extension-new" value="" size="50" />
			</div>

			<div class="input-wrap">
				<label for="field-quantity-new"><?php echo Lang::txt('COM_TOOLS_HANDLERS_QUANTITY'); ?>:</label><br />
				<select name="" id="field-quantity-new">
					<option value="1" selected="selected">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
			</div>
			<a class="delete-rule"><?php echo Lang::txt('COM_TOOLS_HANDLERS_DELETE_RULE'); ?></a>
		</div>

		<?php $i = 0; ?>
		<div class="rules">
			<?php foreach ($this->row->rules as $rule) : ?>
				<div class="rule">
					<input type="hidden" name="rules[<?php echo $i; ?>][id]" value="<?php echo $this->escape(stripslashes($rule->id)); ?>" />
					<div class="input-wrap">
						<label for="field-extension-<?php echo $i; ?>"><?php echo Lang::txt('COM_TOOLS_HANDLERS_EXTENSION'); ?>:</label><br />
						<input type="text" name="rules[<?php echo $i; ?>][extension]" id="field-extension-<?php echo $i; ?>" value="<?php echo $this->escape(stripslashes($rule->extension)); ?>" size="50" />
					</div>

					<div class="input-wrap">
						<label for="field-quantity-<?php echo $i; ?>"><?php echo Lang::txt('COM_TOOLS_HANDLERS_QUANTITY'); ?>:</label><br />
						<select name="rules[<?php echo $i; ?>][quantity]" id="field-quantity-<?php echo $i; ?>">
							<option value="1"<?php echo ($rule->quantity == 1) ? 'selected="selected"' : ''; ?>>1</option>
							<option value="2"<?php echo ($rule->quantity == 2) ? 'selected="selected"' : ''; ?>>2</option>
							<option value="3"<?php echo ($rule->quantity == 3) ? 'selected="selected"' : ''; ?>>3</option>
							<option value="4"<?php echo ($rule->quantity == 4) ? 'selected="selected"' : ''; ?>>4</option>
							<option value="5"<?php echo ($rule->quantity == 5) ? 'selected="selected"' : ''; ?>>5</option>
						</select>
					</div>
					<a class="delete-rule"><?php echo Lang::txt('COM_TOOLS_HANDLERS_DELETE_RULE'); ?></a>
				</div>
				<?php $i++; ?>
			<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
		<div class="buttons">
			<a href="#" class="new-rule button"><?php echo Lang::txt('COM_TOOLS_HANDLERS_NEW_RULE'); ?></a>
		</div>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
