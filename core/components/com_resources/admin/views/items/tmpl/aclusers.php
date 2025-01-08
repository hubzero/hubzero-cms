<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->js('acl.js');

$acluserIDs = array();
?>


<div class="grid">
	<div class="col span9">
		<label for="acluserid">User ID, name, or username:</label>
		<input type="text" name="acluserid" id="acluserid" value="" />
	</div>
	<div class="col span3">
		<input type="button" name="addacluser" id="addacluser" onclick="HUB.ResourcesACL.addUser();" value="Add" />
	</div>
</div>













<ul id="acluser-list">
	<?php
	if ($this->aclusernames != null)
	{
		foreach ($this->aclusernames as $aclusername)
		{
			if ($aclusername->name)
			{
				$name = $aclusername->name;
			}
			else
			{
				$name = $aclusername->givenName . ' ';
				if ($aclusername->middleName != null)
				{
					$name .= $aclusername->middleName . ' ';
				}
				$name .= $aclusername->surname;
			}

			$acluserIDs[] = $aclusername->user_id;


	?>
	<li id="acluser_<?php echo $aclusername->user_id; ?>">
		<a class="state trash" data-parent="acluser_<?php echo $aclusername->user_id; ?>" href="#" onclick="HUB.ResourcesACL.removeUser('acluser_<?php echo $aclusername->user_id; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
		<?php echo $this->escape(stripslashes($name)); ?> (<?php echo $aclusername->user_id; ?>)

















		<input type="hidden" name="<?php echo $aclusername->user_id; ?>_name" value="<?php echo $this->escape($name); ?>" />
	</li>
		<?php
		}
	}
	?>
</ul>
<input type="hidden" name="old_aclusers" id="old_aclusers" value="<?php echo implode(',', $acluserIDs); ?>" />
<input type="hidden" name="new_aclusers" id="new_aclusers" value="<?php echo implode(',', $acluserIDs); ?>" />
