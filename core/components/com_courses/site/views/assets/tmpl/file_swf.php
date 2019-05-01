<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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

<object type="application/x-shockwave-flash" width="100%" height="100%">
	<param name="movie" value="<?php echo Route::url($this->model->path($this->course->get('id'))); ?>"></param>
	<param name="wmode" value="opaque"></param>
</object>