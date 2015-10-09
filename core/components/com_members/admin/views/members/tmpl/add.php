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

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('JACTION_CREATE'), 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save('new');
}
Toolbar::cancel();

?>
<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}

		// do field validation
		submitform(pressbutton);
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', (array)$this->getError()); ?></p>
	<?php } ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?></span></legend>

				<input type="hidden" name="option" value="<?php echo $this->option ?>" />
				<input type="hidden" name="task" value="edit" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME_HINT'); ?>">
					<label for="username"><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="profile[username]" id="username" />
					<span class="hint"><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="email"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="profile[email]" id="email" />
				</div>
				<div class="input-wrap">
					<label for="password"><?php echo Lang::txt('COM_MEMBERS_FIELD_PASSWORD'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="profile[password]" id="password" />
				</div>
				<div class="input-wrap">
					<label for="givenName"><?php echo Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="profile[givenName]" id="givenName" />
				</div>
				<div class="input-wrap">
					<label for="middleName"><?php echo Lang::txt('COM_MEMBERS_FIELD_MIDDLE_NAME'); ?>:</label>
					<input type="text" name="profile[middleName]" id="middleName" />
				</div>
				<div class="input-wrap">
					<label for="surname"><?php echo Lang::txt('COM_MEMBERS_FIELD_LAST_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="profile[surname]" id="surname" />
				</div>
			</fieldset>
			<?php echo Html::input('token'); ?>
		</div>
		<div class="col span5">
			<p class="warning"><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME_NOTE'); ?></p>
		</div>
	</div>
</form>