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

$this->css('jquery.datepicker.css', 'system')
     ->css('jquery.timepicker.css', 'system')
     ->css()
     ->js('jquery.timepicker.js', 'system')
     ->js();
?>
<section class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo Route::url($this->offering->link() . '&active=announcements'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend>
				<?php if ($this->model->get('id')) { ?>
					<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
				<?php } ?>
			</legend>

			<label for="field_content">
				<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_REQUIRED'); ?></span>
				<?php echo $this->editor('fields[content]', $this->escape($this->model->content('raw')), 35, 5, 'field_content', array('class' => 'minimal no-footer')); ?>
			</label>

			<fieldset>
				<legend><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_PUBLISH_WINDOW'); ?></legend>
				<p><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_HINT'); ?></p>
				<div class="grid">
					<div class="col span-half">
						<label for="field-publish_up" id="priority-publish_up">
							<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_START'); ?>
							<input class="datepicker" type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo ($this->model->get('publish_up') && $this->model->get('publish_up') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->model->get('publish_up'))->toSql()) : ''); ?>" />
							<span class="hint"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-publish_down" id="priority-publish_down">
							<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_END'); ?>
							<input class="datepicker" type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo ($this->model->get('publish_down') && $this->model->get('publish_down') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->model->get('publish_down'))->toSql()) : ''); ?>" />
							<span class="hint"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
				</div>
			</fieldset>

			<label for="field-priority" id="priority-label">
				<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1"<?php if ($this->model->get('priority')) { echo ' checked="checked"'; } ?> />
				<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_PRIORITY'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SUBMIT'); ?>" />
			<a class="btn btn-secondary" href="<?php echo Route::url($this->offering->link() . '&active=announcements'); ?>">
				<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CANCEL'); ?>
			</a>
		</p>

		<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="fields[state]" value="1" />
		<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
		<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="action" value="save" />

		<?php echo Html::input('token'); ?>
	</form>
</section><!-- / .main section -->