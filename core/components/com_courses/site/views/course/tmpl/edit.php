<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('course.css')
     ->js();
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
				<label class="course_alias_label" for="course_alias_field">
					<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input name="course[alias]" id="course_alias_field" type="text" size="35" value="<?php echo $this->escape($this->course->get('alias')); ?>" autocomplete="off" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</label>
			<?php endif; ?>

			<label for="field-title">
				<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<input type="text" name="course[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
			</label>

			<label for="field-blurb">
				<?php echo Lang::txt('COM_COURSES_FIELD_BLURB'); ?>
				<textarea name="course[blurb]" id="field-blurb" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?></textarea>
				<span class="hint">
					<?php echo Lang::txt('COM_COURSES_FIELD_BLURB_HINT'); ?>
				</span>
			</label>

			<label for="actags">
				<?php echo Lang::txt('COM_COURSES_FIELD_TAGS'); ?>

				<?php
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->course->tags('string'))));
				if (count($tf) > 0) {
					echo implode("\n", $tf);
				} else { ?>
					<input type="text" name="tags" id="actags" value="<?php echo $this->escape($this->couse->tags('string')); ?>" />
				<?php } ?>

				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
			</label>
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
