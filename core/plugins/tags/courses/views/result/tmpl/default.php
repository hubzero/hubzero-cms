<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<li class="course-entry">
	<p class="title">
		<a href="<?php echo Route::url($this->course->link()); ?>"><?php echo $this->escape(stripslashes($this->course->get('title'))); ?></a>
	</p>
	<p class="details">
		<strong class="type">Courses</strong><span>|</span>'
		<?php echo Date::of($this->course->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
	</p>
	<?php if ($content = \Hubzero\Utility\Str::truncate(strip_tags($this->course->get('blurb')), 200)) { ?>
		<p><?php echo $content; ?></p>
	<?php } ?>
	<p class="href">
		<?php echo rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->course->link()), '/'); ?>
	</p>
</li>
