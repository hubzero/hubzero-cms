<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_POLL_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input class="inputbox" type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape($this->poll->get('title')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_POLL_FIELD_ALIAS'); ?>:</label><br />
					<input class="inputbox" type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->poll->get('alias')); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_POLL_FIELD_LAG_HINT'); ?>">
					<label for="field-lag"><?php echo Lang::txt('COM_POLL_FIELD_LAG'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input class="inputbox" type="text" name="fields[lag]" id="field-lag" class="required" value="<?php echo $this->escape($this->poll->get('lag', 86400)); ?>" />
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
