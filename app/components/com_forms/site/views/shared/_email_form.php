<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$email = $this->email;
$hiddenFields = isset($this->hiddenFields) ? $this->hiddenFields : [];
$replyToTip = Lang::txt('COM_FORMS_EMAIL_REPLY_TO_TIP');
?>

<form id="hubForm" action="<?php echo $action; ?>">

	<fieldset>
		<label>
			<?php echo Lang::txt('COM_FORMS_FIELDS_TITLE'); ?>
			<input type="text" name="email[title]"
				value="<?php echo $email->getTitle(); ?>">
		</label>

		<label>
			<?php echo Lang::txt('COM_FORMS_FIELDS_REPLY_TO'); ?>
			&nbsp
			<span class="hasTip" title="<?php echo $replyToTip; ?>">
				<span class="fontcon">&#xf075</span>
			</span>
			<input type="text" name="email[reply_to]"
				value="<?php echo implode($email->getReplyTo(), ','); ?>">
		</label>

		<label>
			<?php echo Lang::txt('COM_FORMS_FIELDS_CONTENT'); ?>
			<textarea name="email[content]" rows="10"><?php echo $email->getContent(); ?></textarea>
		</label>

		<input type="submit" class="btn btn-success"
			value="<?php echo Lang::txt('COM_FORMS_FIELDS_VALUES_SEND_EMAIL'); ?>">
	</fieldset>

	<span>
		<?php foreach($hiddenFields as $name => $value): ?>
			<input type="hidden"
				name="<?php echo $name; ?>"
				value="<?php echo $value; ?>">
		<?php	endforeach; ?>
	</span>

</form>
