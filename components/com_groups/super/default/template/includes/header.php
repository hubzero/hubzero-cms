<?php
/**
 * Header
 *
 * Template used for Special Groups. Will now be auto-created
 * when admin switches group from type HUB to type Special.
 *
 * @author 		Christopher Smoak
 * @copyright	December 2012
 */
?>

<div class="super-group-header-wrap">
	<div class="super-group-header cf">
		<h1>
			<a href="/groups/<?php echo $this->group->get('cn'); ?>" title="<?php echo $this->group->get('description'); ?> Home">
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