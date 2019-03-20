<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_HZEXEC_') or die();

?>

<fieldset id="users-profile-core">
	<legend>
		<?php echo Lang::txt('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<dl>
		<dt>
			<?php echo Lang::txt('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->data->name; ?>
		</dd>
		<dt>
			<?php echo Lang::txt('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo htmlspecialchars($this->data->username); ?>
		</dd>
		<dt>
			<?php echo Lang::txt('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo Date::of($this->data->registerDate)->toLocal(); ?>
		</dd>
		<dt>
			<?php echo Lang::txt('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</dt>

		<?php if ($this->data->lastvisitDate && $this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<dd>
				<?php echo Date::of($this->data->lastvisitDate)->toLocal(); ?>
			</dd>
		<?php }
		else {?>
			<dd>
				<?php echo Lang::txt('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</dd>
		<?php } ?>

	</dl>
</fieldset>
