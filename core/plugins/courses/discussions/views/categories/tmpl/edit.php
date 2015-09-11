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

defined('_HZEXEC_') or die();

?>
<section class="main section">
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<h3 class="post-comment-title">
		<?php if ($this->model->id) { ?>
			<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT_CATEGORY'); ?>
		<?php } else { ?>
			<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW_CATEGORY'); ?>
		<?php } ?>
		</h3>

		<form action="<?php echo Route::url($this->offering->link() . '&active=discussions'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
				<?php
				$jxuser = new \Hubzero\User\Profile();
				$jxuser->load(User::get('id'));
				?>
				<img src="<?php echo $jxuser->getPicture(); ?>" alt="" />
			</p>

			<fieldset>
				<label for="field-section_id">
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_SECTION'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REQUIRED'); ?></span>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_SECTION_SELECT'); ?></option>
					<?php foreach ($this->sections as $section) { ?>
						<option value="<?php echo $section->id; ?>"<?php if ($this->model->section_id == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
					<?php } ?>
					</select>
				</label>

				<label for="field-title">
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REQUIRED'); ?></span>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->title)); ?>" />
				</label>

				<label for="field-description">
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->model->description)); ?></textarea>
				</label>

				<label for="field-closed" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->model->closed) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CLOSED'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
				</p>
			</fieldset>
			<input type="hidden" name="fields[alias]" value="<?php echo $this->model->alias; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
			<input type="hidden" name="active" value="discussions" />
			<input type="hidden" name="unit" value="manage" />
			<input type="hidden" name="action" value="savecategory" />

			<?php echo Html::input('token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_CATEGORY_HINT'); ?></p>
	</aside><!-- /.aside -->
</section><!-- / .main section -->