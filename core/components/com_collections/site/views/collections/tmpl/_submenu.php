<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option;
?>
	<nav>
		<ul class="sub-menu">
			<li<?php if ($this->active == 'collections') { echo ' class="active"'; } ?>>
				<a class="collections count active" href="<?php echo Route::url($base . '&task=all'); ?>">
					<span><?php echo Lang::txt('COM_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->collections); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'posts') { echo ' class="active"'; } ?>>
				<a class="posts count" href="<?php echo Route::url($base . '&task=posts'); ?>">
					<span><?php echo Lang::txt('COM_COLLECTIONS_HEADER_NUM_POSTS', $this->posts); ?></span>
				</a>
			</li>
		</ul>
	</nav>