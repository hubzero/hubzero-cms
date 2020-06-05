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
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>

	<?php if ($logo = $this->course->logo('url')) { ?>
		<p class="course-identity">
			<img src="<?php echo $logo; ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
		</p>
	<?php } ?>

	<p id="page_identity">
		<a class="prev" href="<?php echo Route::url($this->course->link()); ?>">
			<?php echo Lang::txt('COM_COURSES_COURSE_OVERVIEW'); ?>
		</a>
		<strong>
			<?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
		</span>
		<strong>
			<?php echo Lang::txt('COM_COURSES_SECTION'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
		</span>
	</p>
</header>

<section class="main section">
	<div class="section-inner">
		<p><?php echo Lang::txt('COM_COURSES_ENROLLMENT_ACHIEVED'); ?></p>
		<p>
			<a class="icon-browse btn" href="<?php echo Route::url($this->course->link()); ?>">
				<?php echo Lang::txt('COM_COURSES_COURSE_OVERVIEW'); ?>
			</a>
		</p>
	</div>
</section>