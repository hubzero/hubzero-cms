<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('keepalive');

$this->js();

$data = $this->get('data');
?>

<div id="mailto-window">
	<h2>
		<?php echo Lang::txt('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
	</h2>

	<div class="mailto-close">
		<a href="#close" class="cancel" title="<?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?>">
			<span><?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?></span>
		</a>
	</div>

	<form action="<?php echo Route::url('index.php?option=com_mailto'); ?>" id="mailtoForm" method="post">
		<div class="formelm">
			<label for="mailto_field"><?php echo Lang::txt('COM_MAILTO_EMAIL_TO'); ?></label>
			<input type="text" id="mailto_field" name="mailto" class="inputbox" size="25" value="<?php echo $this->escape($data->mailto); ?>"/>
		</div>
		<div class="formelm">
			<label for="sender_field">
			<?php echo Lang::txt('COM_MAILTO_SENDER'); ?></label>
			<input type="text" id="sender_field" name="sender" class="inputbox" value="<?php echo $this->escape($data->sender); ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="from_field">
			<?php echo Lang::txt('COM_MAILTO_YOUR_EMAIL'); ?></label>
			<input type="text" id="from_field" name="from" class="inputbox" value="<?php echo $this->escape($data->from); ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="subject_field">
			<?php echo Lang::txt('COM_MAILTO_SUBJECT'); ?></label>
			<input type="text" id="subject_field" name="subject" class="inputbox" value="<?php echo $this->escape($data->subject); ?>" size="25" />
		</div>
		<p>
			<button class="button" id="mailto_send">
				<?php echo Lang::txt('COM_MAILTO_SEND'); ?>
			</button>
			<button class="button" id="mailto_cancel" class="cancel">
				<?php echo Lang::txt('JCANCEL'); ?>
			</button>
		</p>
		<input type="hidden" name="layout" value="<?php echo $this->getLayout(); ?>" />
		<input type="hidden" name="option" value="com_mailto" />
		<input type="hidden" name="task" value="send" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="link" value="<?php echo $data->link; ?>" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
