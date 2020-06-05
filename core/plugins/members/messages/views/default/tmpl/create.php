<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//is the autocompleter disabled
$disabled = ($this->tos) ? true : false;

//get autocompleter
$tos = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'mbrs', 'members', '', $this->tos, '', $disabled)));

$this->css();
?>
<form action="<?php echo Route::url($this->member->link() . '&active=messages'); ?>" method="post" id="hubForm<?php echo ($this->no_html) ? '-ajax' : ''; ?>">
	<fieldset class="hub-mail">
		<div class="cont">
			<h3><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_COMPOSE_MESSAGE'); ?></h3>
			<label<?php if ($this->no_html) { echo ' class="width-65"'; } ?>>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_TO'); ?>
				<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<?php
					if (count($tos) > 0)
					{
						echo $tos[0];
					}
					else
					{
						echo '<input type="text" name="mbrs" id="members" value="" />';
					}
				?>
			</label>
			<label>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?>
				<input type="text" name="subject" id="msg-subject" value="<?php echo $this->escape(Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT_MESSAGE')); ?>"  />
			</label>
			<label>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MESSAGE'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
			</label>
			<p class="submit">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SEND'); ?>" />
			</p>
		</div>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="messages" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="action" value="send" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<?php echo Html::input('token'); ?>
</form>