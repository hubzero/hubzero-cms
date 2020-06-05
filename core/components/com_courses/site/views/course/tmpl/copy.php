<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('course.css')
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COURSES_COPY_COURSE'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="prev btn" href="<?php echo ($this->return) ? base64_decode($this->return) : Route::url('index.php?option=' . $this->option); ?>" title="<?php echo Lang::txt('JCANCEL'); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_COURSES_COPY_EXPLANATION'); ?></p>
		</div>
		<fieldset id="top_box">
			<legend><?php echo Lang::txt('COM_COURSES_COPY_ENTRY'); ?></legend>

			<div class="form-group">
				<label class="course_alias_label" for="course_alias_field">
					<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?></span>
					<input name="fields[alias]" id="course_alias_field" type="text" size="35" value="<?php echo $this->escape($this->course->get('alias') . '_copy'); ?>" autocomplete="off" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</label>
			</div>

			<div class="form-group">
				<label for="field-title">
					<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?></span>
					<input type="text" name="fields[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->course->get('title')) . ' Copy'); ?>" />
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="id" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="docopy" />
		<input type="hidden" name="return" value="<?php echo $this->escape($this->return); ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_COURSES_COPY'); ?>" />
		</p>
	</form>
</section><!-- / .section -->
