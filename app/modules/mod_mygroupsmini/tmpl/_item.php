<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<li class="group-mini<?php if ($group->published == 2) { echo ' archived'; } ?>">
   <div class="grpdisp">
   <?php
	 $group1 = Hubzero\User\Group::getInstance($group->gidNumber);
	 $path = PATH_APP . '/site/groups/' . $group1->get('gidNumber') . '/uploads/' . $group1->get('logo');
   ?>
	 <?php if ($group1->get('logo') && is_file($path)) { ?>
	 <?php echo '  <a class="group-img" href="' . Route::url('index.php?option=com_groups&cn='. $group1->get('cn')) . '">'; ?>
	 <?php echo '      <img src="' . with(new Hubzero\Content\Moderator($path))->getUrl() . '" alt="' . $this->escape(stripslashes($group1->get('description'))) . '" />'; ?>
	 <?php echo '  </a>'; ?>
   <?php }
   else { ?>
   <?php echo '  <a class="group-img" href="' . Route::url('index.php?option=com_groups&cn='. $group1->get('cn')) . '">'; ?>
   <?php echo '  <img src="/core/components/com_groups/site/assets/img/group_default_logo.png"/>'; ?>
   <?php echo '  </a>'; ?>
   <?php } ?>
   <div class="group-name"><a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn); ?>"><?php echo $this->escape(stripslashes($group->description));?></a></div>
   </div>

  <?php if (!$group->approved) : ?>
		<span class="status pending-approval"><?php echo Lang::txt('MOD_MYGROUPSMINI_GROUP_STATUS_PENDING'); ?></span>
	<?php endif; ?>
	<?php if ($group->published == 2) : ?>
		<span class="status archived"><?php echo Lang::txt('MOD_MYGROUPSMINI_GROUP_STATUS_ARCHIVED'); ?></span>
	<?php endif; ?>
	<?php if ($group->regconfirmed && !$group->registered) : ?>
		<span class="actions">
			<a class="action-accept" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=accept'); ?>">
				<?php echo Lang::txt('MOD_MYGROUPSMINI_ACTION_ACCEPT'); ?>
			</a>
			<a class="action-cancel" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=cancel'); ?>">
				<?php echo Lang::txt('MOD_MYGROUPSMINI_ACTION_DECLINE'); ?>
			</a>
		</span>
	<?php endif; ?>
</li>
