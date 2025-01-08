<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

if ($this->group):

	$logo = $this->group->getLogo();
?>

<div id="group-owner" class="container">
	<h4>Owned by</h4>
	<div class="group-content">
	<?php if ($logo) { ?>
		<p class="group-img">
			<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>">
				<img src="<?php echo $logo; ?>" width="50" alt="<?php echo Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $this->escape(stripslashes($this->group->get('description')))); ?>" />
			</a>
		</p>
		<p class="group-description group-withlogo"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } else { ?>
		<p class="group-description"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } ?>
	</div>
	<a class="owner" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>"><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></a>
</div>
<?php endif; ?>

<?php if ($this->aclgroups): ?>
	<div id="group-shared" class="container">
		
		<h4>Shared with</h4>
<?php foreach($this->aclgroups as $group):

	$logo = $group->getLogo();
?>
	<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->get('cn')); ?>" class="shared-with-group">
		<div class="inner">
			<?php if ($logo): ?>
			<div class="img"><img src="<?php echo $logo; ?>" alt="<?php echo Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $this->escape(stripslashes($group->get('description')))); ?>" /></div>
			<?php endif; ?>
			<p class="group-description"><?php echo $this->escape(stripslashes($group->get('description'))); ?></p>
		</div>
	</a>

<?php endforeach; ?>
</div>
<?php endif; ?>

