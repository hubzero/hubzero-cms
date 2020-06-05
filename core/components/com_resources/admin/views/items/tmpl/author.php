<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
	<li id="author_<?php echo $this->id; ?>">
		<span class="handle"><?php echo Lang::txt('COM_RESOURCES_AUTHOR_DRAG'); ?></span>
		<a class="state trash" data-parent="author_<?php echo $this->id; ?>" href="#" onclick="HUB.Resources.removeAuthor('author_<?php echo $this->id; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
		<?php echo $this->escape(stripslashes($this->name)); ?> (<?php echo $this->id; ?>)
		<br /><?php echo Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION'); ?>: <input type="text" name="<?php echo $this->id; ?>_organization" value="<?php echo $this->escape(stripslashes($this->org)); ?>" />

		<select name="<?php echo $this->id; ?>_role">
			<option value=""<?php if (empty($this->role)) { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
<?php
	if ($this->roles)
	{
		foreach ($this->roles as $role)
		{
?>
			<option value="<?php echo $this->escape($role->alias); ?>"<?php if (isset($this->role) && ($this->role == $role->alias)) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php
		}
	}
?>
		</select>
		<input type="hidden" class="authid" name="<?php echo $this->id; ?>authid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="<?php echo $this->id; ?>_name" value="<?php echo $this->escape(stripslashes($this->name)); ?>" />
	</li>
