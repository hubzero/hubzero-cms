<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<header id="content-header">
	<h2><?php echo $this->asset->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo Route::url($this->course->offering()->link() . '&active=outline'); ?>">
				<?php echo Lang::txt('Back to course'); ?>
			</a>
		</p>
	</div>
</header>