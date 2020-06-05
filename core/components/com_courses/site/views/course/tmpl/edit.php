<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('course.css')
     ->js('courses.overview.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="prev btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>" title="<?php echo Lang::txt('JCANCEL'); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_COURSES_NEW_EXPLANATION'); ?></p>
		</div>
		<fieldset id="top_box">
			<legend><?php echo Lang::txt('COM_COURSES_NEW_CREATE_ENTRY'); ?></legend>

			<?php if ($this->task != 'new'): ?>
				<input name="alias" type="hidden" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<?php else: ?>
				<div class="form-group">
					<label class="course_alias_label" for="course_alias_field">
						<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<input name="course[alias]" id="course_alias_field" type="text" size="35" class="form-control" value="<?php echo $this->escape($this->course->get('alias')); ?>" autocomplete="off" data-route="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=courseavailability&no_html=1'); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
					</label>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<label for="field-title">
					<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input type="text" name="course[title]" id="field-title" size="35" class="form-control" value="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
				</label>
			</div>

			<div class="form-group">
				<label for="field-blurb">
					<?php echo Lang::txt('COM_COURSES_FIELD_BLURB'); ?>
					<textarea name="course[blurb]" id="field-blurb" class="form-control" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?></textarea>
					<span class="hint">
						<?php echo Lang::txt('COM_COURSES_FIELD_BLURB_HINT'); ?>
					</span>
				</label>
			</div>

			<div class="form-group">
				<label for="actags">
					<?php echo Lang::txt('COM_COURSES_FIELD_TAGS'); ?>

					<?php
					$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->course->tags('string'))));
					if (count($tf) > 0) {
						echo implode("\n", $tf);
					} else { ?>
						<input type="text" name="tags" id="actags" class="form-control" value="<?php echo $this->escape($this->couse->tags('string')); ?>" />
					<?php } ?>

					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
				</label>
			</div>

			<div class="form-group form-check">
				<label for="params-allow_forks" class="form-check-label">
					<input type="checkbox" class="option form-check-input" name="params[allow_forks]" id="params-allow_forks" checked="checked" value="1" />
					<?php echo Lang::txt('COM_COURSES_ALLOW_FORKS'); ?>
				</label>
				<span class="hint"><?php echo Lang::txt('COM_COURSES_ALLOW_FORKS_HINT'); ?></span>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="course[state]" value="<?php echo $this->course->get('state'); ?>" />
		<input type="hidden" name="course[id]" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
		</p>
	</form>
</section><!-- / .section -->
