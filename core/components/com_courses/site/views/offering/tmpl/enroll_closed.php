<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('offering');
?>
<header id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
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
</header><!-- #content-header -->

<section class="main section enroll-closed">
	<div class="section-inner">
		<div id="offering-introduction">
			<div class="instructions">
				<p class="warning"><?php echo Lang::txt('COM_COURSES_ENROLLMENT_CLOSED'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo Lang::txt('COM_COURSES_I_SHOULD_HAVE_ACCESS'); ?></strong></p>
				<p><?php echo Lang::txt('COM_COURSES_I_SHOULD_HAVE_ACCESS_EXPLANATION', Route::url('index.php?option=com_support')); ?></p>
				<p><strong><?php echo Lang::txt('COM_COURSES_WHERE_CAN_I_FIND_OTHER_COURSES'); ?></strong></p>
				<p><?php echo Lang::txt('COM_COURSES_WHERE_CAN_I_FIND_OTHER_COURSES_EXPLANATIONS', Route::url('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
			</div><!-- / .questions -->
		</div><!-- / #offering-introduction -->
	</div>
</section><!-- /.main section -->
