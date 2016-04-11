<?php
/**
 * Header
 *
 * Template used for Special Groups. Will now be auto-created
 * when admin switches group from type HUB to type Special.
 *
 * @author     HUBzero
 * @copyright  December 2015
 */
?>

<div class="super-group-header-wrap">
	<div class="super-group-header cf">
		<h1>
			<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>" title="<?php echo $this->group->get('description'); ?> Home">
				<?php echo $this->group->get('description'); ?>
				<span>[<?php echo $this->group->get('cn'); ?>]</span>
			</a>
		</h1>
	</div>
</div>

<div class="super-group-menu-wrap">
	<div class="super-group-menu">
		<!-- ###  Start Menu Include  ### -->
			<group:include type="menu" />
		<!-- ###  End Menu Include  ### -->
	</div>
</div>