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

defined('_HZEXEC_') or die();

$canDo = \Components\Poll\Helpers\Permissions::getActions('component');

$text = ($this->poll->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_POLL') . ': ' . $text, 'poll.png');
if ($this->poll->id)
{
	Toolbar::preview('index.php?option=' . $this->option . '&task=preview&id=' . $this->poll->id);
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::apply();
	Toolbar::spacer();
}
if ($this->poll->id)
{
	// for existing items the button is renamed `close`
	Toolbar::cancel('cancel', 'COM_POLL_CLOSE');
}
else
{
	Toolbar::cancel();
}
Toolbar::spacer();
Toolbar::help('poll');
?>

<script type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.title.value == "") {
			alert( "<?php echo Lang::txt('COM_POLL_ERROR_MISSING_TITLE', true); ?>" );
		} else if (isNaN(parseInt( form.lag.value ) ) || parseInt(form.lag.value) < 1)  {
			alert( "<?php echo Lang::txt('COM_POLL_ERROR_MISSING_LAG', true); ?>" );
		//} else if (form.menu.options.value == ""){
		//	alert( "COM_POLL_ERROR_MISSING_OPTIONS" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_POLL_FIELD_TITLE'); ?>:</label><br />
					<input class="inputbox" type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->poll->get('title')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_POLL_FIELD_ALIAS'); ?>:</label><br />
					<input class="inputbox" type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->poll->get('alias')); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt( 'COM_POLL_FIELD_LAG_HINT'); ?>">
					<label for="field-lag"><?php echo Lang::txt('COM_POLL_FIELD_LAG'); ?>:</label><br />
					<input class="inputbox" type="text" name="fields[lag]" id="field-lag" value="<?php echo $this->escape($this->poll->get('lag')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_POLL_FIELD_LAG_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_POLL_FIELD_PUBLISHED'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->poll->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->poll->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->poll->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_POLL_FIELD_ACCESS_LEVEL'); ?>:</label>
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->poll->get('access')); ?>
					</select>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_POLL_FIELD_OPEN'); ?>:</label><br />
					<?php echo Html::select('booleanlist', 'fields[open]', 'class="inputbox"', $this->poll->get('open')); ?>
				</div>
			</fieldset>
			<p class="warning"><?php echo Lang::txt('COM_POLL_WARNING'); ?></p>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_POLL_FIELDSET_OPTIONS'); ?></span></legend>

				<?php
				$i = 0;
				$n = $this->options->count();
				foreach ($this->options as $option) { ?>
					<div class="input-wrap">
						<label for="polloption<?php echo $option->id; ?>"><?php echo Lang::txt('COM_POLL_FIELD_OPTION'); ?> <?php echo ($i+1); ?></label><br />
						<input class="inputbox" type="text" name="polloption[<?php echo $option->id; ?>]" id="polloption<?php echo $option->id; ?>" value="<?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>" />
					</div>
				<?php
					$i++;
				} ?>
				<?php for (; $i < 12; $i++) { ?>
					<div class="input-wrap">
						<label for="polloption<?php echo $i + 1; ?>"><?php echo Lang::txt('COM_POLL_FIELD_OPTION'); ?> <?php echo $i + 1; ?></label><br />
						<input class="inputbox" type="text" name="polloption[]" id="polloption<?php echo $i + 1; ?>" value="" />
					</div>
				<?php } ?>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->poll->get('id'); ?>" />

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->poll->get('id'); ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />

	<?php echo Html::input('token'); ?>
</form>
