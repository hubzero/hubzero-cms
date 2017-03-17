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
