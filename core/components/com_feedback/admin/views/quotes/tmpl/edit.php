<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Feedback\Helpers\Permissions::getActions('quote');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FEEDBACK') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('quote');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$short_quote = stripslashes($this->row->get('short_quote'));
$miniquote   = stripslashes($this->row->get('miniquote'));
if (!$short_quote)
{
	$short_quote =  substr(stripslashes($this->row->get('quote')), 0, 270);
}
if (!$miniquote)
{
	$miniquote =  substr(stripslashes($short_quote), 0, 150);
}

if (strlen($short_quote) >= 271)
{
	$short_quote = $short_quote . '...';
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_FEEDBACK_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="fields[notable_quote]" id="field-notable_quote" value="1" <?php if ($this->row->get('notable_quote') == 1) { echo 'checked="checked"'; } ?> />
					<label for="field-notable_quote"><?php echo Lang::txt('COM_FEEDBACK_SELECT_FOR_QUOTES'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-fullname"><?php echo Lang::txt('COM_FEEDBACK_FULL_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[fullname]" id="field-fullname" class="required" value="<?php echo $this->escape(stripslashes($this->row->get('fullname'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-org"><?php echo Lang::txt('COM_FEEDBACK_ORGANIZATION'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[org]" id="field-org" class="required" value="<?php echo $this->escape(stripslashes($this->row->org)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION'); ?>">
					<label for="field-user_id"><?php echo Lang::txt('COM_FEEDBACK_USER_ID'); ?>:</label><br />
					<input type="text" name="fields[user_id]" id="field-user_id" value="<?php echo $this->escape(stripslashes($this->row->get('user_id'))); ?>" <?php if ($this->row->get('id') && $this->row->get('user_id')) { echo 'readonly="readonly"'; } ?> />
					<?php
						if (!$this->row->get('id'))
						{
							echo '<span class="hint">' . Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION') . '</span>';
						}
					?>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENTS'); ?>:</legend>

					<div class="input-wrap">
						<input type="checkbox" name="fields[publish_ok]" id="publish_ok" value="1" <?php if ($this->row->get('publish_ok') == 1) { echo ' checked="checked"';
} if ($this->row->get('id')) { echo ' disabled="disabled"'; } ?>  />
						<label for="publish_ok"><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />

						<input type="checkbox" name="fields[contact_ok]" id="contact_ok" value="1" <?php if ($this->row->get('contact_ok') == 1) { echo ' checked="checked"';
} if ($this->row->get('id')) { echo ' disabled="disabled"'; } ?> />
						<label for="contact_ok"><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
					</div>
				</fieldset>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?>">
					<label for="field-short_quote"><?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE'); ?>:</label><br />
					<?php echo $this->editor('fields[short_quote]', $short_quote, 40, 10, 'field-short_quote'); ?>
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT'); ?>">
					<label for="miniquote"><?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE'); ?>:</label><br />
					<input type="text" name="fields[miniquote]" id="miniquote" value="<?php echo $this->escape($miniquote); ?>" maxlength="150" />
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-quote"><?php echo Lang::txt('COM_FEEDBACK_FULL_QUOTE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[quote]', stripslashes($this->row->get('quote')), 50, 10, 'field-quote', array('class' => 'required')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-date"><?php echo Lang::txt('COM_FEEDBACK_QUOTE_SUBMITTED'); ?>:</label><br />
					<input type="text" name="fields[date]" id="field-date" value="<?php echo $this->escape($this->row->get('date', Date::toSql())); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?>">
					<label for="field-notes"><?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES'); ?>:</label><br />
					<?php echo $this->editor('fields[notes]', stripslashes($this->row->get('notes')), 50, 10, 'field-notes'); ?>
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_FEEDBACK_PICTURE'); ?></span></legend>

				<div class="input-wrap">
					<?php
					$pictures = $this->row->files();

					foreach ($pictures as $counter => $picture)
					{
						list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());

						// scale if image is bigger than 120w x120h
						$num = max($ow/120, $oh/120);
						if ($num > 1)
						{
							$mw = round($ow/$num);
							$mh = round($oh/$num);
						}
						else
						{
							$mw = $ow;
							$mh = $oh;
						}

						$img = substr($picture->getPathname(), strlen(PATH_ROOT));
						?>
						<div id="picture-<?php echo $counter; ?>">
							<input type="hidden" name="existingPictures[<?php echo $counter; ?>]" id="existingPictures<?php echo $counter; ?>" value="<?php echo $picture->getFilename(); ?>" />
							<a class="fancybox-inline" href="<?php echo $img; ?>">
								<img src="<?php echo $img; ?>" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" alt="" />
							</a>
							<button type="button" class="delete-image" id="<?php echo $counter; ?>"><?php echo Lang::txt('COM_FEEDBACK_DELETE'); ?></button>
						</div>
						<br />
						<?php
					}
					?>
					<input id="imgInp" type="file" name="files[]" multiple="multiple" />
					<div id="uploadImages"></div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
