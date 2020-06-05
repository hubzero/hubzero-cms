<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

switch ($this->count)
{
	case 0:
		$cls = 'span4';
		break;
	case 1:
		$cls = 'span4';
		break;
	case 2:
		$cls = 'span4 omega';
		break;
}
?>
<div class="col <?php echo $cls; ?>">
	<div class="course">
		<a href="<?php echo Route::url($this->course->link()); ?>">
			<div class="course-details">
				<div class="course-identity">
					<?php if ($logo = $this->course->logo('url')) { ?>
						<img src="<?php echo Route::url($logo); ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
					<?php } else { ?>
						<span></span>
					<?php } ?>

					<?php if ($this->course->get('rating', 0) > 4) { ?>
					<div>
						<strong><?php echo Lang::txt('COM_COURSES_TOP_RATED_COURSE'); ?></strong> <span class="rating">&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;</span>
					</div>
					<?php } else if ($this->course->get('popularity', 0) > 7) { ?>
					<div>
						<strong><?php echo Lang::txt('COM_COURSES_POPULAR_COURSE'); ?></strong> <span class="popularity">&#xf091;</span>
					</div>
					<?php } ?>
				</div>
				<h3 class="course-title">
					<?php echo $this->escape($this->course->get('title')); ?>
				</h3>
			<?php if ($this->course->get('blurb')) { ?>
				<p class="course-description">
					<?php echo \Hubzero\Utility\Str::truncate($this->escape($this->course->get('blurb')), 130); ?>
				</p>
			<?php } ?>
			</div>
		</a>
	</div>
</div>