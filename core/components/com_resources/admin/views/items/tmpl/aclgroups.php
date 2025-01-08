<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->js('acl.js');

$aclgroupIDs = array();
?>


<div class="grid">
	<div class="col span9">
		<label for="aclgroupid">Group ID or name:</label>
		<input type="text" name="aclgroupid" id="aclgroupid" value="" />
	</div>
	<div class="col span3">
		<input type="button" name="addaclgroup" id="addaclgroup" onclick="HUB.ResourcesACL.addGroup();" value="Add" />
	</div>
</div>













<ul id="aclgroup-list">
	<?php
	if ($this->aclgroupnames != null)
	{
		foreach ($this->aclgroupnames as $aclgroupname)
		{
			if ($aclgroupname->name)
			{
				$name = $aclgroupname->name;
			}

			$aclgroupIDs[] = $aclgroupname->group_id;


	?>
	<li id="aclgroup_<?php echo $aclgroupname->group_id; ?>">
		<a class="state trash" data-parent="aclgroup_<?php echo $aclgroupname->group_id; ?>" href="#" onclick="HUB.ResourcesACL.removeGroup('aclgroup_<?php echo $aclgroupname->group_id; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
		<?php echo $this->escape(stripslashes($name)); ?> (<?php echo $aclgroupname->group_id; ?>)

















		<input type="hidden" name="<?php echo $aclgroupname->group_id; ?>_name" value="<?php echo $this->escape($name); ?>" />
	</li>
		<?php
		}
	}
	?>
</ul>
<input type="hidden" name="old_aclgroups" id="old_aclgroups" value="<?php echo implode(',', $aclgroupIDs); ?>" />
<input type="hidden" name="new_aclgroups" id="new_aclgroups" value="<?php echo implode(',', $aclgroupIDs); ?>" />
