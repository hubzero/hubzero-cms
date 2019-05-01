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

<section class="main section enroll-restricted">
	<div class="section-inner">
		<?php
			foreach ($this->notifications as $notification)
			{
				echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
			}
		?>

		<form action="<?php echo Route::url($this->course->offering()->link() . '&task=enroll'); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h3><?php echo Lang::txt('COM_COURSES_CODE_NOT_WORKING'); ?></h3>
				<p><?php echo Lang::txt('COM_COURSES_CODE_NOT_WORKING_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_COURSES_REDEEM_COUPON_CODE'); ?></legend>

				<p class="warning"><?php echo Lang::txt('COM_COURSES_ENROLLMENT_RESTRICTED'); ?></p>

				<label for="field-code">
					<?php echo Lang::txt('COM_COURSES_FIELD_COUPON_CODE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<input type="text" name="code" id="field-code" size="35" value="" />
				</label>
			</fieldset>
			<div class="clear"></div>

			<input type="hidden" name="offering" value="<?php echo $this->escape($this->course->offering()->get('alias') . ':' . $this->course->offering()->section()->get('alias')); ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="enroll" />

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_COURSES_REDEEM'); ?>" />
			</p>
		</form>
	</div>
</section><!-- /.main section -->