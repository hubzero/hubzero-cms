<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	<div class="col width-60 fltlft">
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
	<div class="col width-40 fltrt">
		<p class="warning"><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME_NOTE'); ?></p>
	</div>
	<div class="clr"></div>
</form>