<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('framework');

Toolbar::title(Lang::txt('COM_MESSAGES_VIEW_PRIVATE_MESSAGE'), 'inbox.png');
$sender = User::getInstance($this->item->user_id_from);
if ($sender->authorise('core.admin') || $sender->authorise('core.manage', 'com_messages') && $sender->authorise('core.login.admin'))
{
	Toolbar::custom('message.reply', 'restore.png', 'restore_f2.png', 'COM_MESSAGES_TOOLBAR_REPLY', false);
}
Toolbar::cancel('message.cancel');
Toolbar::help('JHELP_COMPONENTS_MESSAGING_READ');

?>
<form action="<?php echo Route::url('index.php?option=com_messages'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<ul class="adminformlist">
			<li><?php echo Lang::txt('COM_MESSAGES_FIELD_USER_ID_FROM_LABEL'); ?>
			<?php echo $this->item->get('from_user_name');?></li>

			<li><?php echo Lang::txt('COM_MESSAGES_FIELD_DATE_TIME_LABEL'); ?>
			<?php echo Date::of($this->item->date_time)->toSql();?></li>

			<li><?php echo Lang::txt('COM_MESSAGES_FIELD_SUBJECT_LABEL'); ?>
			<?php echo $this->item->subject;?></li>

			<li><?php echo Lang::txt('COM_MESSAGES_FIELD_MESSAGE_LABEL'); ?>
			<pre class="pre_message"><?php echo $this->escape($this->item->message);?></pre></li>
		</ul>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="reply_id" value="<?php echo $this->item->message_id; ?>" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>
