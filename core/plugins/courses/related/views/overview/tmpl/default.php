<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div id="related-courses" class="after section">
	<h3>
		<?php echo (count($this->ids) > 1) ? Lang::txt('PLG_COURSES_RELATED_OTHER_BY_INSTRUCTORS') : Lang::txt('PLG_COURSES_RELATED_OTHER_BY_INSTRUCTOR'); ?>
	</h3>
	<?php
	$i = 0;
	$cls = '';
	foreach ($this->courses as $course)
	{
		$course = new \Components\Courses\Models\Course($course);
		$i++;
		if ($i == 3)
		{
			$cls = ' omega';
			$i = 0;
		}
		if ($i == 1)
		{
		?>
	<div class="grid">
		<?php
		}
		?>
		<div class="course-block col span-third<?php if ($cls) { echo $cls; } ?>">
			<a href="<?php echo Route::url($course->link()); ?>">
				<div class="course-details">
					<div class="course-identity">
						<?php if ($logo = $course->logo('url')) { ?>
							<img src="<?php echo Route::url($logo); ?>" alt="<?php echo Lang::txt('PLG_COURSES_RELATED_LOGO'); ?>" />
						<?php } else { ?>
							<span></span>
						<?php } ?>
						<?php if ($course->get('rating', 0) > 4) { ?>
							<div>
								<strong><?php echo Lang::txt('PLG_COURSES_RELATED_TOP_RATED'); ?></strong> <span class="rating">&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;</span>
							</div>
						<?php } else if ($this->course->get('popularity', 0) > 7) { ?>
							<div>
								<strong><?php echo Lang::txt('PLG_COURSES_RELATED_POPULAR'); ?></strong> <span class="popularity">&#xf091;</span>
							</div>
						<?php } ?>
					</div>
					<h4 class="course-title">
						<?php echo $this->escape(stripslashes($course->get('title'))); ?>
					</h4>
					<?php if ($course->get('blurb')) { ?>
						<p class="course-description">
							<?php echo \Hubzero\Utility\Str::truncate($this->escape(stripslashes($course->get('blurb'))), 130); ?>
						</p>
					<?php } ?>
				</div>
			</a>
		</div><!-- / .col -->
		<?php
		if ($i == 0 || $i == count($this->courses))
		{
		?>
	</div><!-- / .grid -->
		<?php
		}
	}
	?>
</div><!-- / #related-courses -->