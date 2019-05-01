<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$name = $this->escape(stripslashes($this->instructor->get('name')));
?>
<div class="course-instructor">
	<p class="course-instructor-photo">
		<?php if ($this->instructor->get('public')) { ?>
			<a href="<?php echo Route::url($this->instructor->link()); ?>">
				<img src="<?php echo $this->instructor->picture(); ?>" alt="<?php echo $name; ?>" />
			</a>
		<?php } else { ?>
			<img src="<?php echo $this->instructor->picture(); ?>" alt="<?php echo $name; ?>" />
		<?php } ?>
	</p>

	<div class="course-instructor-content cf">
		<h4>
			<?php if (in_array($this->instructor->get('access'), User::getAuthorisedViewLevels())) { ?>
				<a href="<?php echo Route::url($this->instructor->link()); ?>">
					<?php echo $name; ?>
				</a>
			<?php } else { ?>
				<?php echo $name; ?>
			<?php } ?>
		</h4>
		<p class="course-instructor-org">
			<?php echo $this->escape(stripslashes($this->instructor->get('organization', '--'))); ?>
		</p>
	</div><!-- / .course-instructor-content cf -->

	<?php
	if (in_array($this->instructor->get('access'), User::getAuthorisedViewLevels())) {
		$bio = $this->instructor->get('bio');
		if (!$bio)
		{
			$bio = $this->instructor->get('biography');
		}
	?>
	<div class="course-instructor-bio">
		<?php if ($bio) { ?>
			<?php echo $bio; ?>
		<?php } else { ?>
			<em><?php echo Lang::txt('COM_COURSES_INSTRUCTOR_NO_BIO'); ?></em>
		<?php } ?>
	</div>
	<?php } ?>
</div><!-- / .course-instructor -->