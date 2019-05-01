<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<ul class="team-listing cf <?php echo (isset($this->cls)) ? $this->cls : ''; ?>">
	<?php foreach ($this->members as $member) : ?>
		<?php 
			$profile = $member->getProfile();
			$me      = ($profile->get('uidNumber') == User::get('id')) ? true : false;
		?>
		<li <?php echo ($me) ? 'class="me"' : ''; ?>>
			<a href="<?php echo $profile->link(); ?>" class="tooltips" title="<?php echo $profile->get('name'); ?> <?php echo ($me) ? '(You)' : ''; ?>">
				<img src="<?php echo $profile->picture(0, true); ?>" alt="" />
				<span><?php echo $profile->get('name'); ?></span>
			</a>
			<?php if (!$me) : ?>
				<a class="btn btn-danger btn-secondary remove confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE_CONFIRM'); ?>" href="<?php echo Route::url($member->link('remove')); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE'); ?>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>