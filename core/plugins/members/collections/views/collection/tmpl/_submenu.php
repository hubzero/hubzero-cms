<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;
?>
	<nav>
		<ul class="sub-menu">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<li<?php if ($this->active == 'livefeed') { echo ' class="active"'; } ?>>
					<a class="livefeed tooltips" href="<?php echo Route::url($base); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_TITLE'); ?>">
						<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED'); ?></span>
					</a>
				</li>
			<?php } ?>
			<li<?php if ($this->active == 'collections') { echo ' class="active"'; } ?>>
				<a class="collections count" href="<?php echo Route::url($base . '&task=all'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->collections); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'posts') { echo ' class="active"'; } ?>>
				<a class="posts count" href="<?php echo Route::url($base . '&task=posts'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_POSTS', $this->posts); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'followers') { echo ' class="active"'; } ?>>
				<a class="followers count" href="<?php echo Route::url($base . '&task=followers'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWERS', $this->followers); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'following') { echo ' class="active"'; } ?>>
				<a class="following count" href="<?php echo Route::url($base . '&task=following'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWNG', $this->following); ?></span>
				</a>
			</li>
		</ul>
	</nav>