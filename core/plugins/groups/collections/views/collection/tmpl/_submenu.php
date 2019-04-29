<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>
	<nav>
		<ul class="sub-menu">
			<li<?php if ($this->active == 'collections') { echo ' class="active"'; } ?>>
				<a class="collections count" href="<?php echo Route::url($base . '&scope=all'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_COLLECTIONS', $this->collections); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'posts') { echo ' class="active"'; } ?>>
				<a class="posts count" href="<?php echo Route::url($base . '&scope=posts'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_POSTS', $this->posts); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'followers') { echo ' class="active"'; } ?>>
				<a class="followers count" href="<?php echo Route::url($base . '&scope=followers'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWERS', $this->followers); ?></span>
				</a>
			</li>
			<?php if ($this->params->get('access-can-follow')) { ?>
				<li<?php if ($this->active == 'following') { echo ' class="active"'; } ?>>
					<a class="following count" href="<?php echo Route::url($base . '&scope=following'); ?>">
						<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWING', '<strong>' . $this->following . '</strong>'); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
	</nav>