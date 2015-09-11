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

$this->css()
     ->js('jquery.fileuploader', 'system')
     ->js();

$this->view('default_menu')
     ->set('option', $this->option)
     ->set('controller', $this->controller)
     ->set('course', $this->course)
     ->set('offering', $this->offering)
     ->set('page', $this->model)
     ->set('pages', $this->pages)
     ->display();

$base = $this->offering->link() . '&active=pages';
?>

<div class="pages-wrap">
	<div class="pages-content">

		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo Route::url($base); ?>" method="post" id="pageform" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo ($this->model->exists()) ? Lang::txt('PLG_COURSES_PAGES_EDIT_PAGE') : Lang::txt('PLG_COURSES_PAGES_NEW_PAGE'); ?></legend>

				<div class="grid">
					<div class="col span-half">
						<label for="field-title">
							<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_PAGES_REQUIRED'); ?></span>
							<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
							<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE_HINT'); ?></span>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-url">
							<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS'); ?> <span class="optional"><?php echo Lang::txt('PLG_COURSES_PAGES_OPTINAL'); ?></span>
							<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->model->get('url'))); ?>" />
							<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS_HINT'); ?></span>
						</label>
					</div>
				</div>

				<?php if ($this->offering->access('manage')) { ?>
					<label for="field-section_id">
						<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_SECTION'); ?>
						<select name="fields[section_id]" id="field-section_id">
							<option value="0"<?php if ($this->model->get('section_id') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_OPT_ALL_SECTIONS'); ?></option>
						<?php foreach ($this->offering->sections() as $section) { ?>
							<option value="<?php echo $section->get('id'); ?>"<?php if ($section->get('id') == $this->model->get('section_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
						<?php } ?>
						</select>
					</label>
				<?php } ?>

				<label for="fields_content">
					<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_PAGES_REQUIRED'); ?></span>
					<?php
						echo $this->editor('fields[content]', $this->escape($this->model->content('raw')), 35, 50, 'field_content');
					?>
				</label>

				<div class="field-wrap">
					<div class="grid file-manager">
						<div class="col span-half">
							<div id="file-uploader" data-instructions="<?php echo Lang::txt('PLG_COURSES_PAGES_UPLOAD_INSTRUCTIONS'); ?>" data-action="<?php echo Route::url($base . '&action=upload&no_html=1&section_id='); ?>" data-section="<?php echo $this->model->get('section_id'); ?>" data-list="<?php echo Route::url($base . '&action=list&no_html=1&section_id='); ?>">
								<iframe width="100%" height="370" name="filer" id="filer" src="<?php echo Route::url($base . '&action=list&tmpl=component&page=' . $this->model->get('id') . '&section_id=' . $this->model->get('section_id')); ?>"></iframe>
							</div>
						</div>
						<div class="col span-half omega">
							<div id="file-uploader-list"></div>
						</div>
					</div>
				</div>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_COURSES_PAGES_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
						<?php echo Lang::txt('PLG_COURSES_PAGES_CANCEL'); ?>
					</a>
				</p>
			</fieldset>

			<input type="hidden" name="fields[active]" value="<?php echo $this->model->get('active', 1); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<?php if ($this->offering->access('manage', 'section') && !$this->offering->access('manage')) { ?>
				<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />
			<?php } ?>
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />

			<?php echo Html::input('token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		</form>

		<div class="clear"></div>
	</div>
</div>