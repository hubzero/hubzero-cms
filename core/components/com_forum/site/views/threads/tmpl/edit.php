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

$this->css();

$this->category->set('section_alias', $this->section->get('alias'));
$this->post->set('section', $this->section->get('alias'));
$this->post->set('category', $this->category->get('alias'));

if ($this->post->get('id'))
{
	$action = $this->post->link('edit');
}
else
{
	$this->post->set('access', 0);
	$action = $this->post->link('new');
}
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-comments comments btn" href="<?php echo Route::url($this->category->link()); ?>">
				<?php echo Lang::txt('COM_FORUM_ALL_DISCUSSIONS'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="subject">
		<h3>
			<?php if ($this->post->get('id')) { ?>
				<?php echo Lang::txt('COM_FORUM_EDIT_DISCUSSION'); ?>
			<?php } else { ?>
				<?php echo Lang::txt('COM_FORUM_NEW_DISCUSSION'); ?>
			<?php } ?>
		</h3>
		<form action="<?php echo Route::url($action); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<img src="<?php echo $this->post->creator->picture(); ?>" alt="" />
			</p>

			<fieldset>
			<?php if ($this->config->get('access-manage-thread') && !$this->post->get('parent')) { ?>
				<div class="grid">
					<div class="col span-half">
						<label for="field-sticky">
							<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->post->get('sticky')) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_FORUM_FIELD_STICKY'); ?>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-closed">
							<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->post->get('closed')) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_FORUM_FIELD_CLOSED_THREAD'); ?>
						</label>
					</div>
				</div>
			<?php } else { ?>
				<input type="hidden" name="fields[sticky]" id="field-sticky" value="<?php echo $this->post->get('sticky'); ?>" />
				<input type="hidden" name="fields[closed]" id="field-closed" value="<?php echo $this->post->get('closed'); ?>" />
			<?php } ?>

			<?php if (!$this->post->get('parent')) { ?>
				<label for="field-access">
					<?php echo Lang::txt('COM_FORUM_FIELD_READ_ACCESS'); ?>
					<select name="fields[access]" id="field-access">
						<option value="1"<?php if ($this->post->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'); ?></option>
						<option value="2"<?php if ($this->post->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'); ?></option>
					</select>
				</label>

				<label for="field-category_id">
					<?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY'); ?> <span class="required"><?php echo Lang::txt('COM_FORUM_REQUIRED'); ?></span>
					<select name="fields[category_id]" id="field-category_id">
						<?php
						$filters = array(
							'state'  => 1,
							'access' => User::getAuthorisedViewLevels()
						);
						foreach ($this->forum->sections($filters)->rows() as $section)
						{
							$categories = $section->categories()
								->whereEquals('state', $filters['state'])
								->whereIn('access', $filters['access'])
								->rows();
							if ($categories->count() > 0) { ?>
								<optgroup label="<?php echo $this->escape(stripslashes($section->get('title'))); ?>">
									<?php foreach ($categories as $category) { ?>
										<option value="<?php echo $category->get('id'); ?>"<?php if ($this->category->get('alias') == $category->get('alias')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->get('title'))); ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						<?php } ?>
					</select>
				</label>

				<label for="field-title">
					<?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->post->get('title'))); ?>" />
				</label>
			<?php } else { ?>
				<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
				<input type="hidden" name="fields[access]" id="field-access" value="<?php echo $this->post->get('access', 0); ?>" />
			<?php } ?>

				<label for="fieldcomment">
					<?php echo Lang::txt('COM_FORUM_FIELD_COMMENTS'); ?> <span class="required"><?php echo Lang::txt('COM_FORUM_REQUIRED'); ?></span>
					<?php
					echo $this->editor('fields[comment]', $this->escape(stripslashes($this->post->get('comment'))), 35, 15, 'fieldcomment', array('class' => 'minimal no-footer'));
					?>
				</label>

				<label>
					<?php echo Lang::txt('COM_FORUM_FIELD_TAGS'); ?>:
					<?php
						echo $this->autocompleter('tags', 'tags', $this->escape($this->post->tags('string')), 'actags');
					?>
				</label>

				<fieldset>
					<legend><?php echo Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>

					<?php $attachment = $this->post->attachments()->row(); ?>

					<div class="grid">
						<div class="col span-half">
							<label for="upload">
								<?php echo Lang::txt('COM_FORUM_FIELD_FILE'); ?> <?php if ($attachment->get('filename')) { echo '<strong>' . $this->escape(stripslashes($attachment->get('filename'))) . '</strong>'; } ?>
								<input type="file" name="upload" id="upload" />
							</label>
						</div>
						<div class="col span-half omega">
							<label for="field-attach-descritpion">
								<?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?>
								<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($attachment->get('description'))); ?>" />
							</label>
						</div>
						<input type="hidden" name="attachment" value="<?php echo $this->escape($attachment->get('id')); ?>" />
					</div>
					<?php if ($attachment->get('id')) { ?>
						<p class="warning">
							<?php echo Lang::txt('COM_FORUM_FIELD_FILE_WARNING'); ?>
						</p>
					<?php } ?>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->post->get('anonymous')) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
				</p>

				<div class="sidenote">
					<p>
						<strong><?php echo Lang::txt('COM_FORUM_KEEP_POLITE'); ?></strong>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('parent'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[thread]" value="<?php echo $this->post->get('thread'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[scope]" value="site" />
			<input type="hidden" name="fields[scope_id]" value="0" />
			<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->post->get('scope_sub_id'); ?>" />
			<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="threads" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="section" value="<?php echo $this->escape($this->section->get('alias')); ?>" />

			<?php echo Html::input('token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<p><strong><?php echo Lang::txt('COM_FORUM_WHAT_IS_STICKY'); ?></strong><br />
			<?php echo Lang::txt('COM_FORUM_STICKY_EXPLANATION'); ?></p>

			<p><strong><?php echo Lang::txt('COM_FORUM_WHAT_IS_LOCKING'); ?></strong><br />
			<?php echo Lang::txt('COM_FORUM_LOCKING_EXPLANATION'); ?></p>
		</div>
	</aside><!-- /.aside -->
</section><!-- / .below section -->