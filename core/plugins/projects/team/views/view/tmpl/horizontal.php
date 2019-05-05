<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_TEAM'); ?></h3>
</div>
<div id="team-horiz" class="public-list-wrap">
	<?php if (count($this->team) > 0) { ?>
		<ul>
			<?php foreach ($this->team as $owner)
			{
				if (!$owner->userid || $owner->status != 1)
				{
					continue;
				}
				// Get profile thumb image
				$profile = User::getInstance($owner->userid);
				//$actor   = User::getInstance(User::get('id'));
				//$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);
			?>
			<li>
				<img width="50" height="50" src="<?php echo $profile->picture(); ?>" alt="<?php echo $this->escape($owner->fullname); ?>" />
				<span class="block"><a href="<?php echo Route::url('index.php?option=com_members&id=' . $owner->userid); ?>"><?php echo $this->escape($owner->fullname); ?></a></span>
			</li>
			<?php } ?>
			<li class="clear">&nbsp;</li>
		</ul>
	<?php } else { ?>
		<div class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TEAM_EXTERNAL_NO_TEAM'); ?></div>
	<?php } ?>
	<div class="clear"></div>
</div>
